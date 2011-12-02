<?php
##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc. and Contributors
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
 * @global array $available_controllers
 * @name $available_controllers
 */
$available_controllers = array();

// expTheme
/**
 * Stores the theme validation flags
 * @global array $validateTheme does theme have header & footer
 * @name $validateTheme
 */
$validateTheme = array("headerinfo"=>false,"footerinfo"=>false);

// expLang
/**
 * Stores the list of language strings for the current language
 * @global array $cur_lang
 * @name $cur_lang
 */
$cur_lang = array();
/**
 * Stores the list of language strings for the default language (English - US)
 * @global array $default_lang
 * @name $default_lang
 */
$default_lang = array();
/**
 * Stores the name of the default language file
 * @global array $default_lang_file
 * @name $default_lang_file
 */
$default_lang_file = '';
/**
 * Stores the name of the language file to be created
 * @global array $target_lang_file
 * @name $target_lang_file
 */
$target_lang_file = '';

// expDatabase
/**
 * Stores the exponent database object
 * @global \database $db the exponent database object
 * @name $db
 */
$db = null;

// expHistory
/**
 * Stores the browsing history object
 * @global expHistory $history
 * @name $history
 */
$history = null;

// user model
/**
 * Stores the current user object
 * @global user $user
 * @name $user
 */
$user = null;
/**
 * This global array belongs exclusively to the user model, and is used to cache
 * users as they are retrieved, to help out with performance when doing a lot of
 * work with user accounts and profile information.
 * @global array $SYS_USERS_CACHE
 * @name $SYS_USERS_CACHE
 */
$SYS_USERS_CACHE = array();

// expRouter
/**
 * Stores the routing/link/url object
 * @global expRouter $router
 * @name $router
 */
$router = null;
/**
 * Stores the routing/link/url object
 * @global section $sectionObj
 * @name $sectionObj
 */
$sectionObj = null;

// expCore
/**
 * Stores the list of sections/pages for the site
 * @global array $sections
 * @name $sections
 */
$sections = array();
// expPermissions
/**
 * Stores the permission data for the current user.
 * This should not be modified by anything outside of the permissions subsystem.
 * @global array $exponent_permissions_r
 * @name $exponent_permissions_r
 */
$exponent_permissions_r = array();

// expJavascript
/**
 * Stores the user's javascript files
 * @global array $userjsfiles
 * @name $userjsfiles
 */
$userjsfiles = array();
/**
 * Stores the user's javascript files
 * @global array $js2foot
 * @name $js2foot
 */
$js2foot = array();
//$yui2js = array();
/**
 * Stores the user's javascript files
 * @global array $yui3js
 * @name $yui3js
 */
$yui3js = array();
/**
 * Stores the user's javascript files
 * @global array $expJS
 * @name $expJS
 */
$expJS = array();

// expCSS
/**
 * Stores the user's css files
 * @global array $css_primer
 * @name $css_primer
 */
$css_primer = array();
/**
 * Stores the user's css files
 * @global array $css_core
 * @name $css_core
 */
$css_core = array();
/**
 * Stores the user's css files
 * @global array $css_links
 * @name $css_links
 */
$css_links = array();
/**
 * Stores the user's css files
 * @global array $css_theme
 * @name $css_theme
 */
$css_theme = array();
/**
 * Stores the user's css files
 * @global array $css_inline
 * @name $css_inline
 */
$css_inline = array();
/**
 * Stores the user's css files
 * @global array $head_config
 * @name $head_config
 */
$head_config = array();
/**
 * Stores the user's css files
 * @global string $jsForHead
 * @name $jsForHead
 */
$jsForHead = "";
/**
 * Stores the user's css files
 * @global string $cssForHead
 * @name $cssForHead
 */
$cssForHead = "";

// expTemplate
/**
 * Stores the global template
 * @global \basetemplate $template
 * @name $template
 */
$template = null;

// expTimer
/**
 * Stores the timer
 * @global expTimer $timer
 * @name $timer
 */
$timer = null;

// e-commerce
/**
 * Stores the order
 * @global \order $order
 * @name $order
 */
$order = null;

function renderAction(array $parms=array()) {
    global $user;
    
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
        if ($meth->isPrivate()) expQueue::flashAndFlow('error', 'The requested action could not be performed: Action not found');*/
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
    // this has to be done after the controller get the template for its actions
    $controller->moduleSelfAwareness();

    //if this controller is being called by a container then we should have a module title.
    if (isset($parms['moduletitle'])) $template->assign('moduletitle', $parms['moduletitle']);

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
    $common_action_name = null;
    if ($parms['action'] == 'update') {
        $permaction = (!isset($parms['id']) || $parms['id'] == 0) ? 'create' : 'edit';
    } elseif ($parms['action'] == 'saveconfig') {
        $permaction = 'configure';
    } else {
        // action convention for controllers that manage more than one model (datatype). 
        // if you preface the name action name with a common crud action name we can check perms on 
        // it with the developer needing to  specify any...better safe than sorry.
        // i.e if the action is edit_mymodel it will be checked against the edit permission
        if (stristr($parms['action'], '_')) $parts = explode("_", $parms['action']);
        $common_action_name = isset($parts[0]) ? $parts[0] : null;
        $permaction = $parms['action'];
    }

    if (array_key_exists($permaction, $perms)) {
        if (!expPermissions::check($permaction, $controller->loc)) {
            if (expTheme::inAction()) {
                flash('error', gt("You don't have permission to")." ".$perms[$permaction]);
                expHistory::returnTo('viewable');
            } else {
                return false;
            }
        }
    } elseif (array_key_exists($common_action_name, $perms)) {
        if (!expPermissions::check($common_action_name, $controller->loc)) {
            if (expTheme::inAction()) {
                flash('error', gt("You don't have permission to")." ".$perms[$common_action_name]);
                expHistory::returnTo('viewable');
            } else {
                return false;
            }
        }
    } elseif (array_key_exists($permaction, $controller->requires_login)) {
        // check if the action requires the user to be logged in
        if (!$user->isLoggedIn()) {
            $msg = empty($controller->requires_login[$permaction]) ? gt("You must be logged in to perform this action") : $controller->requires_login[$permaction];
            flash('error', $msg);
            expHistory::redirecto_login();
        }
    } elseif (array_key_exists($common_action_name, $controller->requires_login)) {
        // check if the action requires the user to be logged in
        if (!$user->isLoggedIn()) {
            $msg = empty($controller->requires_login[$common_action_name]) ? gt("You must be logged in to perform this action") : $controller->requires_login[$common_action_name];
            flash('error', $msg);
            expHistory::redirecto_login();
        }
    } 
    
    // run the action 
    $controller->$action();
    
    //register this controllers permissions to the view for in view perm checks
    $template->register_permissions(array_keys($perms), $controller->loc);
    
    // pass this controllers config off to the view
    $template->assign('config', $controller->config);
    
    // globalizing $user inside all templates
    $template->assign('user', $user);
    
    //assign the controllers basemodel to the view
    $template->assign('modelname', $controller->basemodel_name);

    if (empty($parms['no_output'])) {
        $template->output();
    } else {
        $html = $template->render();
        return $html;
    }

    //$html = $template->output();
    //return $html;
}

function hotspot($source = null) {
    if (!empty($source)) {
        global $sectionObj;
	    //FIXME there is NO 'page' object
        $page = new section($sectionObj->id);
        $modules = $page->getModulesBySource($source);
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
//  global $flow;
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

function flushFlash() {
    expQueue::flushAllQueues();
}

function handleErrors($errno, $errstr, $errfile, $errline) {
    if (DEVELOPMENT > 0) {
        $msg = "";
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

function show_msg_queue() {
    $queues = expSession::get('flash');
#    if (!empty($queues)) {
        $template = new template('common','_msg_queue');
        $template->assign('queues', expSession::get('flash'));
        $html = $template->render();
#    } else {
#        $html = '';
#    }
    flushFlash();
    return $html;
}

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
    $controller->baseclassname = empty($controllername) ? 'common' : $controllername;
    $controller->relative_viewpath = 'framework/modules-1/common/views'.$controller->baseclassname;
    $controller->loc = $loc;
    
    $basepath = BASE.'framework/modules/common/views/'.$controllername.'/'.$view.'.tpl';
//    $themepath = BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/common/views/'.$controllername.'/'.$view.'.tpl';
    $themepath = BASE.'themes/'.DISPLAY_THEME.'/modules/common/views/'.$controllername.'/'.$view.'.tpl';

    if (file_exists($themepath)) {
        return new controllertemplate($controller,$themepath);
    } elseif(file_exists($basepath)) {
        return new controllertemplate($controller,$basepath);
    } else {
        return new controllertemplate($controller, BASE.'framework/common/views/scaffold/blank.tpl');
    }
}

function get_config_templates($controller, $loc) {
    global $db;
    
    // set paths we will search in for the view
    $commonpaths = array(
        BASE.'framework/modules/common/views/configure',
//        BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/common/views/configure',
        BASE.'themes/'.DISPLAY_THEME.'/modules/common/views/configure',
    );
    
    $modpaths = array(
        $controller->viewpath.'/configure',
//        BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/'.$controller->relative_viewpath.'/configure'
	    BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/configure'
    );
    
    // get the common configuration files    
    $common_views = find_config_views($commonpaths, $controller->remove_configs);
    
    // get the config views for the module
    $module_views = find_config_views($modpaths);
    
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
//    $themepath = BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/'.$controller->relative_viewpath.'/'.$action.'.tpl';
    $themepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$action.'.tpl';

    // the root action will be used if we don't find a view for this action and it is a derivitative of
    // action.  i.e. showall_by_tags would use the showall.tpl view if we do not have a view named
    // showall_by_tags.tpl
    $root_action = explode('_', $action);
    $rootbasepath = $controller->viewpath.'/'.$root_action[0].'.tpl';
//    $rootthemepath = BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/'.$controller->relative_viewpath.'/'.$root_action[0].'.tpl';
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

function get_action_views($ctl, $action, $human_readable) {
    // setup the controller
    $controllerName = expModules::getControllerClassName($ctl);
    $controller = new $controllerName();
    
    // set path information 
    //$basepath = $controller->viewpath;
    //$themepath = BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/'.$controller->relative_viewpath;
    $paths = array(
        $controller->viewpath,
//        BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/'.$controller->relative_viewpath,
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
                            $diplayname = array_shift($fileparts); //shift the action name off the array of words
                            $views[$filename] = ucwords(implode(' ', $fileparts));
                        }
                    }
                }
            }
        }
    }
    
    return $views;
}

function get_filedisplay_views() {
    $paths = array(
        BASE.'framework/modules/common/views/file/',
//        BASE.'themes/'.DISPLAY_THEME_REAL.'modules/common/views/file/',
        BASE.'themes/'.DISPLAY_THEME.'modules/common/views/file/',
    );
    
    $views = array();
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl') {
                    $filename = substr($file, 0, -4);
                    $views[$filename] = $filename;
                }
            }
        }
    }
    
    return $views;
}

function makeLocation($mod=null,$src=null,$int=null) {
        $loc = null;
        $loc->mod = ($mod ? $mod : "");
        $loc->src = ($src ? $src : "");
        $loc->int = ($int ? $int : "");
        return $loc;
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
    $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
    return unserialize($out); 
}

// callback when the buffer gets flushed. Any processing on the page output
// just before it gets rendered to the screen should happen here.
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
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function gt($s){
    return expLang::gettext($s);
}

?>
