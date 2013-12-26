<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * This is the class contqinerController
 *
 * @subpackage Controllers
 * @package Modules
 */

class containerController extends expController {
	public $useractions = array(
        'showall'=>'Group Other Modules',
	);
	public $remove_configs = array(
        'aggregation',
        'categories',
		'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
		'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)
//    public $codequality = 'beta';

    static function displayname() { return gt("Container"); }
    static function description() { return gt("Encapsulates other modules within a formatted container (e.g. columns, tabs, etc...)"); }

	public function showall() {
        global $db, $user, $module_scope, $template;

        $loc = expCore::makeLocation($this->params['controller'],isset($this->params['src'])?$this->params['src']:null,isset($this->params['int'])?$this->params['int']:null);
        $view = !empty($this->params['view']) ? $this->params['view'] : 'showall';
        $action = !empty($this->params['action']) ? $this->params['action'] : 'showall';
        $title = !empty($this->params['moduletitle']) ? $this->params['moduletitle'] : '';
        $clickable_mods = null; // Show all
        $dest = null;
        $singleview = '_container';
        $singlemodule = 'container';
        if (expSession::is_set('source_select') && defined('SELECTOR')) {
            $source_select = expSession::get('source_select');
            $singleview = $source_select['view'];
            $singlemodule = $source_select['module'];
            $clickable_mods = $source_select['showmodules'];
            if (!is_array($clickable_mods)) $clickable_mods = null;
            $dest = $source_select['dest'];
        }

        $container = null;
        $container_key = serialize($loc);
        //TODO we currently don't use the container cache
        $cache = expSession::getCacheValue('containers');
//        if (!isset($this) || !isset($this->_hasParent) || $this->_hasParent == 0) {
            // Top level container.
//        if (!isset($cache['top'][$container_key])) {
            $container = $db->selectObject('container', "internal='" . $container_key . "'");
//            $container = new container("internal='" . $container_key . "'");
            // if container isn't here already, then create it...nested containers
            if ($container == null) {
                $container = new stdClass();
                $container->internal = serialize($loc);
                $container->external = serialize(null);
                $container->title = $title;
                $container->view = $view;
                $container->action = $action;
                $container->id = $db->insertObject($container, 'container');
            }
            $cache['top'][$container_key] = $container;
            expSession::setCacheValue('containers', $cache);
//        } else {
//            $container = $cache['top'][$container_key];
//        }
        if (!defined('PREVIEW_READONLY') || defined('SELECTOR')) $view = empty($container->view) ? $view : $container->view;
//			$title = $container->title;
//        } else flash('error','container $this is set');
        $container->scope = empty($module_scope[$loc->src]["container"]->scope) ? '' : $module_scope[$loc->src]["container"]->scope;

        if ($dest) {
            assign_to_template(array(
                'dest'=> $dest,
            ));
        }
        assign_to_template(array(
            'singleview'=>$singleview,
            'singlemodule'=>$singlemodule,
            'top'=>$container,
            'src'=>$loc->src,
        ));

        $containers = array();

//        if (!isset($cache[$container_key])) {
            foreach ($db->selectObjects('container', "external='" . $container_key . "'") as $c) {
                if ($c->is_private == 0 || expPermissions::check('view', expCore::makeLocation($loc->mod, $loc->src, $c->id))) {
                    $containers[$c->rank] = $c;
                }
            }
            $cache[$container_key] = $containers;
            expSession::setCacheValue('containers', $cache);
//        } else {
//            $containers = $cache[$container_key];
//        }

        $container_template = clone $template;  // cache the container template

        ksort($containers);
        foreach (array_keys($containers) as $i) {
            $location = unserialize($containers[$i]->internal);

            // check to see if this is a controller or module
//            $iscontroller = expModules::controllerExists($location->mod);
            $modclass = expModules::getModuleClassName($location->mod);
            if (class_exists($modclass)) {
                $mod = new $modclass();

                ob_start();
//                $mod->_hasParent = 1;
                if ($containers[$i]->external != 'N;' && $location->mod == 'container') $containers[$i]->hasParent = 1;
//                if ($iscontroller) {
                    renderAction(array('controller'=>$location->mod, 'action'=>$containers[$i]->action, 'src'=>$location->src, 'view'=>$containers[$i]->view, 'moduletitle'=>$containers[$i]->title));
//                } else {
//                    $mod->show($containers[$i]->view, $location, $containers[$i]->title);
//                }
                $containers[$i]->output = trim(ob_get_contents());
                ob_end_clean();

                $containers[$i]->info = array(
                    'module'              => $mod->name(),
                    'source'              => $location->src,
//                    'scope'=>$module_scope[$loc->src]["containermodule"]->scope,
                    'hasContent'          => $mod->hasContent(),
                    'hasSources'          => $mod->hasSources(),
                    'hasViews'            => $mod->hasViews(),
                    'class'               => $modclass,
                    'clickable'           => ($clickable_mods == null || in_array($modclass, $clickable_mods)),
//                    'hasConfig'           => $db->tableExists($modclass . "_config")  //FIXME old school config
                );
            } else {
                $containers[$i]->output = sprintf(gt('The module "%s" was not found in the system'), $location->mod);
                $containers[$i]->info = array(
                    'module'              => sprintf(gt('Unknown: %s'), $location->mod),
                    'source'              => $location->src,
//                    'scope'=>$module_scope[$loc->src]["containermodule"]->scope,
                    'hasContent'          => 0,
                    'hasSources'          => 0,
                    'hasViews'            => 0,
                    'class'               => $modclass,
//                    'hasConfig'           => $db->tableExists($modclass . "_config"),  //FIXME old school config
                    'clickable'           => 0
                );
            }
            $containers[$i]->moduleLocation = $location;

            $cloc = new stdClass();
            $cloc->mod = $loc->mod;
            $cloc->src = $loc->src;
            $cloc->int = $containers[$i]->id;
            $containers[$i]->permissions = array(
                'manage'    => (expPermissions::check('manage', $location) ? 1 : 0),
                'configure' => (expPermissions::check('configure', $location) ? 1 : 0)
            );
        }

        $template = $container_template;
        assign_to_template(array(
            'user'=>$user,
            'containers'=> $containers,
//            'hasParent'=>(isset($this) && isset($this->_hasParent) ? 1 : 0),  // used to see if we need a border
        ));

	}

    public function edit() {
        global $db, $user;

        $loc = expCore::makeLocation($this->params['controller'],isset($this->params['src'])?$this->params['src']:null,isset($this->params['int'])?$this->params['int']:null);
        expHistory::set('editable',array("module"=>"container","action"=>"edit"));
        $container = null;
        if (isset($this->params['id'])) {
        	$container = $db->selectObject('container','id=' . $this->params['id'] );
        } else {
            $container = new stdClass();
        	$container->rank = $this->params['rank'];
        }
        $loc->src = urldecode($loc->src);

        // Initialize Container, in case its null
        $secref = new stdClass();
        if (!isset($container->id)) {
            $secref->description = '';
            $container->view = '';
            $container->internal = expCore::makeLocation();
            $container->title = '';
            $container->rank = $this->params['rank'];
            $container->is_private = 0;
        } else {
            $container->internal = unserialize($container->internal);
            $secref = $db->selectObject('sectionref',"module='".$container->internal->mod."' AND source='".$container->internal->src."'");
        }

        expSession::clearAllUsersSessionCache('containers');

//        global $template;
//        $template->assign('rerank', (isset($this->params['rerank']) ? $this->params['rerank'] : 0) );
//        $template->assign('container',$container);
//        $template->assign('locref',$secref);
//        $template->assign('is_edit', (isset($container->id) ? 1 : 0) );
//        $template->assign('can_activate_modules',$user->is_acting_admin);
//        $template->assign('current_section',expSession::get('last_section'));
        assign_to_template(array(
            'rerank' => (isset($this->params['rerank']) ? $this->params['rerank'] : 0) ,
            'container' => $container,
            'locref' => $secref,
            'is_edit' => (isset($container->id) ? 1 : 0) ,
            'can_activate_modules' => $user->is_acting_admin,
            'current_section' => expSession::get('last_section'),
        ));

        $haveclass = false;
        $mods = array();

        $modules_list = expModules::getActiveControllersList();

        if (!count($modules_list)) { // No active modules
//            $template->assign('nomodules',1);
            assign_to_template(array(
                'nomodules' => 1,
            ));
        } else {
//            $template->assign('nomodules',0);
            assign_to_template(array(
                'nomodules' => 0,
            ));
        }

        //sort($modules_list);

        $js_init = '<script type="text/javascript">';

        foreach ($modules_list as $moduleclass) {
            $modclass = expModules::getModuleClassName($moduleclass);
            $module = new $modclass();

            // Get basic module meta info
            $mod = new stdClass();
            $mod->name = $module->name();
            $mod->author = $module->author();
            $mod->description = $module->description();
    //        $mod->name = $moduleclass::name();
    //        $mod->author = $moduleclass::author();
    //        $mod->description = $moduleclass::description();
            if (isset($container->view) && $container->internal->mod == $moduleclass) {
                $mod->defaultView = $container->view;
            } else $mod->defaultView = DEFAULT_VIEW;

            // Get support flags
            $mod->supportsSources = ($module->hasSources() ? 1 : 0);
            $mod->supportsViews  = ($module->hasViews()   ? 1 : 0);
    //        $mod->supportsSources = ($moduleclass::hasSources() ? 1 : 0);
    //        $mod->supportsViews  = ($moduleclass::hasViews()   ? 1 : 0);

            // Get a list of views
            $mod->views = expTemplate::listModuleViews($moduleclass);
            natsort($mod->views);

            // if (!$haveclass) {
            //  $js_init .=  exponent_javascript_class($mod,'Module');
            //  $js_init .=  "var modules = new Array();\r\n";
            //  $js_init .=  "var modnames = new Array();\r\n\r\n";
            //  $haveclass = true;
            // }
            // $js_init .=  "modules.push(" . exponent_javascript_object($mod,"Module") . ");\r\n";
            // $js_init .=  "modnames.push('" . $moduleclass . "');\r\n";
            $modules[$moduleclass] = $mod;
            $mods[$moduleclass] = $module->name();
    //        $mods[$moduleclass] = $moduleclass::name();
        }
        //$js_init .= "\r\n</script>";

        array_multisort(array_map('strtolower', $mods), $mods);
        if (!array_key_exists($container->internal->mod, $mods) && !empty($container->id)) {
//            $template->assign('error',gt('The module you are trying to edit is inactive. Please contact your administrator to activate this module.'));
            assign_to_template(array(
                'error' => gt('The module you are trying to edit is inactive. Please contact your administrator to activate this module.'),
            ));
        }
//        $template->assign('user',$user);
//        $template->assign('json_obj',json_encode($modules));
//        $template->assign('modules',$mods);
//        $template->assign('loc',$loc);
//        $template->assign('back',expHistory::getLastNotEditable());
        assign_to_template(array(
            'user' => $user,
            'json_obj' => json_encode($modules),
            'modules' => $mods,
            'loc' => $loc,
            'back' => expHistory::getLastNotEditable(),
        ));
    }

    public function update() {
        $this->params['action'] = $this->params['actions'];
        unset($this->params['actions']);
        $this->params['view'] = $this->params['views'];
        unset($this->params['views']);
        $this->params['external'] = serialize($this->loc);
        unset($this->params['module']);

        $hidetitle = $this->params['hidemoduletitle'];
        unset($this->params['hidemoduletitle']);

        $modelname = $this->basemodel_name;
        $this->$modelname->update($this->params);

        $modconfig = new expConfig(expUnserialize($this->$modelname->internal));
        if ($modconfig->id || $hidetitle) {
            $modconfig->config['hidemoduletitle'] = $hidetitle;
            $modconfig->update();
        }

        define('SOURCE_SELECTOR',0);
        define('PREVIEW_READONLY',0); // for mods

        expSession::clearAllUsersSessionCache('containers');
        expHistory::back();
    }

    public function delete_instance($loc = false) {
        global $user;

        if ($user && $user->is_acting_admin == 1) {
            $modelname = $this->basemodel_name;
            $containers = $this->$modelname->find('all',"external='" . serialize($this->loc) . "'");

            foreach ($containers as $container) {
                $container->delete();
            }
        }
    }

    public function getaction() {
        $controller = expModules::getController($this->params['mod']);
        $actions = $controller->useractions;
        // Language-ize the action names
        foreach ($actions as $key=>$value) {
            $actions[$key] = gt($value);
        }
        echo json_encode($actions);
    }

    public function getactionviews() {
        $views = get_action_views($this->params['mod'], $this->params['act'], $this->params['actname']);
        if (count($views) < 1) $views[$this->params['act']] = $this->params['actname'].' - Default View';
        echo json_encode($views);
    }

    static function wrapOutput($modclass, $view, $loc = null, $title = '') {
        global $db;

        $modclass = expModules::getModuleClassName($modclass);
        if (defined('SOURCE_SELECTOR') && strtolower($modclass) != 'containerController') {
            $mod = new $modclass();

            ob_start();
            if (expModules::controllerExists($modclass)) {
                $action = $db->selectValue('container', 'action', "internal='" . serialize($loc) . "'");
                renderAction(array('controller' => $modclass, 'action' => $action, 'view' => $view,'src'=>$loc->src));
//            } else {
//                $mod->show($view, $loc, $title);
            }

            $container = new stdClass();
            $container->output = ob_get_contents();
            ob_end_clean();

            $source_select = expSession::get('source_select');
            $c_view = $source_select['view'];
            $c_module = $source_select['module'];
            $clickable_mods = $source_select['showmodules'];
            if (!is_array($clickable_mods)) $clickable_mods = null;
            $dest = $source_select['dest'];

//            $template = new template($c_module, $c_view, $loc);
            $cmodule = expModules::getController($c_module);
            $template = get_template_for_action($cmodule,$c_view);
            if ($dest) $template->assign('dest', $dest);

            $container->info = array(
                'module'     => $mod->name(),
                'source'     => $loc->src,
                'hasContent' => $mod->hasContent(),
                'hasSources' => $mod->hasSources(),
                'hasViews'   => $mod->hasViews(),
                'class'      => $modclass,
                'clickable'  => ($clickable_mods == null || in_array($modclass, $clickable_mods))
            );

            $template->assign('container', $container);
            $template->output();
        } else {
//            call_user_func(array($modclass, 'show'), $view, $loc, $title);
            renderAction(array('controller'=>$modclass, 'action'=>'showall', 'view'=>$view, 'title'=>$title, 'loc'=>$loc));
        }
    }

}

?>
