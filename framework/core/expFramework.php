<?php
##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
/** @define "BASE" "../.." */

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
/**
 * Stores the theme framework
 * @var integer $framework
 * @name $framework
 */
$framework = null;

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
 * Stores the list of language specific strings specific to the theme
 * @var array $custom_lang
 * @name $custom_lang
 */
$custom_lang = array();
/**
 * Stores the name of the default language file
 * @var string $default_lang_file
 * @name $default_lang_file
 */
$default_lang_file = '';
/**
 * Stores the name of the language file to be created
 * @var string $target_lang_file
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
 * Stores the page's inline javascript code
 * @var array $js2foot
 * @name $js2foot
 */
$js2foot = array();
/**
 * Stores the yui3 javascript files list
 * @var array $yui3js
 * @name $yui3js
 */
$yui3js = false;
/**
 * Stores the jquery javascript files list
 * @var array $jqueryjs
 * @name $jqueryjs
 */
$jqueryjs = array();
/**
 * Stores the twitter bootstrap javascript file list
 * @var array $bootstrapjs
 * @name $bootstrapjs
 */
$bootstrapjs = array();
/**
 * Stores the 'other' javascript files list
 * @var array $expJS
 * @name $expJS
 */
$expJS = array();

// expCSS
/**
 * Stores the user's less global variables
 * @var array $less_vars
 * @name $less_vars
 */
$less_vars = array();
/**
 * Stores the user's css files to load first
 * @var array $css_primer
 * @name $css_primer
 */
$css_primer = array();
/**
 * Stores the user's css core/system files
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

// page header level for smart headings
const HEADER_LEVEL = ['h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6'];
$page_heading_top = false;
$page_main_section = false;

/**
 * Main module action/display logic/routine; initializes/checks controller before calling action/method
 *
 * @param array $parms
 *
 * @return bool|mixed|string
 * @throws ReflectionException
 */
function renderAction(array $parms=array()) {
    global $user, $db;

    //Get some info about the controller
//    $baseControllerName = expModules::getControllerName($parms['controller']);
    $fullControllerName = expModules::getControllerClassName($parms['controller']);
    if (expModules::controllerExists($fullControllerName)) {
        $controllerClass = new ReflectionClass($fullControllerName);
    } else {
        return sprintf(gt("The module '%s' was not found in the system"), $parms['controller']);
    }

    if (isset($parms['view'])) $parms['view'] = urldecode($parms['view']);
    // Figure out the action to use...if the specified action doesn't exist then we look for the showall action.
    if ($controllerClass->hasMethod($parms['action'])) {
        $action = $parms['action'];
        /* TODO:  Not sure if we need to check for private methods to be here. FJD
		$meth = $controllerClass->getMethod($action);
        if ($meth->isPrivate()) expQueue::flashAndFlow('error', gt('The requested action could not be performed: Action not found'));*/
    } elseif ($controllerClass->hasMethod('showall')) {
        //note every invalid command gets converted to 'showall'
        if ($parms['controller'] === 'cart' && $parms['action'] === 'cart.cgi') {
            flash('error', gt("This action does not exist"));
            notfoundController::handle_not_found();
            expHistory::returnTo('viewable');
        }
        $parms['action'] = 'showall';
        $action = 'showall';
    } else {
        expQueue::flashAndFlow('error', gt('The requested action could not be performed: Action not found'));
    }

    // initialize the controller.
    $src = isset($parms['src']) ? $parms['src'] : null;
    if (!expJavascript::inAjaxAction() && ($action === 'show' || $action === 'showall') && isset($parms['view'])) {
        if (stripos($parms['view'], $action . '_') !== 0 && $parms['view'] !== $action) {
            unset($parms['view']);
        }
    }
    $controller = new $fullControllerName($src, $parms);

    //Set up the correct template to use for this action
    global $template;
    $view = !empty($parms['view']) ? $parms['view'] : $action;
    $template = expTemplate::get_template_for_action($controller, $view, $controller->loc);

    //setup default model(s) for this controller's actions to use
    foreach ($controller->getModels() as $model) {
        $controller->$model = new $model(null,false,false);   //added null,false,false to reduce unnecessary queries. FJD
        // flag for needing approval check
        if ($controller->$model->supports_revisions && ENABLE_WORKFLOW) {
            if (!expPermissions::check('approve', $controller->loc)) {
                $controller->$model->needs_approval = true;
            } elseif (expTheme::inPreview()) {
                $controller->$model->needs_approval = true;
            }
        }
    }

    //if this controller is being called by a container then we should have a module title.
    if (isset($parms['moduletitle'])) {
        $template->assign('moduletitle', $parms['moduletitle']);
    } else {
        $title = new stdClass();
        $title->mod = $controller->loc->mod;
        $title->src = $controller->loc->src;
        $title->int = '';
        $template->assign('moduletitle', $db->selectValue('container', 'title', "internal='".serialize($title)."'"));
    }

    // check the perms for this action
    $perms = $controller->permissions_all();

    $common_action = null;
    // action convention for controllers that manage more than one model (datatype).
    // if you preface the name action name with a common crud action name we can check perms on
    // it with the developer needing to specify any...better safe than sorry.
    // i.e if the action is edit_mymodel it will be checked against the edit permission
    if (stripos($parms['action'], '_') !== false)
        $parts = explode("_", $parms['action']);
    else
        $parts = preg_split('/(?=[A-Z])/', $parms['action']);  // account for actions with camelCase action/perm such as editItem
    $common_action = isset($parts[0]) ? $parts[0] : null;
    // we have to treat the update permission a little different..it's tied to the create/edit
    // permissions.  Really the only way this will fail will be if someone bypasses the perm check
    // on the edit form somehow..like a hacker trying to bypass the form and just submit straight to
    // the action. To safeguard, we'll catch if the action is update and change it either to create or
    // edit depending on whether an id param is passed to. that should be sufficient.
    if ($parms['action'] === 'update' || $common_action === 'update') {
        $perm_action = (!isset($parms['id']) || $parms['id'] == 0) ? 'create' : 'edit';
    } elseif (($parms['action'] === 'edit' || $common_action === 'edit') && (!isset($parms['id']) || $parms['id'] == 0)) {
        $perm_action = 'create';
    } elseif ($parms['action'] === 'saveconfig') {
        $perm_action = 'configure';
    } else {
        $perm_action = $parms['action'];
    }

    // Here is where we check for ownership of an item and 'create' perm
    if (($parms['action'] === 'edit' || $parms['action'] === 'update' || $parms['action'] === 'delete' ||
        $common_action === 'edit' || $common_action === 'update' || $common_action === 'delete') && !empty($parms['id'])) {
        $theaction = !empty($common_action) ? $common_action : $parms['action'];
        $owner = $db->selectValue($controller->model_table, 'poster', 'id=' . $parms['id']);
        if ($owner == $user->id && !expPermissions::check($theaction, $controller->loc) && expPermissions::check('create', $controller->loc)) {
            $perm_action = 'create';
        }
    }

    if (!DISABLE_PRIVACY) {
        // check to see if it's on a private page and we shouldn't see it
        if ($perm_action === 'showall' || $perm_action === 'show' || $perm_action === 'downloadfile' || $common_action === 'showall' || $common_action === 'show' || $common_action === 'downloadfile') {
            $loc = null;
            if (!empty($parms['src'])) {
                $loc = expCore::makeLocation($parms['controller'], $parms['src']);
            } elseif (!empty($parms['id']) || !empty($parms['title']) || !empty($parms['sef_url'])) {
                if (!empty($parms['id'])) {
                    $record = new $controller->basemodel_name($parms['id']);
                } elseif (!empty($parms['title'])) {
                    $record = new $controller->basemodel_name($parms['title']);
                } elseif (!empty($parms['sef_url'])) {
                    $record = new $controller->basemodel_name($parms['sef_url']);
                }
                if (!empty($record->location_data)) $loc = expUnserialize($record->location_data);
            }
            if (!empty($loc)) {
                $section = new section();
                $sectionref = new sectionref();
                $container = new container();
                $secref = $sectionref->find('first',"module='".$parms['controller']."' AND source='" . $loc->src . "'");
                if (!empty($secref->section)) {
                    $page = $section->find('first','id=' . $secref->section);  // only one page can have the section id#
                    $module = $container->find('first',"internal='" . serialize($loc) . "'");  // only one container can have the internal == location
                    if ($page !== null && $module !== null && !empty($page->id) && (empty($page->public) || !empty($module->is_private))) {
                        // we've found the page and module/container and it's either a private page or private module
                        if (!expPermissions::check('view',expCore::makeLocation('navigation', $page->id))) {
                            if (expTheme::inAction()) {
                                flash('error', gt("You don't have permission to view that item"));
                                notfoundController::handle_not_authorized();
                                expHistory::returnTo('viewable');
                            } else {
                                return false;
                            }
                        }
                    }
                }
            }
        }
    }

    // deal with lower case only to prevent hacking reflection function names
    $lc_perms = array_change_key_case($perms);
    $lc_perm_action = strtolower($perm_action);
    $lc_common_action = strtolower($common_action);
    //FIXME? if the assoc $perm doesn't exist, the 'action' will ALWAYS be allowed, e.g., default is to allow action
    if (array_key_exists($lc_perm_action, $lc_perms)) {
        if (!expPermissions::check($perm_action, $controller->loc)) {
            if (expTheme::inAction()) {
                flash('error', gt("You don't have permission to")." ".$lc_perms[$lc_perm_action]);
//                notfoundController::handle_not_authorized();
                expHistory::returnTo('viewable');
            } else {
                return false;
            }
        }
    } elseif (array_key_exists($lc_common_action, $lc_perms)) {
        if (!expPermissions::check($common_action, $controller->loc)) {
            if (expTheme::inAction()) {
                flash('error', gt("You don't have permission to")." ".$lc_perms[$lc_common_action]);
//                notfoundController::handle_not_authorized();
                expHistory::returnTo('viewable');
            } else {
                return false;
            }
        }
    } elseif (array_key_exists($lc_perm_action, $controller->requires_login)) {
        // check if the action requires the user to at least be logged in
        if (!$user->isLoggedIn()) {
            $msg = empty($controller->requires_login[$lc_perm_action]) ? gt("You must be logged in to perform this action") : gt($controller->requires_login[$lc_perm_action]);
            flash('error', $msg);
//            notfoundController::handle_not_authorized();
            expHistory::redirecto_login();
        }
    } elseif (array_key_exists($lc_common_action, $controller->requires_login)) {
        // check if the common action requires the user to at least be logged in
        if (!$user->isLoggedIn()) {
            $msg = empty($controller->requires_login[$lc_common_action]) ? gt("You must be logged in to perform this action") : gt($controller->requires_login[$lc_common_action]);
            flash('error', $msg);
//            notfoundController::handle_not_authorized();
            expHistory::redirecto_login();
        }
    }

    // register this controllers permissions to the view for in view perm checks
    $template->register_permissions(array_keys($perms), $controller->loc);

    // globalizing $user inside all templates
    $template->assign('user', $user);

    // lastly, run the action which can also override the above assignments
    if (!empty($parms['recycled'])) {
        $controller->config['add_source'] = true;
        $controller->config['aggregate'] = array();
    }
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
    //FIXME this works by making assumptions
    $start_pos = stripos($controller_name, 'controller');
    if ($start_pos === false) {
        return false;
    } else {
        return substr($controller_name, 0, $start_pos);
    }
}

/**
 * @deprecated 2.3.3 moved to expTemplate subsystem
 * @param $view
 * @param $loc
 * @param string $controllername
 *
 * @return controllertemplate
 */
function get_common_template($view, $loc, $controllername='') {
    expCore::deprecated('expTemplate::get_common_template()', array($view, $loc, $controllername));
    return expTemplate::get_common_template($view, $loc, $controllername);

    $controller = new stdClass();
    $controller->baseclassname = empty($controllername) ? 'common' : $controllername;
    $controller->loc = $loc;

    $themepath = BASE . 'themes/' . DISPLAY_THEME . '/modules/common/views/' . $controllername . '/' . $view . '.tpl';
    $basenewuipath = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.newui.tpl';
    $basepath = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.tpl';

    if (bs(true)) {
        $basebstrap3path = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.bootstrap3.tpl';
        $basebstrappath = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.bootstrap.tpl';
        if (file_exists($themepath)) {
            return new controllertemplate($controller, $themepath);
        } elseif (bs3(true) && file_exists($basebstrap3path)) {
            return new controllertemplate($controller, $basebstrap3path);
        } elseif (file_exists($basebstrappath)) {
            return new controllertemplate($controller, $basebstrappath);
        } elseif(newui() && file_exists($basenewuipath)) {  //FIXME is this the correct sequence spot?
            return new controllertemplate($controller,$basenewuipath);
        } elseif (file_exists($basepath)) {
            return new controllertemplate($controller, $basepath);
        } else {
            return new controllertemplate($controller, BASE.'framework/modules/common/views/scaffold/blank.tpl');
        }
    } else {
        if (file_exists($themepath)) {
            return new controllertemplate($controller,$themepath);
        } elseif (newui() && file_exists($basenewuipath)) {
            return new controllertemplate($controller,$basenewuipath);
        } elseif(file_exists($basepath)) {
            return new controllertemplate($controller,$basepath);
        } else {
            return new controllertemplate($controller, BASE.'framework/modules/common/views/scaffold/blank.tpl');
        }
    }
}

/**
 * @deprecated 2.3.3 moved to expTemplate subsystem
 * @param $controller
 * @param $loc
 *
 * @return array
 */
function get_config_templates($controller, $loc) {
    expCore::deprecated('expTemplate::get_config_templates()', array($controller, $loc));
    return expTemplate::get_config_templates($controller, $loc);

//    global $db;

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
    $common_views = expTemplate::find_config_views($commonpaths, $controller->remove_configs);
    foreach ($common_views as $key=>$value) {
        $common_views[$key]['name'] = gt($value['name']);
    }
    $moduleconfig = array();
    if (!empty($common_views['module'])) $moduleconfig['module'] = $common_views['module'];
    unset($common_views['module']);

    // get the config views for the module
    $module_views = expTemplate::find_config_views($modpaths);
    foreach ($module_views as $key=>$value) {
        $module_views[$key]['name'] = gt($value['name']);
    }

    // look for a config form for this module's current view
//    $controller->loc->mod = expModules::getControllerClassName($controller->loc->mod);
    //check to see if hcview was passed along, indicating a hard-coded module
//    if (!empty($controller->params['hcview'])) {
//        $viewname = $controller->params['hcview'];
//    } else {
//        $viewname = $db->selectValue('container', 'view', "internal='".serialize($controller->loc)."'");
//    }
//    $viewconfig = $viewname.'.config';
//    foreach ($modpaths as $path) {
//        if (file_exists($path.'/'.$viewconfig)) {
//            $fileparts = explode('_', $viewname);
//            if ($fileparts[0]=='show'||$fileparts[0]=='showall') array_shift($fileparts);
//            $module_views[$viewname]['name'] = ucwords(implode(' ', $fileparts)).' '.gt('View Configuration');
//            $module_views[$viewname]['file'] =$path.'/'.$viewconfig;
//        }
//    }

    // sort the views highest to lowest by filename
    // we are reverse sorting now so our array merge
    // will overwrite property..we will run array_reverse
    // when we're finished to get them back in the right order
    krsort($common_views);
    krsort($module_views);

    if (!empty($moduleconfig)) $common_views = array_merge($common_views, $moduleconfig);
    $views = array_merge($common_views, $module_views);
    $views = array_reverse($views);

    return $views;
}

/**
 * @deprecated 2.3.3 moved to expTemplate subsystem
 * @param array $paths
 * @param array $excludes
 *
 * @return array
 */
function find_config_views($paths=array(), $excludes=array()) {
    expCore::deprecated('expTemplate::find_config_views()', array($paths, $excludes));
    return expTemplate::find_config_views($paths, $excludes);

    $views = array();
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl' && substr($file, -14) != '.bootstrap.tpl' && substr($file, -15) != '.bootstrap3.tpl' && substr($file, -10) != '.newui.tpl') {
                    $filename = substr($file, 0, -4);
                    if (!in_array($filename, $excludes)) {
                        $fileparts = explode('_', $filename);
                        $views[$filename]['name'] = ucwords(implode(' ', $fileparts));
                        $views[$filename]['file'] = $path.'/'.$file;
                        if ((bs(true)) && file_exists($path.'/'.$filename.'.bootstrap.tpl')) {
                            $views[$filename]['file'] = $path . '/' . $filename . '.bootstrap.tpl';
                        }
                        if (bs3() && file_exists($path.'/'.$filename.'.bootstrap3.tpl')) {
                            $views[$filename]['file'] = $path.'/'.$filename.'.bootstrap3.tpl';
                        }
                        if (newui() && file_exists($path.'/'.$filename.'.newui.tpl')) {  //FIXME newui take priority
                            $views[$filename]['file'] = $path.'/'.$filename.'.newui.tpl';
                        }
                    }
                }
            }
        }
    }

    return $views;
}

/**
 * @deprecated 2.3.3 moved to expTemplate subsystem
 * @param $controller
 * @param $action
 * @param null $loc
 *
 * @return controllertemplate
 */
function get_template_for_action($controller, $action, $loc=null) {
    expCore::deprecated('expTemplate::get_template_for_action()', array($controller, $action, $loc));
    expTemplate::get_template_for_action($controller, $action, $loc);

    // set paths we will search in for the view
    $themepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$action.'.tpl';
    $basepath = $controller->viewpath.'/'.$action.'.tpl';
    $newuithemepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$action.'.newui.tpl'; //FIXME shoudl there be a theme newui variation?
    $basenewuipath = $controller->viewpath.'/'.$action.'.newui.tpl';

    // the root action will be used if we don't find a view for this action and it is a derivative of
    // action.  i.e. showall_by_tags would use the showall.tpl view if we do not have a view named
    // showall_by_tags.tpl
    $root_action = explode('_', $action);
    $rootthemepath = BASE . 'themes/' . DISPLAY_THEME . '/modules/' . $controller->relative_viewpath . '/' . $root_action[0] . '.tpl';
    $rootbasepath = $controller->viewpath . '/' . $root_action[0] . '.tpl';

    if (newui()) {
        if (file_exists($newuithemepath)) {
            return new controllertemplate($controller, $newuithemepath);
        } elseif (file_exists($basenewuipath)) {
            return new controllertemplate($controller, $basenewuipath);
        }
    }
    if (bs(true)) {
        $rootbstrap3path = $controller->viewpath . '/' . $root_action[0] . '.bootstrap3.tpl';
        $basebstrap3path = $controller->viewpath . '/' . $action . '.bootstrap3.tpl';
        $rootbstrappath = $controller->viewpath . '/' . $root_action[0] . '.bootstrap.tpl';
        $basebstrappath = $controller->viewpath . '/' . $action . '.bootstrap.tpl';
        if (file_exists($themepath)) {
            return new controllertemplate($controller, $themepath);
        } elseif (bs3(true) && file_exists($basebstrap3path)) {
            return new controllertemplate($controller, $basebstrap3path);
        } elseif (file_exists($basebstrappath)) {
            return new controllertemplate($controller, $basebstrappath);
        } elseif (file_exists($basepath)) {
            return new controllertemplate($controller, $basepath);
        } elseif ($root_action[0] != $action) {
            if (file_exists($rootthemepath)) {
                return new controllertemplate($controller, $rootthemepath);
            } elseif (bs3(true) && file_exists($rootbstrap3path)) {
                return new controllertemplate($controller, $rootbstrap3path);
            } elseif (file_exists($rootbstrappath)) {
                return new controllertemplate($controller, $rootbstrappath);
            } elseif (file_exists($rootbasepath)) {
                return new controllertemplate($controller, $rootbasepath);
            }
        }
    } else {
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
    }

    // if we get here it means there were no views for the this action to be found.
    // we will check to see if we have a scaffolded version or else just grab a blank template.
    if (file_exists(BASE . 'framework/modules/common/views/scaffold/' . $action . (newui()?'.newui':'') . '.tpl')) {
        return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/' . $action . (newui()?'.newui':'') . '.tpl');
    } else {
        return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/blank.tpl');
    }
}

/**
 * @deprecated 2.3.3 moved to expTemplate subsystem
 * @param $ctl
 * @param $action
 * @param $human_readable
 *
 * @return array
 */
function get_action_views($ctl, $action, $human_readable) {
    expCore::deprecated('expTemplate::get_action_views()', array($ctl, $action, $human_readable));
    expTemplate::get_action_views($ctl, $action, $human_readable);

    // setup the controller
//    $controllerName = expModules::getControllerClassName($ctl);
//    $controller = new $controllerName();
    $controller = expModules::getController($ctl);

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
                if (is_readable($path.'/'.$file) && substr($file, -4) === '.tpl' && substr($file, -14) !== '.bootstrap.tpl' && substr($file, -15) !== '.bootstrap3.tpl' && substr($file, -10) !== '.newui.tpl') {
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
 * @deprecated 2.3.3 moved to expTemplate subsystem
 */
function get_filedisplay_views() {
    expCore::deprecated('expTemplate::get_filedisplay_views()');
    expTemplate::get_filedisplay_views();

    $paths = array(
        BASE.'framework/modules/common/views/file/',
        BASE.'themes/'.DISPLAY_THEME.'modules/common/views/file/',
    );

    $views = array();
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($path.'/'.$file) && substr($file, -4) === '.tpl' && substr($file, -14) !== '.bootstrap.tpl' && substr($file, -15) !== '.bootstrap3.tpl' && substr($file, -10) !== '.newui.tpl') {
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
    if ($serial_str === 'Array' || is_null($serial_str))
        return null;  // empty array string??
    if (is_array($serial_str) || is_object($serial_str))
        return $serial_str;  // already unserialized
//    $out1 = @preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
//    $out1 = preg_replace_callback(
//        '!s:(\d+):"(.*?)";!s',
//        create_function ('$m',
//            '$m_new = str_replace(\'"\',\'\"\',$m[2]);
//            return "s:".strlen($m_new).\':"\'.$m_new.\'";\';'
//        ),
//        $serial_str );
    $out = preg_replace_callback(
        '!s:(\d+):"(.*?)";!s',
        function ($m) {
            $m_new = str_replace('"','\"',$m[2]);
            return "s:".strlen($m_new).':"'.$m_new.'";';
        }, $serial_str );
//    if ($out1 !== $out) {
//        eDebug('problem:<br>'.$out.'<br>'.$out1);
//    }
    $out2 = @unserialize($out);
    // list of fields with rich text
    $stripList = array(
        'moduledescription',
        'description',
        'report_desc',
        'report_def',
        'report_def_showall',
        'response',
        'auto_respond_body',
        'ecomheader',
        'ecomfooter',
        'cart_description_text',
        'policy',
        'checkout_message_top',
        'checkout_message_bottom',
        'message'
    );
    if (is_array($out2)) {
        foreach ($stripList as $strip) {
            if (!empty($out2[$strip])) {  // work-around for links in rich text
                $out2[$strip] = stripslashes($out2[$strip]);
            }
        }
    } elseif (is_object($out2) && $out2 instanceof \htmlcontrol) {
        $out2->html = stripslashes($out2->html);
    }
    if ($out2 === false && !empty($out)) {
        $out2 = $out;
    }
    return $out2;
}

/**
 *  callback when the buffer gets flushed. Any processing on the page output
 * just before it gets rendered to the screen should happen here.
 * @param $buffer
 * @return mixed
 */
function expProcessBuffer($buffer) {
     global $cssForHead;

    return (str_replace("<!-- MINIFY REPLACE -->", $cssForHead, $buffer));
}

/**
 * Ensure we have a valid html 'id' attribute
 *
 * @param $id
 * @param string $value
 *
 * @return mixed
 */
function createValidId($id, $value='') {
    $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");  //FIXME do we need to update this to HTML5 and only include the space?
    if (strpos($id, '[]') !== false)
        $id .= $value;
    $new_id = str_replace($badvals, "_", trim($id));
    return $new_id;
}

function curPageURL() {
    if (expJavascript::inAjaxAction()) {
        $new_request = $_REQUEST;
        unset($new_request['ajax_action']);
        if ($new_request['controller'] === 'store' && $new_request['action'] === 'edit')
            unset($new_request['view']);
        $pageURL = makeLink($new_request);
    } else {
        $pageURL = 'http';
        if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
    }
    return $pageURL;
}

/**
 * Return status of e-commerce
 */
function ecom_active() {
    global $db;

    return ($db->selectValue('modstate', 'active', 'module=\'store\'') ||
        $db->selectValue('modstate', 'active', 'module=\'eventregistration\'') ||
        $db->selectValue('modstate', 'active', 'module=\'donation\'') ||
        FORCE_ECOM);
}

/**
 * Return the current framework
 *
 * @return bool
 */
function framework() {
    global $framework;

    return $framework;
}

/**
 * Is the current framework Bootstrap v2 based?
 *
 * @param bool $strict
 *
 * @return bool
 */
function bs2($strict = false) {
    global $framework;

    return $framework === 'bootstrap';
}

/**
 * Is the current framework Bootstrap v3 based?
 *
 * @param bool $strict must be bootstrap3 and NOT newui
 * @return bool
 */
function bs3($strict = false) {
    global $framework;

    if ($framework === 'bootstrap3') {
        return true;
    }
    if ($framework === 'newui' && !$strict) {
        return true;
    }
    return false;
}

/**
 * Is the current framework Bootstrap v4 based?
 *
 * @param bool $strict must be bootstrap4
 * @return bool
 */
function bs4($strict = false) {
    global $framework;

    return $framework === 'bootstrap4';
}

/**
 * Is the current framework Bootstrap v5 based?
 *
 * @param bool $strict must be bootstrap5
 * @return bool
 */
function bs5($strict = false) {
    global $framework;

    return $framework === 'bootstrap5';
}

/**
 * Is the current framework Bootstrap based?
 *
 * @param bool $strict must be bootstrap 2 or 3 or 4 or 5 and NOT newui
 * @return bool
 */
function bs($strict = false) {
    global $framework;

    if ($framework === 'bootstrap5' || $framework === 'bootstrap4' || $framework === 'bootstrap3' || $framework === 'bootstrap') {
        return true;
    }
    if ($framework === 'newui' && !$strict) {
        return true;
    }
    return false;
}

/**
 * Is the current framework NEWUI and NOT a Bootstrap framework
 *
 * @return bool
 */
function newui() {
    global $framework;

    return $framework === 'newui';
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
 * called from exponent.php as the ajax error handler
 *
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
function handleErrors($errno, $errstr, $errfile, $errline) {
    if (DEVELOPMENT > 0 && AJAX_ERROR_REPORTING == 1) {
        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
                $msg = 'PHP Error('.$errno.'): ';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $msg = 'PHP Warning('.$errno.'): ';
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $msg = 'PHP Notice('.$errno.'): ';
                break;
            default:
                return;  // we really don't want other issues printed
                $msg = 'PHP Issue('.$errno.'): ';
                break;
        }
        $msg .= $errstr;
        $msg .= !empty($errfile) ? ' in file '.$errfile : "";
        $msg .= !empty($errline) ? ' on line '.$errline : "";
        // send to the debug output
        eDebug($msg);
    }
}

/**
 * dumps the passed variable to screen/log, but only if in development mode
 *
 * @param mixed $var the variable to dump
 * @param bool $halt if set to true will halt execution
 * @param bool $disable_log if set to true will disable logging and force to screen
 * @return void
 */
function eDebug($var, $halt=false, $disable_log=false){
	if (DEVELOPMENT) {
        if (LOGGER && !$disable_log) {
//            if(is_array($var) || is_object($var)) {
//                $pvar = print_r($var, true);
//            } else {
//                $pvar = $var;
//            }
//            echo("<script>YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {Y.log('".json_encode($pvar)."','info','exp')});;</script>");
            eLog($var, gt('DEBUG'));
        } else {
            if (file_exists(BASE . 'external/kint.phar')) {
                require_once BASE . 'external/kint.phar';
                d($var);  // kint v3 & v4
            } elseif (file_exists(BASE . 'external/kint-2.2/build/kint.php')) {
                require_once BASE . 'external/kint-2.2/build/kint.php';
                d($var);  // kint v2
            } elseif (file_exists(BASE . 'external/kint/Kint.class.php')) {
                require_once BASE . 'external/kint/Kint.class.php';
                d($var);  // kint v1
            } else {
                echo "<pre>";
                print_r($var);
                echo "</pre>";
            }
        }

		if ($halt) die();
	}
}

/**
 * dumps the passed variable to a log, but only if in development mode
 *
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
				eDebug(gt("Error opening log file for writing."), false, true);
			} else {
                if(is_array($var) || is_object($var)) {
//                    $pvar = print_r($var, true);
                    $pvar = json_encode($var, true);  // json is easier to deal with as data
                } else {
                    $pvar = $var;
                }
				if (fwrite ($log, $type . ": " . $pvar . "\r\n") === FALSE) {
					eDebug(gt("Error writing to log file")." (".$path.").", false, true);
				}
				fclose ($log);
			}
		} else {
			eDebug(gt("Log file"." (".$path)." ".gt("not writable."), false, true);
		}
	}
}

/**
 * Shortcut function to get a phpThumb thumbnail
 *
 * @param $src
 * @return string
 */
function get_thumbnail($src) {
    global $PHPTHUMB_CONFIG;
    require_once(BASE . "external/phpThumb/phpThumb.config.php");

    $params = explode('&', $src);
    if (isset($params['id'])) {
        // Since bootstrap doesn't setup the session we need to define this
        // otherwise the expFile can't find it's table desc from cache.
        // Initialize the Database Subsystem
        $db = expDatabase::connect(DB_USER,DB_PASS,DB_HOST.':'.DB_PORT,DB_NAME,'',false,BASE . 'tmp/thumb_sql.log');

        $file_obj = new expFile((int)($params['id']));
        //$params['src'] = "/" . $file_obj->directory.$file_obj->filename;
    //    $params['src'] = $file_obj->path;
        array_unshift($params, array('src' => $file_obj->path_relative));  // fix for using v1.7.12+

        unset(
            $params['id'],
            $params['square']
        );
        $src = implode('&', $params);
    }

    return htmlspecialchars(phpThumbURL($src, URL_FULL . "external/phpThumb/phpThumb.php"));
}

/**
 * Determine whether we are secure
 *
 * @return bool
 */
function isSSL() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}

/**
 * Equivalent to `date_format_to( $format, 'date' )`
 *
 * @param string $strf_format A `strftime()` date/time format
 * @return string A `date()` date/time format
 */
function strftime_to_date_format( $strf_format ) {
	return expDateTime::date_format_to( $strf_format, 'date' );
}

/**
 * Equivalent to `convert_datetime_format_to( $format, 'strf' )`
 *
 * @param string $date_format A `date()` date/time format
 * @return string A `strftime()` date/time format
 */
function date_to_strftime_format( $date_format ) {
	return expDateTime::date_format_to( $date_format, 'strf' );
}

/**
 * Converts an strftime format to a moment.js format
 *
 * @param string $strf_format A `strftime()` date/time format
 * @return string A `moment.js` date/time format
 */
function strftime_to_moment_format( $strf_format ) {
	return expDateTime::convertPhpToJsMomentFormat(expDateTime::date_format_to( $strf_format, 'date' ));
}

/**
 * PHP v8.1+ friendly stripslashes() command
 *
 * @param $str string to strip slashes from
 * @return mixed|string
 */
function expStripSlashes($str) {
    if (empty($str)) {
        return $str;
    }
    return stripslashes($str);
}

?>