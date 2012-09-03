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
/** @define "BASE" "../../.." */

class containermodule {
    static function name() { return 'Container'; }
    static function author() { return 'James Hunt'; }
    static function description() { return 'Contains other modules'; }
    static function hasContent() { return true; }
	static function hasSources() { return true; }
    static function hasViews()   { return true; }
    static function supportsWorkflow() { return false; }
	
	function permissions($internal = '') {
		return array(
			'manage'=>gt('Manage'),
            'configure'=>gt('Configure'),
			'create'=>gt('Create'),
			'edit'=>gt('Edit'),
			'delete'=>gt('Delete'),
		);
	}
	
	function deleteIn($loc) {
		global $user;
		if ($user && $user->is_acting_admin == 1) {
			include_once(BASE . 'framework/core/models-1/container.php');
			
			global $db;
			$containers = $db->selectObjects('container',"external='" . serialize($loc) . "'");
			
			foreach ($containers as $container) {
				self::delete($container);
				$db->delete('container','id='.$container->id);
			}
		}
	}
	
	static function show($view,$loc = null,$title = '') {
		if (empty($view)) $view = "Default";
//		$source_select = array();
		$clickable_mods = null; // Show all
		$dest = null;
		
		$singleview = '_container';
		$singlemodule = 'containermodule';
		if (expSession::is_set('source_select') && defined('SELECTOR')) {
			$source_select = expSession::get('source_select');
			$singleview = $source_select['view'];
			$singlemodule = $source_select['module'];
			$clickable_mods = $source_select['showmodules'];
			if (!is_array($clickable_mods)) $clickable_mods = null;
			$dest = $source_select['dest'];
		}
		
		global $db, $user, $module_scope;
		
		$container = null;
		$container_key = serialize( $loc );
		$cache = expSession::getCacheValue('containermodule');
		if (!isset($this) || !isset($this->_hasParent) || $this->_hasParent == 0) {
			// Top level container.			
			if(!isset($cache['top'][$container_key])) {        		
				$container = $db->selectObject('container',"external='".serialize(null)."' AND internal='".$container_key."'");
				//if container isn't here already, then create it.
	            if ($container == null) {
                    $container = new stdClass();
					$container->external = serialize(null);
					$container->internal = serialize($loc);
					$container->view = $view;
					$container->title = $title;
					$container->id = $db->insertObject($container,'container');
				}
				$cache['top'][$container_key] = $container;
				expSession::setCacheValue('containermodule', $cache);
        	}else{
        		$container = $cache['top'][$container_key];
        	}
			if (!defined('PREVIEW_READONLY') || defined('SELECTOR')) $view = empty($container->view) ? $view : $container->view;
//			$title = $container->title;
		}
        $container->scope = empty($module_scope[$loc->src]["containermodule"]->scope) ? '' : $module_scope[$loc->src]["containermodule"]->scope;

		$template = new template('containermodule',$view,$loc,$cache);
		if ($dest) $template->assign('dest',$dest);
		$template->assign('singleview',$singleview);
		$template->assign('singlemodule',$singlemodule);
		
		$template->assign('top',$container);
        $template->assign('src',$loc->src);

		$containers = array();
       
        if(!isset($cache[$container_key])) {
            foreach ($db->selectObjects('container',"external='" . $container_key . "'") as $c) {
            if ($c->is_private == 0 || expPermissions::check('view',expCore::makeLocation($loc->mod,$loc->src,$c->id))) {
                $containers[$c->rank] = $c;
                }
            }
            $cache[$container_key] = $containers;
            expSession::setCacheValue('containermodule', $cache);
        } else {
                $containers = $cache[$container_key];
        }
 
		ksort($containers);
		foreach (array_keys($containers) as $i) {
			$location = unserialize($containers[$i]->internal);

			// check to see if this is a controller or module
			$iscontroller = expModules::controllerExists($location->mod);
			$modclass = $iscontroller ? expModules::getControllerClassName($location->mod) : $location->mod;

			if (class_exists($modclass)) {
				$mod = new $modclass();
				
				ob_start();
                $mod->_hasParent = 1;
                if ($iscontroller) {
//                    renderAction(array('controller'=>$location->mod, 'action'=>$containers[$i]->action, 'src'=>$location->src, 'view'=>$containers[$i]->view, 'moduletitle'=>$containers[$i]->title));
                    renderAction(array('controller'=>expModules::getControllerName($location->mod), 'action'=>$containers[$i]->action, 'src'=>$location->src, 'view'=>$containers[$i]->view, 'moduletitle'=>$containers[$i]->title));
                } else {
                    $mod->show($containers[$i]->view,$location,$containers[$i]->title);
                }

				$containers[$i]->output = trim(ob_get_contents());
				ob_end_clean();
				
				$containers[$i]->info = array(
					'module'=>$mod->name(),
					'source'=>$location->src,
//                    'scope'=>$module_scope[$loc->src]["containermodule"]->scope,
					'hasContent'=>$mod->hasContent(),
					'hasSources'=>$mod->hasSources(),
					'hasViews'=>$mod->hasViews(),
					'class'=>$modclass,
					'supportsWorkflow'=>($mod->supportsWorkflow()?1:0),
					'workflowPolicy'=>'',
					'workflowUsesDefault'=>0,
					'clickable'=>($clickable_mods == null || in_array($modclass,$clickable_mods)),
					'hasConfig'=>$db->tableExists($modclass."_config")
				);
			} else {
				$containers[$i]->output = sprintf(gt('The module "%s" was not found in the system'),$location->mod);
				$containers[$i]->info = array(
					'module'=>sprintf(gt('Unknown: %s'),$location->mod),
					'source'=>$location->src,
//                    'scope'=>$module_scope[$loc->src]["containermodule"]->scope,
					'hasContent'=>0,
					'hasSources'=>0,
					'hasViews'=>0,
					'class'=>$modclass,
					'supportsWorkflow'=>0,
					'workflowPolicy'=>'',
					'workflowUsesDefault'=>0,
					'hasConfig'=>$db->tableExists($modclass."_config"),
					'clickable'=>0
				);
			}
			$containers[$i]->moduleLocation = $location;
			
			$cloc = new stdClass();
			$cloc->mod = $loc->mod;
			$cloc->src = $loc->src;
			$cloc->int = $containers[$i]->id;
            $location->mod = str_replace('Controller','',$location->mod);
			$containers[$i]->permissions = array(
				'manage'=>(expPermissions::check('manage',$location) ? 1 : 0),
				'configure'=>(expPermissions::check('configure',$location) ? 1 : 0)
			);
		}
	
		$template->assign('user',$user);
		$template->assign('containers',$containers);
		$template->assign('hasParent',(isset($this) && isset($this->_hasParent) ? 1 : 0));
		$template->register_permissions(
			array('manage','create','edit','delete','configure'),
			$loc
		);
		
		$template->output();
	}
	
	function copyContent($oloc,$nloc, $section=0) {
		global $db;
		foreach ($db->selectObjects('container',"external='".serialize($oloc)."'") as $c) {
			unset($c->id);
			$c->external = serialize($nloc);
			
			if (!$c->is_existing == 1) { // Copy over content to a new source
				$oldinternal = unserialize($c->internal);
				$iloc = expCore::makeLocation($oldinternal->mod,'@random'.uniqid(''));
				$c->internal = serialize($iloc);
				$db->insertObject($c,'container');
			
				// Now copy over content
				if (call_user_func(array($oldinternal->mod,'hasContent')) == true) {
					call_user_func(array($oldinternal->mod,'copyContent'),$oldinternal,$iloc);
					// Incrementors!
					expCore::incrementLocationReference($iloc,$section); // SECTION
				}
			} else {
				$db->insertObject($c,'container');
				expCore::incrementLocationReference($iloc,$section); // SECTION
			}
		}
	}
	
//	static function spiderContent($item = null) {
//		// Do nothing, no content
//		return false;
//	}
	
	static function wrapOutput($modclass,$view,$loc = null,$title = '') {
	    global $db;
		if (defined('SOURCE_SELECTOR') && strtolower($modclass) != 'containermodule') {
			$container = new stdClass();
			$mod = new $modclass();
			
			ob_start();
			if (expModules::controllerExists($modclass)) {
			    $action = $db->selectValue('container', 'action', "internal='".serialize($loc)."'");
			    renderAction(array('controller'=>$modclass,'action'=>$action,'view'=>$view));
			} else {
			    $mod->show($view,$loc,$title);
			}
			
			$container->output = ob_get_contents();
			ob_end_clean();

			$source_select = expSession::get('source_select');
			$c_view = $source_select['view'];
			$c_module = $source_select['module'];
			$clickable_mods = $source_select['showmodules'];
			if (!is_array($clickable_mods)) $clickable_mods = null;
			$dest = $source_select['dest'];
			
			$template = new template($c_module,$c_view,$loc);
			if ($dest) $template->assign('dest',$dest);
			
			$container->info = array(
				'module'=>$mod->name(),
				'source'=>$loc->src,
				'hasContent'=>$mod->hasContent(),
				'hasSources'=>$mod->hasSources(),
				'hasViews'=>$mod->hasViews(),
				'class'=>$modclass,
				'clickable'=>($clickable_mods == null || in_array($modclass,$clickable_mods))
			);
			
			$template->assign('container',$container);
			$template->output();
		} else {
			call_user_func(array($modclass,'show'),$view,$loc,$title);
		}
	}
}

?>