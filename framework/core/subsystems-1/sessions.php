<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

if (!defined('EXPONENT')) exit('');

/* exdoc
 * The definition of this constant lets other parts of the system know 
 * that the subsystem has been included for use.
 * @node Subsystems:Sessions
 */
//define('SYS_SESSIONS',1);

// session key may be overridden
if (!defined('SYS_SESSION_KEY')) {
	/* exdoc
	 * @state <b>UNDOCUMENTED</b>
	 * @node Undocumented
	 */
	define('SYS_SESSION_KEY',PATH_RELATIVE);
}

// Name of session cookie may also be overridden
if (!defined('SYS_SESSION_COOKIE')) {
	define('SYS_SESSION_COOKIE','PHPSESSID');
}

/* exdoc
 * Runs necessary code to initialize sessions for use.
 * This sends the session cookie header (via the session_start
 * PHP function) and sets up session variables needed by the
 * rest of the system and this subsystem.
 * @node Subsystems:Sessions
 */
function exponent_sessions_initialize() {	
	$sessid  = '';
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
	
}

/* exdoc
 * Validates the stored session ticket against the database.  This is used
 * to force refreshes and force logouts.  It also updates activity time.
 * @node Subsystems:Sessions
 */
function exponent_sessions_validate() {
	global $db, $user;    
	//FJD - create a ticket for every session instead of just logged in users
	if (!isset($_SESSION[SYS_SESSION_KEY]['ticket'])) {	
		$ticket = exponent_sessions_createTicket();    
	}else{
		$ticket = $db->selectObject('sessionticket',"ticket='".$_SESSION[SYS_SESSION_KEY]['ticket']."'");               
	}	
    
    //if we don't have a ticket here, that means the browser passed the cookie, the session is still 
    // active, but the DATABASE tickets table was cleared.
    
	if(SESSION_TIMEOUT_ENABLE == true){	
		$timeoutval = SESSION_TIMEOUT;
        if ($timeoutval < 300) $timeoutval = 300;
        if ($ticket == null || $ticket->last_active < time() - $timeoutval) {
			exponent_sessions_logout();
			define('SITE_403_HTML',SESSION_TIMEOUT_HTML);
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
		if (isset($user)) exponent_permissions_load($user);			
		exponent_sessions_clearCurrentUserSessionCache();
		$ticket->refresh = 0;		
	}
        
	exponent_sessions_updateTicket($ticket, $user);
	// Clean out old sessions from the sessionticket table.
	$db->delete('sessionticket','last_active < ' . (time() - SESSION_TIMEOUT));
	
	define('SITE_403_HTML', SITE_403_REAL_HTML);
}

/* exdoc
 * Creates and stores a session ticket for the given user,
 * so that sessions can be tracked and permissions can be
 * refreshed as needed.
 *
 * @param User $user The user object of the newly logged-in user.
 * @node Subsystems:Sessions
 */
function exponent_sessions_login($user) {	
	$ticket = exponent_sessions_getTicketString();
	if (!isset($ticket)) $ticket = exponent_sessions_createTicket($user);	
	$_SESSION[SYS_SESSION_KEY]['user'] = $user;
	exponent_sessions_updateTicket($ticket, $user);
	exponent_permissions_load($user);
}

/* exdoc
 * Creates user ticket in sessionticket table and session
 *
 * @param User $user The user object of the newly logged-in user. Uses id of 0 if not supplied.
 * @node Subsystems:Sessions
 */
function exponent_sessions_createTicket($user = null){    
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

/* exdoc
 * Creates user ticket in sessionticket table and session
 *
 * @param User $user The user object of the newly logged-in user. Uses id of 0 if not supplied.
 * @node Subsystems:Sessions
 */
function exponent_sessions_updateTicket($ticket, $user){
	global $db;	
	if (isset($ticket->ticket)){
		$ticket->uid = isset($user->id) ? $user->id : 0;
		$ticket->last_active = time();
		$db->updateObject($ticket,'sessionticket',"ticket='" . $ticket->ticket . "'");
	}
	return $ticket;
}


/* exdoc
 * Clears the session of all user data, used when a user logs out.
 * This gets rid of stale session tickets, and resets the session
 * to a blank state.
 * @node Subsystems:Sessions
 */
function exponent_sessions_logout() {
	global $db, $user;
	$ticket = $db->delete('sessionticket', "ticket='" . $_SESSION[SYS_SESSION_KEY]['ticket'] . "'");
	unset($_SESSION[SYS_SESSION_KEY]['user']);
	unset($_SESSION[SYS_SESSION_KEY]['ticket']);
	//unset($_SESSION[SYS_SESSION_KEY]['vars']);
	exponent_permissions_clear();
	//redirect_to(array('section'=>SITE_DEFAULT_SECTION));
}

/* exdoc
 * Looks at the session data to see if the current session is
 * that of a logged in user. Returns true if the viewer is logged
 * in, and false if it is not
 * @node Subsystems:Sessions
 */
function exponent_sessions_loggedIn() {
	//if ($anon){
	return (isset($_SESSION[SYS_SESSION_KEY]['ticket']) && isset($_SESSION[SYS_SESSION_KEY]['user']));
	//}
	//else{
	//	return (isset($_SESSION[SYS_SESSION_KEY]['ticket']));		
	//}	
}

//Clears entire user session data and truncates the sessionticket table
function exponent_sessions_clearAllSessionData(){
	global $db;
	$db->delete('sessionticket',"1");
	unset($_SESSION[SYS_SESSION_KEY]);
}


function exponent_sessions_getTicketString() {
	//if (isset($_SESSION[SYS_SESSION_KEY]['ticket'])) { 
	//	return $_SESSION[SYS_SESSION_KEY]['ticket'];
	//} else return null;
	global $db;
	if (isset($_SESSION[SYS_SESSION_KEY]['ticket'])) {
		if($db->selectObject('sessionticket',"ticket='".$_SESSION[SYS_SESSION_KEY]['ticket']."'") != null ) {
			return $_SESSION[SYS_SESSION_KEY]['ticket'];
		}else return null;
	}
}

function exponent_sessions_getOrigReferrer($ticket=null) {
    global $db;
    
    // if they didn't pass in a specific ticket
    if (empty($ticket)) {
        // see if there is a ticket in the session to use
        if (isset($_SESSION[SYS_SESSION_KEY]['ticket'])) {
            $ticket = $_SESSION[SYS_SESSION_KEY]['ticket'];
        } else {
            return null;
        }
    }
    
    return $db->selectValue('sessionticker', 'referrer', "ticket='".$ticket."'" );
}

/* exdoc
 * Checks to see if the session holds a set variable of the given name.
 *
 * Note that some session variables (like the user object and the ticket)
 * cannot be changed using this call (for security / sanity reason)
 * @node Subsystems:Sessions
 */
function exponent_sessions_isset($var) {
	return isset($_SESSION[SYS_SESSION_KEY]['vars'][$var]);
}

/* 
 * Sets a variable in the session data, for use on subsequent page calls.
 *
 * Note that some session variables (like the user object and the ticket)
 * cannot be changed using this call (for security / sanity reason)
 *
 * @param string $var The name of the variable, for later reference
 * @param mixed $val The value to store
 * @node Subsystems:Sessions
 */
function exponent_sessions_set($var, $val) {
	$_SESSION[SYS_SESSION_KEY]['vars'][$var] = $val;
}

/* exdoc
 * Removes a variable from the session.
 *
 * Note that some session variables (like the user object and the ticket)
 * cannot be changed using this call (for security / sanity reason)
 *
 * @param string $var The name of the variable to unset.
 * @node Subsystems:Sessions
 */
function exponent_sessions_unset($var) {
	unset($_SESSION[SYS_SESSION_KEY]['vars'][$var]);
}

/* exdoc
 * This retrieves the value of a persistent session variable. Returns
 * null if the variable is not set in the session, or the value of the stored variable.
 *
 * Note that some session variables (like the user object and the ticket)
 * cannot be changed using this call (for security / sanity reason)
 *
 * @param string $var The name of the variable to retrieve.
 * @node Subsystems:Sessions
 */
function exponent_sessions_get($var) {
	if (isset($_SESSION[SYS_SESSION_KEY]['vars'][$var])) {
		return $_SESSION[SYS_SESSION_KEY]['vars'][$var];
	} else return null;
}

/* exdoc
 * Clears current users session cache
 *
 * @param Modules $modules Array or string. If not set, applies to all modules.  If set, will only clear cache for that module
 * @param User $user if not set,applies to all users. If set, will only clear for that user
 * @node Subsystems:Sessions
 */
function exponent_sessions_clearCurrentUserSessionCache($modules = null) {	
		
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

function exponent_sessions_getCacheValue($module){
	//returns array or null
	if (isset($_SESSION[SYS_SESSION_KEY]['cache'][$module])) return($_SESSION[SYS_SESSION_KEY]['cache'][$module]);
	else return null;
}

function exponent_sessions_setCacheValue($module, $val){
	//should always be an array, even if single index
	$_SESSION[SYS_SESSION_KEY]['cache'][$module] = $val;
}

/* exdoc
 * Clears global users session cache
 *
 * @param Modules $modules If not set, applies to all modules.  If set, will only clear cache for that module
 * @param User $user if not set,applies to all users. If set, will only clear for that user
 * @node Subsystems:Sessions
 */
function exponent_sessions_clearAllUsersSessionCache($modules = null, $user = null) {	
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


// Database-stored session handling
//
//  Read function must return string value always to make 
// save handler work as expected. Return empty string if 
// there is no data to read. Return values from other handlers 
// are converted to boolean expression. TRUE for success, 
// FALSE for failure.

// Opens the session
function exponent_sessions_db_open($save_path,$sess_name) {
	global $SYS_SESSIONS_SESSIONNAME;
	$SYS_SESSIONS_SESSIONNAME = $sess_name;
	// Put locking in database
	
}

function exponent_sessions_db_close() {
	
}

function exponent_sessions_db_read($id) {

}

function exponent_sessions_db_write($id, $sess_data) {

}

function exponent_sessions_db_destroy($id) {

}

function exponent_sessions_db_gc($maxlifetime) {

}

?>
