<?php
##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

/**
 * This is the class expHistory
 * Exponent History Subsystem
 *
 * The expHistory class is responsible for keeping track of which pages a user
 * has visited while on the site. It also tracks what type of page/view each one is,
 * viewable, manageable, editable and uses this information to intelligently route
 * the user to proper places.  A developer can also use the methods of this class
 * help Exponent know where to route users.
 *
 *<b>DEFINITIONS</b>
 * <ul>
 * <li><b>viewable</b> - standard (public) page/view like that for showall, show, etc...</li>
 * <li><b>manageable</b> - page/view with list of editable items for management like manage_tags, etc...  Typically you wouldn't edit in this view</li>
 * <li><b>editable</b> - a page/view used to edit an item or settings</li>
 * </ul>
 *
 * <b>USAGE EXAMPLES:</b>
 * 
 * <b>Example: How to set a history WayPoint</b>
 * 
 * This example shows how you would use expHistory to set a waypoint inside one of your actions
 * 
 * <code>
 * public function showall() {
 *	    expHistory::set('viewable', $this->params);
 *      $record = new foo($id);
 *      assign_to_template(array('record'=>$record));
 * }     
 * </code>
 * 
 * <b>Example: Return to a waypoint (automagically)</b>
 * 
 * This example shows how to let expHistory::back() automagically determine where to route the user after an action completes
 * 
 * <code>
 * public function delete() {
 *      $record = new foo($id);
 *      $record->delete();
 *      expHistory::back();
 * }     
 * </code>
 * 
 * <b>Example: Return to a waypoint 'type' (manually)</b>
 * This example shows how use expHistory::returnTo() to manually route the user after an
 * action completes. In this example the user will be routed to the last form he/she was on.
 * 
 * <code>
 * public function delete() {
 *      $record = new foo($id);
 *      $record->delete();
 *      expHistory::returnTo('editable');
 * }     
 * </code>
 * 
 * @package Subsystems
 * @subpackage Subsystems
 */
class expHistory {
    /**
     * @access public
     * @var array
     */
    public $history = array();

	/**
	 * expHistory Constructor
	 *
	 * The constructor will grab the users history from the session.  If it is not present in the session
	 * it will be initialized and saved later.
	 *
	 * @return \expHistory
	 */
	public function __construct() {
		/** exdoc
		 * Flow Type Specifier : None
	     * Old flow subsystem code
		 * @node Subsystems:Flow
		 */
		define('SYS_FLOW_NONE',	 0);

		/** exdoc
		 * Flow Type Specifier : Public Access
		 * Old flow subsystem code
		 * @node Subsystems:Flow
		 */
		define('SYS_FLOW_PUBLIC',	 1);

		/** exdoc
		 * Flow Type Specifier : Protected Access
		 * Old flow subsystem code
		 * @node Subsystems:Flow
		 */
		define('SYS_FLOW_PROTECTED', 2);

		/** exdoc
		 * Flow Type Specifier : Sectional Page
		 * Old flow subsystem code
		 * @node Subsystems:Flow
		 */
		define('SYS_FLOW_SECTIONAL', 1);

		/** exdoc
		 * Flow Type Specifier : Action Page
		 * Old flow subsystem code
		 * @node Subsystems:Flow
		 */
		define('SYS_FLOW_ACTION',	 2);

		$history = expSession::get('history');
		if (empty($history)) {
		    $this->history = array('viewable'=>array(), 'editable'=>array(), 'manageable'=>array(), 'lasts'=>array('not_editable'=>array()));
		} else {
		    $this->history = $history;
		}
	}

    public function setHistory($url_type, $params) {
        global $router;
        
        // if the history gets bigger than 10 then we will trim it.
        $size = empty($this->history[$url_type]) ? 0 : count($this->history[$url_type]);
  	    if ($size > 10) {
  	        array_shift($this->history[$url_type]);
  	        $size = $size -1;
  	    }

        // if we're in an action, we'll only set history if the action we're trying to set
        // matches the action the we're in...otherwise if we're on a page we check to make sure
        // the page we're trying to set isn't the same as the last one we just set.  This will keep
        // page refreshes the controllers on the same page from loading up the viewable array with a 
        // bunch of identical entries        

        $url = '';
        if (stristr($router->current_url,'EXPONENT.')) return false;
        if (expTheme::inAction()) {
            // we don't want to save history for these action...it screws up the flow when logging in
            if (!isset($router->params['action']) || $router->params['action'] == 'loginredirect' || $router->params['action'] == 'logout') return false;
            
            // figure out the module/controller names
            $router_name = isset($router->params['controller']) ? $router->params['controller'] : $router->params['module'];
            $params_name = isset($params['controller']) ? $params['controller'] : $params['module'];

            // make sure the controller action is the one specified via the URL
            if (expModules::getControllerName($router_name) == expModules::getControllerName($params_name) && $router->params['action'] == $params['action']) {
                $url = array('url_type'=>$router->url_type, 'params'=>$router->params);
            }        
        } else { //if we hit here it should be a page, not an action            
            $url = array('url_type'=>$router->url_type, 'params'=>$router->params);
        }
        
        if (!empty($url)) {
            $diff = array();
            
            // if this url is the exact same as the last for this type we won't save it..that way refresh won't fill up our history
            if ($size > 0) {
                $diff = @array_diff_assoc($router->params, $this->history[$url_type][$size-1]['params']);
                if (!$diff && (count($router->params) != count($this->history[$url_type][$size-1]['params']))) $diff = true;;
            }
      	    if (!empty($diff) || $size == 0) $this->history[$url_type][] = $url;
      	    
      	    // save the "lasts" information
            $this->history['lasts']['type'] = $url_type;      
            if ($url_type != 'editable') $this->history['lasts']['not_editable'] = $url_type;
  	    }
  	    
        expSession::set('history', $this->history);
    }

    public static function flush() {
        $history = array('viewable'=>array(), 'editable'=>array(), 'manageable'=>array(), 'lasts'=>array());
        expSession::set('history', $history);
    }
    
  	public static function set($url_type, $params) {
  	    global $history;

  	    $history->setHistory($url_type, $params);
	}
	
	public function goHere($url_type=null, $params=array()) {
	    global $router;
	    
	    // figure out which type of url we should go back to
	    if (empty($url_type)) $url_type = empty($this->history['lasts']['type']) ? 'viewable' : $this->history['lasts']['type'];
	    
	    // figure out how far back we should go
	    $goback = isset($params['goback']) ? $params['goback'] : 1;
	    
	    // return the user where they need to go
	    $index = count($this->history[$url_type]) - $goback;
	    if ($index < 0) $index=0;
	    $url = $this->history[$url_type][$index]['params'];
	    if ( (!isset($this->history[$url_type][$index]['params'])) || ($url['controller'] == $router->params['controller'] && $url['action'] == $router->params['action'])) {
	        $url = isset($this->history[$url_type][$index-1]['params']) ? $this->history[$url_type][$index-1]['params'] : array('section'=>'SITE_DEFAULT_SECTION');
	    }

	    redirect_to($url);
	}
	
	public function lastNotEditable() {
	    $this->goHere($this->history['lasts']['not_editable']);
	}
	
	/**
	 *
     * This function will redirect the user to the last page or action not marked
     * as editable.
     * 
     */
	public static function back() {
	    global $history;

        $history->lastNotEditable();
	}

	/**
	 * Returns a history item further back in time than the most recent (last) one
	 *
	 * @static
	 * @param $depth int How deep/far back in history to pull a link
	 * @return mixed
	 */
	public static function getBack($depth) {
	    global $history;

        $d=$depth?$depth+1:2;
		return $history->history[$history->history['lasts']['type']][count($history->history[$history->history['lasts']['type']])-$d]['params'];
	}

    public static function returnTo($url_type=null, $params=array()) {
        global $history;

        $history->goHere($url_type, $params);
    }

    public static function getLast($url_type=null) {
        global $history;

        return $history->lastUrl($url_type);
    }
    
    public static function getLastNotEditable() {
        global $history;

        return $history->lastUrl($history->history['lasts']['not_editable']);
    }
    
    public function lastUrl($url_type=null) {
        global $router;
        
        if (empty($this->history['lasts']['type']) && empty($url_type)) return $router->makeLink(array('section'=>SITE_DEFAULT_SECTION));
        
        if (empty($url_type)) $url_type = $this->history['lasts']['type'];
        
        if (!empty($this->history[$url_type])) {
            $last = end($this->history[$url_type]);
            $link = $router->makeLink($last['params']);
        } else {
            $link = $router->makeLink(array('section'=>SITE_DEFAULT_SECTION));
        }
        return $link;
    }
    
	public static function redirecto_login($redirecturl = null) {
    	$redirecturl = empty($redirecturl) ? self::getLastNotEditable() : $redirecturl;
        expSession::set('redirecturl',$redirecturl);
    	redirect_to(array('controller'=>'login', 'action'=>'loginredirect'));
	}

}

?>