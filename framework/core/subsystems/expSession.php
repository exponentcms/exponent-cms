<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expSession class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expSession
 *
 * @subpackage Core-Subssytems
 * @package Framework
 */

class expSession {

	/**
	* Sets a variable in the session data, for use on subsequent page calls.
	*
	* Note that some session variables (like the user object and the ticket)
	* cannot be changed using this call (for security / sanity reason)
	*
	* @param string $var The name of the variable, for later reference
	* @param mixed $val The value to store
	* @node Subsystems:Sessions
	*/
    public static function set($var, $val) {
        $_SESSION[SYS_SESSION_KEY]['vars'][$var] = $val;
    }

	/** exdoc
	* This retrieves the value of a persistent session variable. Returns
	* null if the variable is not set in the session, or the value of the stored variable.
	*
	* Note that some session variables (like the user object and the ticket)
	* cannot be changed using this call (for security / sanity reason)
	*
	* @param string $var The name of the variable to retrieve.
	* @return mixed
	* @node Subsystems:Sessions
	*/
    public static function get($var) {
		if (isset($_SESSION[SYS_SESSION_KEY]['vars'][$var])) {
			return $_SESSION[SYS_SESSION_KEY]['vars'][$var];
		} else return null;
    }

    public static function exists($var) {
        return isset($_SESSION[SYS_SESSION_KEY]['vars'][$var]);
    }

    public static function deleteVar($var) {
        self::un_set($var);
    }
    
    public function setCache($params=array()) {
        $_SESSION[SYS_SESSION_KEY]['cache'][$params['module']] = $params['val'];
    }
    
    public static function setTableCache($tablename, $desc) {
        $_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename] = $desc;
    }
    
    public static function getTableCache($tablename) {
        if (isset($_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename])) {
            return $_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename];
        } else {
            return null;
        }
    }
    
    public static function issetTableCache($tablename) {
        return isset($_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename]) ? true : false;
    }
    
    public static function clearUserCache() {
        self::clearCurrentUserSessionCache();
    }

	/** exdoc
	 * Runs necessary code to initialize sessions for use.
	 * This sends the session cookie header (via the session_start
	 * PHP function) and sets up session variables needed by the
	 * rest of the system and this subsystem.
	 * @node Subsystems:Sessions
	 */
	public static function initialize() {

	// session key may be overridden
	if (!defined('SYS_SESSION_KEY')) {
		/** exdoc
		 * @state <b>UNDOCUMENTED</b>
		 * @node Undocumented
		 */
		define('SYS_SESSION_KEY',PATH_RELATIVE);
	}

	// Name of session cookie may also be overridden
	if (!defined('SYS_SESSION_COOKIE')) {
		define('SYS_SESSION_COOKIE','PHPSESSID');
	}

//	$sessid  = '';
		if (isset($_GET['expid']))
		{
			$sessid = $_GET['expid'];
		}
		else if (isset($_POST['expid']))
		{
			$sessid =  $_POST['expid'];
		}
		else if (!isset($_COOKIE[SYS_SESSION_COOKIE]))
		{
			$sessid = md5(uniqid(rand(), true));
		}
		else
		{
			$sessid = $_COOKIE[SYS_SESSION_COOKIE];
		}
		session_name(SYS_SESSION_COOKIE);
		session_id($sessid);
		$_COOKIE['PHPSESSID'] = $sessid;
		session_set_cookie_params(60*60*24*100); // This sets the cookie to expire in 100 days - ||seconds*minutes*hours*days||

		session_start();
		if (!isset($_SESSION[SYS_SESSION_KEY]))
		{
			$_SESSION[SYS_SESSION_KEY] = array();
		}
		if (!isset($_SESSION[SYS_SESSION_KEY]['vars']))
		{
			$_SESSION[SYS_SESSION_KEY]['vars'] = array();
		}
		if (isset($_SESSION[SYS_SESSION_KEY]['vars']['display_theme']))
		{
			define('DISPLAY_THEME',$_SESSION[SYS_SESSION_KEY]['vars']['display_theme']);
		}
		if (isset($_SESSION[SYS_SESSION_KEY]['vars']['theme_style']))
		{
			define('THEME_STYLE',$_SESSION[SYS_SESSION_KEY]['vars']['theme_style']);
		}
		if (isset($_SESSION[SYS_SESSION_KEY]['vars']['mobile']))
		{
			define('MOBILE',$_SESSION[SYS_SESSION_KEY]['vars']['mobile']);
		}
	}

	/** exdoc
	 * Validates the stored session ticket against the database.  This is used
	 * to force refreshes and force logouts.  It also updates activity time.
	 * @node Subsystems:Sessions
	 */
	public static function validate() {
		global $db, $user;
		//FJD - create a ticket for every session instead of just logged in users
		if (!isset($_SESSION[SYS_SESSION_KEY]['ticket'])) {
			$ticket = self::createTicket();
		}else{
			$ticket = $db->selectObject('sessionticket',"ticket='".$_SESSION[SYS_SESSION_KEY]['ticket']."'");
		}

		//if we don't have a ticket here, that means the browser passed the cookie, the session is still
		// active, but the DATABASE tickets table was cleared.

		if(SESSION_TIMEOUT_ENABLE == true){
			$timeoutval = SESSION_TIMEOUT;
			if ($timeoutval < 300) $timeoutval = 300;
			if ($ticket == null || $ticket->last_active < time() - $timeoutval) {
				define('SITE_403_HTML',SESSION_TIMEOUT_HTML);
				self::logout();
				flash('notice',gt('Your user session has expired.  Please log in again.'));
				return;
			}
		}

		if (isset($_SESSION[SYS_SESSION_KEY]['user'])) {
			$user = ($_SESSION[SYS_SESSION_KEY]['user']);
		} else {
			$user = new user();
			$user->id = 0;
		}

		if (isset($ticket->refresh) && $ticket->refresh == 1) {
			if (isset($user)) expPermissions::load($user);
			self::clearCurrentUserSessionCache();
			$ticket->refresh = 0;
		}

		self::updateTicket($ticket, $user);
		// Clean out old sessions from the sessionticket table.
		$db->delete('sessionticket','last_active < ' . (time() - SESSION_TIMEOUT));

		define('SITE_403_HTML', SITE_403_REAL_HTML);
	}

	/** exdoc
	 * Creates user ticket in sessionticket table and session
	 *
	 * @param $ticket
	 * @param User $user The user object of the newly logged-in user. Uses id of 0 if not supplied.
	 * @return
	 * @node Subsystems:Sessions
	 */
	public static function updateTicket($ticket, $user){
		global $db;
		if (isset($ticket->ticket)){
			$ticket->uid = isset($user->id) ? $user->id : 0;
			$ticket->last_active = time();
			$db->updateObject($ticket,'sessionticket',"ticket='" . $ticket->ticket . "'");
		}
		return $ticket;
	}

	/** exdoc
	 * Checks to see if the session holds a set variable of the given name.
	 *
	 * Note that some session variables (like the user object and the ticket)
	 * cannot be changed using this call (for security / sanity reason)
	 * @node Subsystems:Sessions
	 * @param $var
	 * @return bool
	 */
	public static function is_set($var) {
		return isset($_SESSION[SYS_SESSION_KEY]['vars'][$var]);
	}


	public static function getCacheValue($module){
		//returns array or null
		if (isset($_SESSION[SYS_SESSION_KEY]['cache'][$module])) return($_SESSION[SYS_SESSION_KEY]['cache'][$module]);
		else return null;
	}

	public static function getTicketString() {
		//if (isset($_SESSION[SYS_SESSION_KEY]['ticket'])) {
		//	return $_SESSION[SYS_SESSION_KEY]['ticket'];
		//} else return null;
		global $db;
		if (isset($_SESSION[SYS_SESSION_KEY]['ticket'])) {
			if($db->selectObject('sessionticket',"ticket='".$_SESSION[SYS_SESSION_KEY]['ticket']."'") != null ) {
				return $_SESSION[SYS_SESSION_KEY]['ticket'];
			} else return null;
		}
	}

	/** exdoc
	 * Removes a variable from the session.
	 *
	 * Note that some session variables (like the user object and the ticket)
	 * cannot be changed using this call (for security / sanity reason)
	 *
	 * @param string $var The name of the variable to unset.
	 * @node Subsystems:Sessions
	 */
	public static function un_set($var) {
		unset($_SESSION[SYS_SESSION_KEY]['vars'][$var]);
	}

	/* exdoc
	 * Creates and stores a session ticket for the given user,
	 * so that sessions can be tracked and permissions can be
	 * refreshed as needed.
	 *
	 * @param User $user The user object of the newly logged-in user.
	 * @node Subsystems:Sessions
	 */
	public static function login($user) {
		$ticket = self::getTicketString();
		if (!isset($ticket)) $ticket = self::createTicket($user);
		$_SESSION[SYS_SESSION_KEY]['user'] = $user;
		self::updateTicket($ticket, $user);
		expPermissions::load($user);
	}

	/** exdoc
	 * Clears the session of all user data, used when a user logs out.
	 * This gets rid of stale session tickets, and resets the session
	 * to a blank state.
	 * @node Subsystems:Sessions
	 */
	public static function logout() {
		global $db, $user;
		$ticket = $db->delete('sessionticket', "ticket='" . $_SESSION[SYS_SESSION_KEY]['ticket'] . "'");
		self::set('display_theme',DISPLAY_THEME_REAL);
		unset($_SESSION[SYS_SESSION_KEY]['user']);
		unset($_SESSION[SYS_SESSION_KEY]['ticket']);
		//unset($_SESSION[SYS_SESSION_KEY]['vars']);
		self::un_set("permissions");
		//redirect_to(array('section'=>SITE_DEFAULT_SECTION));
	}

	/** exdoc
	 * Looks at the session data to see if the current session is
	 * that of a logged in user. Returns true if the viewer is logged
	 * in, and false if it is not
	 * @node Subsystems:Sessions
	 * @return bool
	 */
	public static function loggedIn() {
		//if ($anon){
		return (isset($_SESSION[SYS_SESSION_KEY]['ticket']) && isset($_SESSION[SYS_SESSION_KEY]['user']));
		//}
		//else{
		//	return (isset($_SESSION[SYS_SESSION_KEY]['ticket']));
		//}
	}

	/** exdoc
	 * Clears global users session cache
	 *
	 * @param Modules $modules If not set, applies to all modules.  If set, will only clear cache for that module
	 * @param User $user if not set,applies to all users. If set, will only clear for that user
	 * @node Subsystems:Sessions
	 */
	public static function clearAllUsersSessionCache($modules = null, $user = null) {
		//ignoring module for now, as we can only easily clear entire cache
		//by just flagging the refresh field in the session ticket.
		//Maybe we'll enhance this later to store the session cache in the db
		//and we can then use this to only clear certain modules' cache.
		//We can easily clear for just an individual user or all users though,
		//but I don't see why we need to clear just one other specified user's cache
		//at this point either.  This just updates all sessionticket records to refresh=1

		global $db;
		$sessionticket->refresh = 1;
		$db->updateObject($sessionticket, 'sessionticket', '1');

		/* Possible future code
		if (isset($user)){
			$where = " uid='" . $user->id . "'";
		}else {
			$where = '';
		}

		if (isset($modules)){
			if (is_array($modules)){
				foreach ($modules as $mod){

				}
			}else{

			}
		}else{

		}
		*/
	}

	/** exdoc
	 * Clears current users session cache
	 *
	 * @param Modules $modules Array or string. If not set, applies to all modules.  If set, will only clear cache for that module
	 *
	 * @internal param \User $user if not set,applies to all users. If set, will only clear for that user
	 * @node Subsystems:Sessions
	 */
	public static function clearCurrentUserSessionCache($modules = null) {

		if (isset($modules)){
			if (is_array($modules)){
				foreach ($modules as $mod){
					if (isset($_SESSION[SYS_SESSION_KEY]['cache'][$mod])) unset($_SESSION[SYS_SESSION_KEY]['cache'][$mod]);
				}
			}else{
				if (isset($_SESSION[SYS_SESSION_KEY]['cache'][$modules])) unset($_SESSION[SYS_SESSION_KEY]['cache'][$modules]);
			}
		}else{
			if (isset($_SESSION[SYS_SESSION_KEY]['cache'])) unset($_SESSION[SYS_SESSION_KEY]['cache']);
		}
	}

	/**
	 * Clears entire user session data and truncates the sessionticket table
	 *
	 */
	public static function clearAllSessionData(){
		global $db;
		$db->delete('sessionticket',"1");
		unset($_SESSION[SYS_SESSION_KEY]);
	}

	/** exdoc
	 * Creates user ticket in sessionticket table and session
	 *
	 * @param User $user The user object of the newly logged-in user. Uses id of 0 if not supplied.
	 * @return null
	 * @node Subsystems:Sessions
	 */
	static function createTicket($user = null){
		$ticket = null;
		if (!isset($user->id)) $user = new user(0);
		$ticket->uid = $user->id;
		$ticket->ticket = uniqid("",true);
		$ticket->last_active = time();
		$ticket->start_time = time();
		$ticket->browser = $_SERVER['HTTP_USER_AGENT'];
		$ticket->ip_address = $_SERVER['REMOTE_ADDR'];
		if (!empty($_SERVER['HTTP_REFERER'])) {
			$ticket->referrer = $_SERVER['HTTP_REFERER'];
		}
		$_SESSION[SYS_SESSION_KEY]['ticket'] = $ticket->ticket;

		global $db;
		$db->insertObject($ticket,'sessionticket');
		return $ticket;
	}

	public static function setCacheValue($module, $val){
		//should always be an array, even if single index
		$_SESSION[SYS_SESSION_KEY]['cache'][$module] = $val;
	}

}

?>
