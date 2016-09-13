<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * This is the class expRouter
 *
 * @package Subsystems
 * @subpackage Subsystems
 */

class expRouter {

    private $maps = array();
    public  $url_parts = '';
    public  $current_url = '';
    /**
     * Type of url
     * either 'base' (default page), 'page', 'action', or 'malformed'
     * @var string
     */
    public  $url_type = '';
    /**
     * Style of url
     * either 'sef' or 'query'
     * @var string
     */
    public  $url_style = '';
    public  $params = array();
    public  $sefPath = null;
    
    function __construct() {
        self::getRouterMaps();
    }

    /**
     * remove trailing slash
     *
     * @param $fulllink
     *
     * @return string
     */
    public static function cleanLink($fulllink)
    {           
        if(substr($fulllink, -1) == '/') $fulllink = substr($fulllink, 0, -1);  
        return $fulllink;                                   
    }
    
    /**
	 * Will build url to a module/page/etc (determined by what is passed to the $params array).
	 *
	 * @param array $params The params that are passed will determine what link is make
	 *               section
	 *               action
	 *               sef_name
	 *               module
	 *               controller
	 *               action
	 *
	 * @param bool $force_old_school Old School as in not SEF.
	 *
	 * @param bool $secure If you set $secure true but ENABLE_SSL is not turned on in the config this will be forced false
	 *
	 * @param bool $no_map Ignore router_maps
     *
	 * @return string A url
	 */
    public function makeLink($params, $force_old_school=false, $secure=false, $no_map=false) {
        $secure = ENABLE_SSL == 1 ? $secure : false;  // check if this site can use SSL if not then force the link to not be secure
        $linkbase =  $secure ? URL_BASE_SECURE : URL_BASE;
        $linkbase .= SCRIPT_RELATIVE;
                
        if (isset($params['section']) && $params['section'] == SITE_DEFAULT_SECTION) {            
            return self::cleanLink($linkbase);
        }

        // Check to see if SEF_URLS have been turned on in the site config
        if (SEF_URLS == 1 && ($_SERVER["PHP_SELF"] == PATH_RELATIVE.'index.php' || $_SERVER["PHP_SELF"] == PATH_RELATIVE.'install/index.php') && $force_old_school == false) {
            
            if (isset($params['section']) && !isset($params['action'])) {                
                if (empty($params['sef_name'])) {
                    global $db;

                    $params['sef_name'] = $db->selectValue('section', 'sef_name', 'id='.intval($params['section']));
                }                               
                return self::cleanLink($linkbase.$params['sef_name']);
            } else {                
                // initialize the link
                $link = '';               
        
                // we need to add the change the module parameter to controller if it exists
                // we can remove this snippit once the old modules are gone.
                if (!empty($params['module']) && empty($params['controller'])) $params['controller'] = $params['module'];
            
                // check to see if we have a router mapping for this controller/action
                if (empty($no_map)){
                    for ($i = 0, $iMax = count($this->maps); $i < $iMax; $i++) {
                        $missing_params = array("dump");

                        if ((!empty($params) && !empty($params['controller']) && !empty($params['action'])) && (in_array($params['controller'], $this->maps[$i]) && in_array($params['action'], $this->maps[$i]) && (!isset($this->maps[$i]['src']) || in_array($params['src'], $this->maps[$i])))) {
                            $missing_params = array_diff_key($this->maps[$i]['url_parts'], $params);
                        }

                        if (count($missing_params) == 0) {
                            foreach($this->maps[$i]['url_parts'] as $key=>$value) {
                                if ($key == 'controller') {
                                    $link .= urlencode($value)."/";
                                } else {
                                    $link .= urlencode($params[$key])."/";
                                }
                            }
                            break;  // if this hits then we've found a match
                        }
                    }
                }

                // if we found a mapping for this link then we can return it now.
                //if ($link != '') return self::encode($linkbase.$link);
                if ($link != '') return self::cleanLink($linkbase.$link);
                
                if (!empty($params['controller'])) $link .= $params['controller'].'/';
                if (!empty($params['action'])) $link .= $params['action'].'/';
                foreach ($params as $key=>$value) {
                    if(!is_array($value) && strpos($key,'__') !== 0 && $key !== 'PHPSESSID') {
                        $value = trim($value);
                        $key = trim($key);
                        if ($value != "") {
                            if ($key != 'module' && $key != 'action' && $key != 'controller') {
                                if ($key != 'src') {
                                    $link .= urlencode($key)."/".urlencode($value)."/";
                                } else {
                                    $link .= $key."/".$value."/";
                                }
                            }
                        }
                    }
                }
                //trim last / off                 
                return self::cleanLink($linkbase.$link);
            }
        } else {
            // if the users don't have SEF URL's turned on then we make the link the old school way.
            if (!empty($params['sef_name'])) unset($params['sef_name']);
            $link = $linkbase . SCRIPT_FILENAME . "?";
            foreach ($params as $key=>$value) {
                if (!is_array($value) && strpos($key,'__') !== 0 && $key !== 'PHPSESSID'){
                    $value = trim($value);
                    $key = trim($key);
                    if ($value != "") {
                        if ($key != 'src') {
                            $link .= urlencode($key)."=".urlencode($value)."&";
                        } else {
                            $link .= $key."=".$value."&";
                        }                    
                    }
                }
            }

            $link = substr($link,0,-1);
            return $link; // phillip: removed htmlspecialchars so that links return without parsing & to &amp; in URL strings
            //return htmlspecialchars($link,ENT_QUOTES);
        }
    }

    /**
     * Returns a cleaner canonical link sans 'src' param
     *
     * @return string
     */
    public function plainPath() {
        $params = $this->params;
        unset($params['src']);
        return $this->makeLink($params);
    }

    public function routeRequest() {
        global $user;

        // strip out possible xss exploits via url
        foreach ($_GET as $key=>$var) {
            if (is_string($var) && strpos($var,'">')) {
                unset(
                    $_GET[$key],
                    $_REQUEST[$key]
                );
            }
        }
        // conventional method to ensure the 'id' is only an id
        if (isset($_REQUEST['id'])) {
            if (isset($_GET['id']))
                $_GET['id'] = intval($_GET['id']);
            if (isset($_POST['id']))
                $_POST['id'] = intval($_POST['id']);

            $_REQUEST['id'] = intval($_REQUEST['id']);
        }
        // do the same for the other id's
        foreach ($_REQUEST as $key=>$var) {
            if (is_string($var) && strrpos($key,'_id',-3) !== false) {
                if (isset($_GET[$key]))
                    $_GET[$key] = intval($_GET[$key]);
                if (isset($_POST[$key]))
                    $_POST[$key] = intval($_POST[$key]);

                $_REQUEST[$key] = intval($_REQUEST[$key]);
            }
        }
        if (empty($user->id) || (!empty($user->id) && !$user->isAdmin())) {  //FIXME why would $user be empty here unless $db is down?
//            $_REQUEST['route_sanitized'] = true;//FIXME debug test
            expString::sanitize($_REQUEST);  // strip other exploits like sql injections
        }

        // start splitting the URL into it's different parts
        $this->splitURL();
        // edebug($this,1);

        if ($this->url_style == 'sef') {
            if ($this->url_type == 'page' || $this->url_type == 'base') {
                $ret = $this->routePageRequest();               // if we hit this the formatting of the URL looks like the user is trying to go to a page.
                if (!$ret) $this->url_type = 'malformed';
            } elseif ($this->url_type == 'action') {
                $this->isMappedURL();                       //check for a router map
                $ret = $this->routeActionRequest();         // we didn't have a map for this URL.  Try to route it with this function.

                // if this url wasn't a valid section, or action then kill it.  It might not actually be a "bad" url, 
                // but this is a precautionary measure against bad paths on images, css & js file, etc...with the new
                // mod_rewrite rules these bad paths will not route thru here so we need to take them into account and
                // deal with them accordingly.
                if (!$ret) $this->url_type = 'malformed';  
            } elseif ($this->url_type == 'post') {
                // some forms aren't getting the controller field set right when the form is created
                // we are putting this check here to safe guard against a controller being referred to as
                // a module in the form.
                if (!empty($_POST['controller']) || !empty($_POST['module'])) {
                    $module = !empty($_POST['controller']) ? expString::sanitize($_POST['controller']) : expString::sanitize($_POST['module']);
                    // Figure out if this is module or controller request - WE ONLY NEED THIS CODE UNTIL WE PULL OUT THE OLD MODULES
                    if (expModules::controllerExists($module)) {
                        $_POST['controller'] = $module;
                        $_REQUEST['controller'] = $module;
                    }
                }
            }
        } elseif ($this->url_style == 'query' && SEF_URLS == 1 && !empty($_REQUEST['section']) && PRINTER_FRIENDLY != 1 && EXPORT_AS_PDF != 1) {
            // if we hit this it's an old school url coming in and we're trying to use SEF's. 
            // we will send a permanent redirect so the search engines don't freak out about 2 links pointing
            // to the same page.
            header("Location: ".$this->makeLink(array('section'=>intval($_REQUEST['section']))),TRUE,301);          
        }

        // if this is a valid URL then we build out the current_url var which is used by flow, and possibly other places too
        if ($this->url_type != 'malformed') {               
            $this->current_url = $this->buildCurrentUrl();
        } else {
            // check if the URL is looking for a non-existent page or controller (we will check for bad action in renderAction())
            // if page or controller is not found we will route to the not found controller.            
            $_REQUEST['controller'] = 'notfound';
            $_REQUEST['action'] = 'handle';
        }
    }

    //FIXME what are we doing with this history? saving each page load
    public function updateHistory($section=null) {
        global $db,$user;

        // if its not already set
        // configurable tracking length
        setcookie('UserUID',expSession::getTicketString(),86400 * TRACKING_COOKIE_EXPIRES);
        $cookieID = (empty($_COOKIE['UserUID'])) ? expSession::getTicketString() : $_COOKIE['UserUID'];
        // Build out the object to insert into the db.
        // Get our parameters.
        $tmpParams = array();
        foreach ($this->params as $key=>$value) {
            if ($key != 'module' && $key != 'action' && $key != 'controller' && $key != 'section') {
                $tmpParams[$key] = $value;
            }
        }
        $trackingObject = new stdClass();
        $trackingObject->params = serialize($tmpParams);
        if ($this->url_type == 'page' || $this->url_type == 'base') {
            $trackingObject->section = $section;
        } else {
            $trackingObject->module = ($_SERVER['REQUEST_METHOD'] == 'POST') ? (empty($_POST['controller']) ? expString::sanitize($_POST['module']) : expString::sanitize($_POST['controller'])) : $this->url_parts[0];
            $trackingObject->action = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST['action'] : $this->url_parts[1];
        }
        $trackingObject->referer = empty($_SERVER['HTTP_REFERER']) ? null : $_SERVER['HTTP_REFERER'];
        $trackingObject->cookieUID = $cookieID;
        $trackingObject->user_id = $user->id;
        $trackingObject->timestamp = time();
        $trackingObject->user_address = $_SERVER['REMOTE_ADDR'];
        $trackingObject->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $trackingObject->session_id = $_COOKIE['PHPSESSID'];
        $db->insertObject($trackingObject,'tracking_rawdata');
    }

    public function splitURL() {
        global $db;

        $this->url_parts = array();
        $this->buildSEFPath();

        if (!empty($this->sefPath)) {
            $this->url_style = 'sef';
            $this->url_parts = explode('/', $this->sefPath);     

            // remove empty first and last url_parts if they exist
            //if (empty($this->url_parts[count($this->url_parts)-1])) array_pop($this->url_parts);
            if ($this->url_parts[count($this->url_parts)-1] == '') array_pop($this->url_parts);
            if (empty($this->url_parts[0])) array_shift($this->url_parts);
            
            if (count($this->url_parts) < 1 || (empty($this->url_parts[0]) && count($this->url_parts) == 1) ) {
                $this->url_type = 'base';  // no params
            } elseif (count($this->url_parts) == 1 || $db->selectObject('section', "sef_name='" . substr($this->sefPath,1) . "'") != null) {
                $this->url_type = 'page';  // single param is page name
            } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->url_type = 'post';  // params via form/post
            } else {
                // take a peek and see if a page exists with the same name as the first value...if so we probably have a page with
                // extra perms...like printerfriendly=1 or ajax_action=1;
                if (($db->selectObject('section', "sef_name='" . $this->url_parts[0] . "'") != null) && (in_array(array('printerfriendly','exportaspdf','ajax_action'), $this->url_parts))) {
                    $this->url_type = 'page';
                } else {
                    $this->url_type = 'action';
                }
            }
            $this->params = $this->convertPartsToParams();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->url_style = 'sef';
            $this->url_type = 'post';
            $this->params = $this->convertPartsToParams();
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            // if we hit here, we don't really need to do much.  All the pertinent info will come thru in the POST/GET vars
            // so we don't really need to worry about what the URL looks like.
            if ($_SERVER['REQUEST_URI'] == PATH_RELATIVE) {
                $this->url_type = 'base';
                $this->params = array();
            } else {
                $sefPath = explode('%22%3E',$_SERVER['REQUEST_URI']);  // remove any attempts to close the command
                $_SERVER['REQUEST_URI'] = $sefPath[0];
                $this->url_style = 'query';
            }
        } else {
            $this->url_type = 'base';
            $this->params = array();
        }
                              
        // Check if this was a printer friendly link request
        define('PRINTER_FRIENDLY', (isset($_REQUEST['printerfriendly']) || isset($this->params['printerfriendly'])) ? 1 : 0);         
        define('EXPORT_AS_PDF', (isset($_REQUEST['exportaspdf']) || isset($this->params['exportaspdf'])) ? 1 : 0);
        define('EXPORT_AS_PDF_LANDSCAPE', (isset($_REQUEST['landscapepdf']) || isset($this->params['landscapepdf'])) ? 1 : 0);
    }

    public function routePageRequest() {        
//        global $db;

        if ($this->url_type == 'base') {
            // if we made it in here this is a request for http://www.baseurl.com
            if (expTheme::inAction()) {
                $_REQUEST['section'] = (expSession::is_set('last_section') ? expSession::get('last_section') : SITE_DEFAULT_SECTION);
            } else {
                $_REQUEST['section'] = SITE_DEFAULT_SECTION;  
            }
        } else {
            // Try to look up the page by sef_name first.  If that doesn't exist, strip out the dashes and
            // check the regular page names.  If that still doesn't work then we'll redirect them to the 
            // search module using the page name as the search string.
            $section = $this->getPageByName(substr($this->sefPath,1));
            ########################################################
            #FJD TODO:  this needs further refinement
            #currently this requires a matching routerMap as such to work properly:
            /*
            $maps[] = array('controller'=>'store',
                    'action'=>'showall',
                    'url_parts'=>array(                
                            'title'=>'(.*)'),
            );
            $maps[] = array('controller'=>'store',
                    'action'=>'showByTitle',
                    'url_parts'=>array(                
                            'title'=>'(.*)'),
            );
            */
            //if section is empty, we'll look for the page overrides first and route to 
            //routeActionRequest with some hand wacked variables. If we can't find an override
            //then we'll return false as usual
            // since we only received a single param and it wasn't a page, try for store category, or a product
            if (empty($section)) {
                $sef_url = $this->url_parts[0];
                //check for a category
                $c = new storeCategory();                
                $cat = $c->findBy('sef_url', $sef_url);
                if (empty($cat)) {
                    //check for a product
                    $p = new product();
                    $prod = $p->findBy('sef_url', $sef_url);
                    if(!empty($prod)) {
                        //fake parts and route to action  
                        $this->url_type = 'action';                   
                        $this->url_parts[0] = 'store'; //controller
                        $this->url_parts[1] = 'show'; //controller
                        $this->url_parts[2] = 'title'; //controller
                        $this->url_parts[3] = $sef_url; //controller
                        //eDebug($this->url_parts,true);
                        $this->params = $this->convertPartsToParams();
                        return $this->routeActionRequest();
                    }
                    //else fall through
                } else {
                    //fake parts and route to action 
                    $this->url_type = 'action';                                      
                    $this->url_parts[0] = 'store'; //controller
                    $this->url_parts[1] = 'showall'; //controller
                    $this->url_parts[2] = 'title'; //controller                    
                    $this->url_parts[3] = $sef_url; //controller
                    //eDebug($this->url_parts,true);
                    $this->params = $this->convertPartsToParams();
                    return $this->routeActionRequest();
                }
                return false;
            }
            #########################################################
            //if (empty($section)) return false;  //couldnt find the page..let the calling action deal with it.
            $_REQUEST['section'] = $section->id;
        }
        
        expHistory::set('viewable', array('section'=>intval($_REQUEST['section'])));
        return true;
    }

    /**
     * figure out if this action is mapped via the mapping file (router_maps.php)
     */
    public function isMappedURL() {
        $part_count = count($this->url_parts);
        foreach ($this->maps as $map) {
            $matched = true;
            $pairs = array();
            $i = 0;
            if ($part_count == count($map['url_parts'])) {               
                foreach($map['url_parts'] as $key=>$map_part) {
                    $res = preg_match("/^$map_part/", $this->url_parts[$i]);
                    if ($res != 1) {
                        $matched = false;
                        break;
                    } 
                    $pairs[$key] = $this->url_parts[$i];
                    $i++;
                }
            } else {
                $matched = false;
            }            
              
            if ($matched) {
                // safeguard against false matches when a real action was what the user really wanted.
                if (count($this->url_parts) >= 2 && method_exists(expModules::getController($this->url_parts[0]), $this->url_parts[1]))
                    return false;

                $this->url_parts = array();
                $this->url_parts[0] = $map['controller'];
                $this->url_parts[1] = $map['action'];
        
                if (isset($map['view'])) {
                    $this->url_parts[2] = 'view';
                    $this->url_parts[3] = $map['view'];
                }

                foreach($map as $key=>$value) {
                    if ($key != 'controller' && $key != 'action' && $key != 'view' && $key != 'url_parts') {
                        $this->url_parts[] = $key;
                        $this->url_parts[] = $value;
                    }
                }

                foreach($pairs as $key=>$value) {
                    if ($key != 'controller') {
                        $this->url_parts[] = $key;
                        $this->url_parts[] = $value;
                    }
                }
                
                $this->params = $this->convertPartsToParams();
                return true;
            }
        }

        return false;
    }

    public function routeActionRequest() {
        $return_params = array('controller'=>'','action'=>'','url_parts'=>array());
    
        // If we have three url parts we assume they are controller->action->id, otherwise split them out into name<=>value pairs
        $return_params['controller'] = $this->url_parts[0]; // set the controller/module
        $return_params['action'] = $this->url_parts[1];     // set the action

        // Figure out if this is module or controller request - WE ONLY NEED THIS CODE UNTIL WE PULL OUT THE OLD MODULES
        if (expModules::controllerExists($return_params['controller'])) {
            $requestType = 'controller';
//        } elseif (is_dir(BASE.'framework/modules-1/'.$return_params['controller'])) {
//            $requestType = 'module';
        } else {
            return false;  //this is an invalid url return an let the calling function deal with it.
        }

        // now figure out the name<=>value pairs
        if (count($this->url_parts) == 3) {
            if ( is_numeric($this->url_parts[2])) {
                $return_params['url_parts']['id'] = $this->url_parts[2];
            }
        } else {
            for ($i = 2, $iMax = count($this->url_parts); $i < $iMax; $i++) {
                if ($i % 2 == 0) {
                    $return_params['url_parts'][$this->url_parts[$i]] = isset($this->url_parts[$i+1]) ? $this->url_parts[$i+1] : '';
                }
            }
        }        

        // Set the module or controller - this how the actual routing happens
        $_REQUEST[$requestType] = $return_params['controller']; //url_parts[0];
        $_GET[$requestType] = $return_params['controller'];
        $_POST[$requestType] = $return_params['controller'];
    
        // Set the action for this module or controller
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // most of the time we can just grab the action outta the POST array since this is passed as a hidden field, 
            // but sometimes it is actually set as the action on the form itself...then we get it from the params array instead.
            $action = !empty($_POST['action']) ? $_POST['action'] : $this->params['action'];
        } else {
            $action = $return_params['action'];
        }
    
        $_REQUEST['action'] = $action;
        $_GET['action'] = $action;
        $_POST['action'] = $action;

        // pass off the name<=>value pairs
        foreach($return_params['url_parts'] as $key=>$value) {
            $save_value = expString::sanitize($value);
            $_REQUEST[$key] = $save_value;
            $_GET[$key] = $save_value;
        }

        return true;
    }

    public function buildCurrentUrl() {
        $url =  URL_BASE;
        if ($this->url_style == 'sef') {
            $url .= substr(PATH_RELATIVE,0,-1).$this->sefPath;
        } else {
            $url .= urldecode((empty($_SERVER['REQUEST_URI'])) ? $_ENV['REQUEST_URI'] : $_SERVER['REQUEST_URI']);
        }
        return expString::escape(expString::sanitize($url));
    }

    public static function encode($url) {
        $url = str_replace('&', 'and', $url);
        return preg_replace("/(-)$/", "", preg_replace('/(-){2,}/', '-', strtolower(preg_replace("/([^0-9a-z-_\+])/i", '-', $url))));
    }
    
    public static function decode($url) {
        $url = str_replace('-', ' ', $url);
        return str_replace('+', '-', $url);
    }

    public function getSefUrlByPageId($id=null) {  //FIXME this method is never called and doesn't do anything as written
        if (!empty($id)) {
            global $db;

            $section = $db->selectObject('section', 'id='.intval($id));
            $url = URL_FULL;
            $url .= !empty($section->sef_name) ? $section->sef_name : $section->name;
        }
    }

    public function buildUrlByPageId($id=null) {
        global $db;

        //$url = URL_FULL;
        $url = '';
        if (!empty($id)) {
            if (SEF_URLS == 1) {
                $section = $db->selectObject('section', 'id='.intval($id));
                if ($section->id != SITE_DEFAULT_SECTION) {
                    $url .= !empty($section->sef_name) ? $section->sef_name : $section->name;
                }
            } else {
                $url .= 'index.php?section='.$id;
            }
        }
        return $url;
    }

    public function printerFriendlyLink($link_text="Printer Friendly", $class=null, $width=800, $height=600, $view='', $title_text = "Printer Friendly") {
        $url = '';
        if (PRINTER_FRIENDLY != 1 && EXPORT_AS_PDF != 1) {
            $class = !empty($class) ? $class : 'printer-friendly-link';
            $url =  '<a class="'.$class.'" href="javascript:void(0)" onclick="window.open(\'';
            if (!empty($_REQUEST['view']) && !empty($view) && $_REQUEST['view'] != $view) {
                $_REQUEST['view'] = $view;
            }
            if ($this->url_style == 'sef') {
                $url .= $this->convertToOldSchoolUrl();
                if (empty($_REQUEST['view']) && !empty($view)) $url .= '&view='.$view;
                if ($this->url_type=='base') $url .= '/index.php?section='.SITE_DEFAULT_SECTION;
            } else {
                $url .= $this->current_url;
            }
            $url .= '&printerfriendly=1\' , \'mywindow\',\'menubar=1,resizable=1,scrollbars=1,width='.$width.',height='.$height.'\');"';
            $url .= ' title="'.$title_text.'"';
            $url .= '> '.$link_text.'</a>';
            $url = str_replace('&ajax_action=1','',$url);
        }
        
        return $url; 
    }

    public function exportAsPDFLink($link_text="Export as PDF", $class=null, $width=800, $height=600, $view='', $orientation=false, $limit='', $title_text="Export as PDF") {
        $url = '';
        if (EXPORT_AS_PDF != 1 && PRINTER_FRIENDLY != 1) {
            $class = !empty($class) ? $class : 'export-pdf-link';
            $url =  '<a class="'.$class.'" href="javascript:void(0)" onclick="window.open(\'';
            if (!empty($_REQUEST['view']) && !empty($view) && $_REQUEST['view'] != $view) {
                $_REQUEST['view'] = $view;
            }
            if ($this->url_style == 'sef') {
                $url .= $this->convertToOldSchoolUrl();
                if (empty($_REQUEST['view']) && !empty($view)) $url .= '&view='.$view;
                if ($this->url_type=='base') $url .= '/index.php?section='.SITE_DEFAULT_SECTION;
            } else {
                $url .= $this->current_url;
            }
            if (!empty($orientation)) {
                $orientation = '&landscapepdf='.$orientation;
            }
            if (!empty($limit)) {
                $limit = '&limit='.$limit;
            }
            $url .= '&exportaspdf=1'.$orientation.$limit.'&\' , \'mywindow\',\'menubar=1,resizable=1,scrollbars=1,width='.$width.',height='.$height.'\');"';
            $url .= ' title="'.$title_text.'"';
            $url .= '> '.$link_text.'</a>';
            $url = str_replace('&ajax_action=1','',$url);
        }

        return $url;
    }

    public function convertToOldSchoolUrl() {
        $params = $this->convertPartsToParams();
        return $this->makeLink($params, true);
    }

    public function convertPartsToParams() {
        $params = array();
        if ($this->url_type == 'base') {
            $params['section'] = SITE_DEFAULT_SECTION;
        } elseif ($this->url_type == 'page') {
            $section = $this->getPageByName(substr($this->sefPath,1));
            $params['section'] = empty($section->id) ? null : $section->id;
        } elseif ($this->url_type == 'action') {
            $params['controller'] = $this->url_parts[0];
            $params['action'] = !empty($this->url_parts[1]) ? $this->url_parts[1] : null;
            for ($i = 2, $iMax = count($this->url_parts); $i < $iMax; $i++) {
                if ($i % 2 == 0) {
                    $params[$this->url_parts[$i]] = isset($this->url_parts[$i+1]) ? $this->url_parts[$i+1] : '';
                }
            }
        } elseif ($this->url_type == 'post') {
            if (isset($_REQUEST['PHPSESSID'])) unset($_REQUEST['PHPSESSID']);
//            foreach($_REQUEST as $name=>$val) {
////                if (get_magic_quotes_gpc()) $val = stripslashes($val);  // magic quotes fix??
////                $params[$name] = $val;
//                $params[$name] = expString::sanitize($val);  //FIXME need array sanitizer
//            }
//            if (empty($_REQUEST['route_sanitized']))
                $params = expString::sanitize($_REQUEST);
//            if (empty($data['route_sanitized'])) $_REQUEST['pre_sanitized'] = true;//FIXME debug test
        }
        //TODO: fully sanitize all params values here for ---We already do this!
//        if (isset($params['src'])) $params['src'] = expString::sanitize(htmlspecialchars($params['src']));
        return $params;
    }

    public function getPageByName($url_name) {
        global $db;
        
        $section = null;
        if (is_numeric($url_name)) {
            $section = $db->selectObject('section', 'id=' . $url_name);
            if ($section == null) $section = $db->selectObject('section', "sef_name='" . $url_name . "'");
        } elseif ($this->url_type == 'base') {
            // if we made it in here this is a request for http://www.baseurl.com
            $section = $db->selectObject('section', 'id='.SITE_DEFAULT_SECTION);
        } else {
            $section = $db->selectObject('section', "sef_name='".$url_name."'");
        }
        // if section is still empty then we should route the user to the search (cool new feature :-) )
        // at some point we need to write a special action/view for the search module that lets the user
        // know they were redirected to search since the page they tried to go to directly didn't exist.
#       if (empty($section)) {
#           header("Refresh: 0; url=".$this->makeLink(array('module'=>'search', 'action'=>'search', 'search_string'=>$this->url_parts[0])), false, 404);
#           exit();
#       } else {
#           return $section;
#       }
        return $section;
    }
    
    private function buildSEFPath () {
        // Apache
        if (strpos($_SERVER['SERVER_SOFTWARE'],'Apache') !== false || strpos($_SERVER['SERVER_SOFTWARE'],'WebServerX') !== false) {
            switch(php_sapi_name()) {
                case "cgi":
                    $this->sefPath = !empty($_SERVER['REQUEST_URI']) ? urldecode($_SERVER['REQUEST_URI']): null;
                    break;
                case "cgi-fcgi":
                    if (isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] != PATH_RELATIVE.'index.php') {
                        $this->sefPath = urldecode($_SERVER['REDIRECT_URL']);
                    } elseif (!empty($_ENV['REQUEST_URI'])) {
                        $this->sefPath = urldecode($_ENV['REQUEST_URI']);
                    } else {
                        $this->sefPath = urldecode($_SERVER['REQUEST_URI']);
                    }
                    break;
                default:
                    $this->sefPath = !empty($_SERVER['REDIRECT_URL']) ? urldecode($_SERVER['REDIRECT_URL']) : null;
                    break;
            }
        // Lighty ???
        } elseif (strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'lighttpd') !== false) {
            //FIXME, we still need a good lighttpd.conf rewrite config for sef_urls to work
            if (isset($_SERVER['ORIG_PATH_INFO'])) {
                $this->sefPath = urldecode($_SERVER['ORIG_PATH_INFO']);
            } elseif (isset($_SERVER['REDIRECT_URI'])){
                $this->sefPath = urldecode(substr($_SERVER['REDIRECT_URI'],9));
            } elseif (isset($_SERVER['REQUEST_URI'])){
                $this->sefPath = urldecode($_SERVER['REQUEST_URI']);
            }
        // Nginx ???
        } elseif (strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'nginx') !== false) {
            $this->sefPath = urldecode($_SERVER['REQUEST_URI']);
        } else {
            $this->sefPath = urldecode($_SERVER['REQUEST_URI']);
        }
        
        $this->sefPath = substr($this->sefPath,strlen(substr(PATH_RELATIVE,0,-1))); 
        if (strpos($this->sefPath,'/index.php') === 0) {
            $this->sefPath = null;
        }
        
		//parse the ecommerce tracking code if present and include in the object
        if(isset($_SERVER['argv']) && is_array($_SERVER['argv']))
        {
            foreach($_SERVER['argv'] as $set)
            {
                $s = explode("=",$set);
                if($s[0] == "ectid")
                {
                    $this->ectid = $s[1];    
                }   
            }            
        }
        if (substr($this->sefPath,-1) == "/") $this->sefPath = substr($this->sefPath,0,-1);
        // sanitize it
        $sefPath = explode('">',$this->sefPath);  // remove any attempts to close the command
        $this->sefPath = expString::escape(expString::sanitize($sefPath[0]));
    }

    public function getSection() {
        global $db;

        if (expTheme::inAction()) {
            if (isset($_REQUEST['section'])) {
                $section = $this->url_style=="sef" ? $this->getPageByName($_REQUEST['section'])->id : intval($_REQUEST['section']) ;
            } else {
                $section = (expSession::is_set('last_section') ? expSession::get('last_section') : SITE_DEFAULT_SECTION);
            }
        } else {
            $section = (isset($_REQUEST['section']) ? intval($_REQUEST['section']) : SITE_DEFAULT_SECTION);
        }
        $testsection = $db->selectObject('section','id='.$section);
        if (empty($testsection)) {
            $section = SITE_DEFAULT_SECTION;
        }
        return $section;
    }

    public function getSectionObj($section) {
        global $db;

        if ($section == "*") {
            $sectionObj = call_user_func(expModules::getModuleClassName($this->params['controller']) . "::getSection", $this->params);
        } else {
//            $sectionObj = $db->selectObject('section','id='. intval($section));
            $sectionObj = new section(intval($section));
        }
//        $sectionObj = $db->selectObject('section','id='. intval($section));
        if (!$sectionObj->canView()) {
            define('AUTHORIZED_SECTION',0);
        } else {
            define('AUTHORIZED_SECTION',1);
        }
        if (!$sectionObj->isPublic()) {
            define('PUBLIC_SECTION',0);
        } else {
            define('PUBLIC_SECTION',1);
        }
    
        if (isset($_REQUEST['section'])) {
            expSession::set('last_section', intval($_REQUEST['section']));
        } elseif ($section == SITE_DEFAULT_SECTION) {
            expSession::set('last_section', intval(SITE_DEFAULT_SECTION));
        } else {
            //expSession::unset('last_section');
        }
        return $sectionObj;
    }
    
    public function getRouterMaps() {
        $mapfile = BASE.'framework/core/router_maps.php';
		if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/router_maps.php')) {
			$mapfile = BASE.'themes/'.DISPLAY_THEME.'/router_maps.php';
        }

        include_once($mapfile);
        $this->maps = $maps;  // $maps is set by included $mapfile
    }
    
    public function getTrackingId()
    {        
        if(isset($this->ectid)) return $this->ectid;
        else return '';
    }
}

?>