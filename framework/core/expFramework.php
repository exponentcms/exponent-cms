<?php
##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

//Global Variables

// expModules
/**
 * Stores the list of available/active controllers
 * @var array $available_controllers
 * @name $available_controllers
 */
$available_controllers = array();

// expTheme
/**
 * Stores the theme validation flags
 * @var array $validateTheme does theme have header & footer
 * @name $validateTheme
 */
$validateTheme = array("headerinfo"=>false,"footerinfo"=>false);
/**
 * Stores the list of module scopes
 * @var array $module_scope
 * @name $module_scope
 */
$module_scope = array();

// expLang
/**
 * Stores the list of language strings for the current language
 * @var array $cur_lang
 * @name $cur_lang
 */
$cur_lang = array();
/**
 * Stores the list of language strings for the default language (English - US)
 * @var array $default_lang
 * @name $default_lang
 */
$default_lang = array();
/**
 * Stores the name of the default language file
 * @var array $default_lang_file
 * @name $default_lang_file
 */
$default_lang_file = '';
/**
 * Stores the name of the language file to be created
 * @var array $target_lang_file
 * @name $target_lang_file
 */
$target_lang_file = '';

// expDatabase
/**
 * Stores the exponent database object
 * @var \database $db the exponent database object
 * @name $db
 */
$db = new stdClass();

// expHistory
/**
 * Stores the browsing history object
 * @var expHistory $history
 * @name $history
 */
$history = new stdClass();

// user model
/**
 * Stores the current user object
 * @var user $user
 * @name $user
 */
$user = new stdClass();
/**
 * This global array belongs exclusively to the user model, and is used to cache
 * users as they are retrieved, to help out with performance when doing a lot of
 * work with user accounts and profile information.
 * @var array $SYS_USERS_CACHE
 * @name $SYS_USERS_CACHE
 */
$SYS_USERS_CACHE = array();

// expRouter
/**
 * Stores the routing/link/url object
 * @var expRouter $router
 * @name $router
 */
$router = new stdClass();
/**
 * Stores the routing/link/url object
 * @var section $sectionObj
 * @name $sectionObj
 */
$sectionObj = new stdClass();

// expCore
/**
 * Stores the list of sections/pages for the site
 * @var array $sections
 * @name $sections
 */
$sections = array();
// expPermissions
/**
 * Stores the permission data for the current user.
 * This should not be modified by anything outside of the permissions subsystem.
 * @var array $exponent_permissions_r
 * @name $exponent_permissions_r
 */
$exponent_permissions_r = array();

// expJavascript
/**
 * Stores the user's javascript files
 * @var array $userjsfiles
 * @name $userjsfiles
 */
$userjsfiles = array();
/**
 * Stores the user's javascript files
 * @var array $js2foot
 * @name $js2foot
 */
$js2foot = array();
//$yui2js = array();
/**
 * Stores the user's javascript files
 * @var array $yui3js
 * @name $yui3js
 */
$yui3js = array();
/**
 * Stores the user's javascript files
 * @var array $jqueryjs
 * @name $jqueryjs
 */
$jqueryjs = array();
/**
 * Stores the user's javascript files
 * @var array $expJS
 * @name $expJS
 */
$expJS = array();

// expCSS
/**
 * Stores the user's css files
 * @var array $css_primer
 * @name $css_primer
 */
$css_primer = array();
/**
 * Stores the user's css files
 * @var array $css_core
 * @name $css_core
 */
$css_core = array();
/**
 * Stores the user's css files
 * @var array $css_links
 * @name $css_links
 */
$css_links = array();
/**
 * Stores the user's css files
 * @var array $css_theme
 * @name $css_theme
 */
$css_theme = array();
/**
 * Stores the user's css files
 * @var array $css_inline
 * @name $css_inline
 */
$css_inline = array();
/**
 * Stores the user's css files
 * @var array $head_config
 * @name $head_config
 */
$head_config = array();
/**
 * Stores the user's css files
 * @var string $jsForHead
 * @name $jsForHead
 */
$jsForHead = "";
/**
 * Stores the user's css files
 * @var string $cssForHead
 * @name $cssForHead
 */
$cssForHead = "";

// expTemplate
/**
 * Stores the global template
 * @var \basetemplate $template
 * @name $template
 */
$template = null;

// expTimer
/**
 * Stores the timer
 * @var expTimer $timer
 * @name $timer
 */
$timer = null;

// e-commerce
/**
 * Stores the order
 * @var \order $order
 * @name $order
 */
$order = null;

/**
 * Main module display logic/routine
 *
 * @param array $parms
 * @return bool|mixed|string
 */
function renderAction(array $parms=array()) {
    global $user, $db;
    
    //Get some info about the controller
    $baseControllerName = expModules::getControllerName($parms['controller']);
    $fullControllerName = expModules::getControllerClassName($parms['controller']);
    $controllerClass = new ReflectionClass($fullControllerName);
    
    // Figure out the action to use...if the specified action doesn't exist then
    // we look for the index action.
    if ($controllerClass->hasMethod($parms['action'])) {
        $action = $parms['action'];
        /* TODO:  Not sure if this needs to be here. FJD
		$meth = $controllerClass->getMethod($action);
        if ($meth->isPrivate()) expQueue::flashAndFlow('error', gt('The requested action could not be performed: Action not found'));*/
    } elseif ($controllerClass->hasMethod('index')) {
        $action = 'index';
    } elseif ($controllerClass->hasMethod('showall')) {
        $action = 'showall';
    } else {
        expQueue::flashAndFlow('error', gt('The requested action could not be performed: Action not found'));
    }

    // initialize the controller.
    $src = isset($parms['src']) ? $parms['src'] : null;
    $controller = new $fullControllerName($src, $parms);    
    
    //Set up the template to use for this action
    global $template;
    $view = !empty($parms['view']) ? $parms['view'] : $action;
    $template = get_template_for_action($controller, $view, $controller->loc);
    
    // have the controller assign knowledge about itself to the template.
    // this has to be done after the controller gets the template for its actions
    $controller->moduleSelfAwareness();

    //if this controller is being called by a container then we should have a module title.
    if (isset($parms['moduletitle'])) {
        $template->assign('moduletitle', $parms['moduletitle']);
    } else {
        $title = new stdClass();
        $title->mod = $controller->loc->mod.'Controller';  //FIXME do we process modules also needing this?
        $title->src = $controller->loc->src;
        $title->int = '';
        $template->assign('moduletitle', $db->selectValue('container', 'title', "internal='".serialize($title)."'"));
    }

    //setup some default models for this controller's actions to use
    foreach ($controller->getModels() as $model) {
        $controller->$model = new $model(null,false,false);   //added null,false,false to reduce unnecessary queries. FJD
    }
    
    // add the $_REQUEST values to the controller <- pb: took this out and passed in the params to the controller constructor above
    //$controller->params = $parms;
    //check the perms for this action
    $perms = $controller->permissions();
    
    //we have to treat the update permission a little different..it's tied to the create/edit
    //permissions.  Really the only way this will fail will be if someone bypasses the perm check
    //on the edit form somehow..like a hacker trying to bypass the form and just submit straight to 
    //the action. To safeguard, we'll catch if the action is update and change it either to create or
    //edit depending on whether an id param is passed to. that should be sufficient.
    $common_action = null;
    if ($parms['action'] == 'update') {
        $perm_action = (!isset($parms['id']) || $parms['id'] == 0) ? 'create' : 'edit';
    } elseif ($parms['action'] == 'saveconfig') {
        $perm_action = 'configure';
    } else {
        // action convention for controllers that manage more than one model (datatype). 
        // if you preface the name action name with a common crud action name we can check perms on 
        // it with the developer needing to specify any...better safe than sorry.
        // i.e if the action is edit_mymodel it will be checked against the edit permission
        if (stristr($parms['action'], '_')) $parts = explode("_", $parms['action']);
        $common_action = isset($parts[0]) ? $parts[0] : null;
        $perm_action = $parms['action'];
    }

    if (array_key_exists($perm_action, $perms)) {
        if (!expPermissions::check($perm_action, $controller->loc)) {
            if (expTheme::inAction()) {
                flash('error', gt("You don't have permission to")." ".$perms[$perm_action]);
                expHistory::returnTo('viewable');
            } else {
                return false;
            }
        }
    } elseif (array_key_exists($common_action, $perms)) {
        if (!expPermissions::check($common_action, $controller->loc)) {
            if (expTheme::inAction()) {
                flash('error', gt("You don't have permission to")." ".$perms[$common_action]);
                expHistory::returnTo('viewable');
            } else {
                return false;
            }
        }
    } elseif (array_key_exists($perm_action, $controller->requires_login)) {
        // check if the action requires the user to be logged in
        if (!$user->isLoggedIn()) {
            $msg = empty($controller->requires_login[$perm_action]) ? gt("You must be logged in to perform this action") : $controller->requires_login[$perm_action];
            flash('error', $msg);
            expHistory::redirecto_login();
        }
    } elseif (array_key_exists($common_action, $controller->requires_login)) {
        // check if the action requires the user to be logged in
        if (!$user->isLoggedIn()) {
            $msg = empty($controller->requires_login[$common_action]) ? gt("You must be logged in to perform this action") : $controller->requires_login[$common_action];
            flash('error', $msg);
            expHistory::redirecto_login();
        }
    } 
    
    // register this controllers permissions to the view for in view perm checks
    $template->register_permissions(array_keys($perms), $controller->loc);
    
    // pass this controllers config to the view
    $template->assign('config', $controller->config);
    
    // globalizing $user inside all templates
    $template->assign('user', $user);
    
    // assign the controllers basemodel to the view
    $template->assign('modelname', $controller->basemodel_name);

    // lastly, run the action which can also override the above assignments
    $controller->$action();

    if (empty($parms['no_output'])) {
        $template->output();
    } else {
        $html = $template->render();
        return $html;
    }
}

function hotspot($source = null) {
    if (!empty($source)) {
        global $sectionObj;
	    //FIXME there is NO 'page' object and section has no _construct method
        $page = new section($sectionObj->id);
        $modules = $page->getModulesBySource($source);  //FIXME there is no getModulesBySource method anywhere
        //eDebug($modules);exit();

        foreach ($modules as $module) {
            renderAction(array('controller'=>$module->type, 'action'=>$module->action, 'instance'=>$module->id));
        }
    }
}

function makeLink($params=array(), $secure=false) {
    global $router;
    if(!is_array($params) || count($params) == 0) return false;
    $secure = empty($secure) ? false : true;
    return $router->makeLink($params, false, $secure);
}

function redirect_to($params=array(), $secure=false) {
    global $router;
    $secure = empty($secure) ? false : true;
    $link = (!is_array($params)) ? $params : $router->makeLink($params, false, $secure);
    header("Location: " . $link);
    exit();
}   

function flash($name, $msg) {
    expQueue::flash($name, $msg);
}

function flashAndFlow($name, $msg) {
    expQueue::flashAndFlow($name, $msg);
}

/**
 * Display the message queue
 *
 * @param null $name
 * @return bool|mixed|string
 */
function show_msg_queue($name=null) {
    return expQueue::show($name);
}

/**
 * Assign a variable to the current template
 *
 * @param array $vars
 * @return bool
 */
function assign_to_template(array $vars=array()) {
    global $template;
    
    if (empty($template) || count($vars) == 0) return false;
    foreach ($vars as $key=>$val) {
        $template->assign($key, $val);
    }
}

function get_model_for_controller($controller_name) {
    $start_pos = stripos($controller_name, 'controller');
    if ($start_pos === false) {
        return false;
    } else {
        return substr($controller_name, 0, $start_pos);
    }
}

function get_common_template($view, $loc, $controllername='') {
    $controller = new stdClass();
    $controller->baseclassname = empty($controllername) ? 'common' : $controllername;
    $controller->relative_viewpath = 'framework/modules-1/common/views'.$controller->baseclassname;
    $controller->loc = $loc;
    
    $basepath = BASE.'framework/modules/common/views/'.$controllername.'/'.$view.'.tpl';
    $themepath = BASE.'themes/'.DISPLAY_THEME.'/modules/common/views/'.$controllername.'/'.$view.'.tpl';

    if (file_exists($themepath)) {
        return new controllertemplate($controller,$themepath);
    } elseif(file_exists($basepath)) {
        return new controllertemplate($controller,$basepath);
    } else {
        return new controllertemplate($controller, BASE.'framework/common/views/scaffold/blank.tpl');
    }
}

/**
 * Return entire list of all controller configuration views available
 *
 * @param $controller
 * @param $loc
 * @return array
 */
function get_config_templates($controller, $loc) {
    global $db;
    
    // set paths we will search in for the view
    $commonpaths = array(
        BASE.'framework/modules/common/views/configure',
        BASE.'themes/'.DISPLAY_THEME.'/modules/common/views/configure',
    );
    
    $modpaths = array(
        $controller->viewpath.'/configure',
	    BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/configure'
    );
    
    // get the common configuration files    
    $common_views = find_config_views($commonpaths, $controller->remove_configs);
    foreach ($common_views as $key=>$value) {
        $common_views[$key]['name'] = gt($value['name']);
    }

    // get the config views for the module
    $module_views = find_config_views($modpaths);
    foreach ($module_views as $key=>$value) {
        $module_views[$key]['name'] = gt($value['name']);
    }

    // look for a config form for this module's current view    
    $controller->loc->mod = expModules::getControllerClassName($controller->loc->mod);
    //check to see if hcview was passed along, indicating a hard-coded module
    if (!empty($controller->params['hcview'])) {
        $viewname = $controller->params['hcview'];
    } else {
        $viewname = $db->selectValue('container', 'view', "internal='".serialize($controller->loc)."'");
    }
    $viewconfig = $viewname.'.config';
    foreach ($modpaths as $path) {
        if (file_exists($path.'/'.$viewconfig)) {
            $fileparts = explode('_', $viewname);
            if ($fileparts[0]=='show'||$fileparts[0]=='showall') array_shift($fileparts);
            $module_views[$viewname]['name'] = ucwords(implode(' ', $fileparts)).' '.gt('View Configuration');
            $module_views[$viewname]['file'] =$path.'/'.$viewconfig;
        }
    }
    
    // sort the views highest to lowest by filename
    // we are reverse sorting now so our array merge
    // will overwrite property..we will run array_reverse
    // when we're finised to get them back in the right order
    krsort($common_views);
    krsort($module_views);
    
    $views = array_merge($common_views, $module_views);
    $views = array_reverse($views);

    return $views;
}

/**
 * Return list of controller configuration views
 * @param array $paths
 * @param array $excludes
 * @return array
 */
function find_config_views($paths=array(), $excludes=array()) {
    $views = array();
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl') {
                    $filename = substr($file, 0, -4);
                    if (!in_array($filename, $excludes)) {
                        $fileparts = explode('_', $filename);
                        $views[$filename]['name'] = ucwords(implode(' ', $fileparts));
                        $views[$filename]['file'] = $path.'/'.$file;
                    }
                }
            }
        }
    }
    
    return $views;
}

function get_template_for_action($controller, $action, $loc) {
    // set paths we will search in for the view
    $basepath = $controller->viewpath.'/'.$action.'.tpl';
    $themepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$action.'.tpl';

    // the root action will be used if we don't find a view for this action and it is a derivative of
    // action.  i.e. showall_by_tags would use the showall.tpl view if we do not have a view named
    // showall_by_tags.tpl
    $root_action = explode('_', $action);
    $rootbasepath = $controller->viewpath.'/'.$root_action[0].'.tpl';
    $rootthemepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$root_action[0].'.tpl';

    if (file_exists($themepath)) {
        return new controllertemplate($controller, $themepath);
    } elseif (file_exists($basepath)) {     
        return new controllertemplate($controller, $basepath);
    } elseif ($root_action[0] != $action) {
        if (file_exists($rootthemepath)) {
            return new controllertemplate($controller, $rootthemepath);
        } elseif (file_exists($rootbasepath)) {
            return new controllertemplate($controller, $rootbasepath);
        }
    }
    
    // if we get here it means there were no views for the this action to be found.
    // we will check to see if we have a scaffolded version or else just grab a blank template.
    if (file_exists(BASE.'framework/modules/common/views/scaffold/'.$action.'.tpl')) {
        return new controllertemplate($controller, BASE.'framework/modules/common/views/scaffold/'.$action.'.tpl');
    } else {
        return new controllertemplate($controller, BASE.'framework/modules/common/views/scaffold/blank.tpl');
    }
}

/**
 * Return list of controller display views available
 * @param $ctl
 * @param $action
 * @param $human_readable
 * @return array
 */
function get_action_views($ctl, $action, $human_readable) {
    // setup the controller
    $controllerName = expModules::getControllerClassName($ctl);
    $controller = new $controllerName();
    
    // set path information 
    $paths = array(
        $controller->viewpath,
        BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath,
    );
    
    $views = array();
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl') {
                    $filename = substr($file, 0, -4);
                    $fileparts = explode('_', $filename);
                    if ($fileparts[0] == $action) {
                        if (count($fileparts) == 1) {
                            $views[$filename] = 'Default';
                        } else {
                            array_shift($fileparts); //shift the action name off the array of words
                            $views[$filename] = ucwords(implode(' ', $fileparts));
                        }
                    }
                }
            }
        }
    }

    // Language-ize the views names
    foreach ($views as $key=>$value) {
        $views[$key] = gt($value);
    }

    return $views;
}

/**
 * Return list of attached file display views available
 * @return array
 */
function get_filedisplay_views() {
    $paths = array(
        BASE.'framework/modules/common/views/file/',
        BASE.'themes/'.DISPLAY_THEME.'modules/common/views/file/',
    );
    
    $views = array();
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl') {
                    $filename = substr($file, 0, -4);
                    $views[$filename] = gt($filename);
                }
            }
        }
    }
    
    return $views;
}

function object2Array($object=null) {
    $ret_array = array();
    if(empty($object)) return $ret_array;

    foreach($object as $key=>$value) {
        $ret_array[$key] = $value;
    }

    return $ret_array;
}

function expUnserialize($serial_str) {
    if ($serial_str === 'Array') return null;  // empty array string??
    $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
    $out2 = unserialize($out);
    if (is_array($out2) && !empty($out2['moduledescription'])) {  // work-around for links in module descriptions
        $out2['moduledescription'] = stripslashes($out2['moduledescription']);
    }
    return $out2;
}

/**
 *  callback when the buffer gets flushed. Any processing on the page output
 * just before it gets rendered to the screen should happen here.
 * @param $buffer
 * @param null $mode
 * @return mixed
 */
function expProcessBuffer($buffer, $mode=null) {
     global $jsForHead, $cssForHead;
     return (str_replace("<!-- MINIFY REPLACE -->", $cssForHead.$jsForHead, $buffer));
}

function createValidId ($id) {
    $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
    return str_replace($badvals, "",$id);
}

function curPageURL() {
    $pageURL = 'http';
    if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

// this function is called from exponent.php as the ajax error handler
function handleErrors($errno, $errstr, $errfile, $errline) {
    if (DEVELOPMENT > 0) {
        switch ($errno) {
                case E_USER_ERROR:
                    $msg = 'PHP Error('.$errno.'): ';
                break;
                case E_USER_WARNING:
                    $msg = 'PHP Warning('.$errno.'): ';
                break;
                case E_USER_NOTICE:
                case E_NOTICE:
                    $msg = 'PHP Notice('.$errno.'): ';
                    break;
                default:
                    $msg = 'PHP Issue('.$errno.'): ';
                break;
            }
        $msg .= $errstr;
        $msg .= !empty($errfile) ? ' in file '.$errfile : "";
        $msg .= !empty($errline) ? ' on line '.$errline : "";
        // currently we are doing nothing with these error messages..we could in the future however.
    }
}

function gt($s){
    return expLang::gettext($s);
}

function glist($s){
    if (is_array($s)) {
        $list = array();
        foreach ($s as $key=>$phrase) {
            $list[$key] = expLang::gettext(trim($phrase));
        }
    } else {
        $list = '';
        $phrases = explode(",",$s);
        foreach ($phrases as $key=>$phrase) {
            if ($key) $list .= ',';
            $list .= expLang::gettext(trim($phrase));
        }
    }
    return $list;
}

/**
 * dumps the passed variable to screen, but only if in development mode
 * @param mixed $var the variable to dump
 * @param bool $halt if set to true will halt execution
 * @return void
 */
function eDebug($var, $halt=false){
	if (DEVELOPMENT) {
        echo "<pre>";
		print_r($var);
        echo "</pre>";

		if ($halt) die();
	}
}

/**
 * dumps the passed variable to a log, but only if in development mode
 * @param mixed $var the variable to log
 * @param string $type the type of entry to record
 * @param string $path the pathname for the log file
 * @param string $minlevel
 * @return void
 */
function eLog($var, $type='', $path='', $minlevel='0') {
	if($type == '') { $type = "INFO"; }
	if($path == '') { $path = BASE . 'tmp/exponent.log'; }
	if (DEVELOPMENT >= $minlevel) {
		if (is_writable ($path) || !file_exists($path)) {
			if (!$log = fopen ($path, "ab")) {
				eDebug(gt("Error opening log file for writing."));
			} else {
				if (fwrite ($log, $type . ": " . $var . "\r\n") === FALSE) {
					eDebug(gt("Error writing to log file")." (".$path.").");
				}
				fclose ($log);
			}
		} else {
			eDebug(gt("Log file"." (".$path)." ".gt("not writable."));
		}
	}
}

?>