<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

class containermodule {
	function name() { return exponent_lang_loadKey('modules/containermodule/class.php','module_name'); }
	function author() { return 'James Hunt'; }
	function description() { return exponent_lang_loadKey('modules/containermodule/class.php','module_description'); }
	
	function hasContent() { return true; }
	function hasSources() { return true; }
	function hasViews()   { return true; }
	
	function supportsWorkflow() { return false; }
	
	function permissions($internal = '') {
		$i18n = exponent_lang_loadFile('modules/containermodule/class.php');
		return array(
			'administrate'=>$i18n['perm_administrate'],
			'add_module'=>$i18n['perm_add_module'],
			'edit_module'=>$i18n['perm_edit_module'],
			'delete_module'=>$i18n['perm_delete_module'],
			'order_modules'=>$i18n['perm_order_modules'],
		);
	}
	
	function deleteIn($loc) {
		global $user;
		if ($user && $user->is_acting_admin == 1) {
			include_once(BASE.'datatypes/container.php');
			
			global $db;
			$containers = $db->selectObjects('container',"external='" . serialize($loc) . "'");
			
			foreach ($containers as $container) {
				container::delete($container);
				$db->delete('container','id='.$container->id);
			}
		}
	}
	
	function show($view,$loc = null,$title = '') {
		$i18n = exponent_lang_loadFile('modules/containermodule/class.php');
		if (empty($view)) $view = "Default";
		$source_select = array();
		$clickable_mods = null; // Show all
		$dest = null;
		
		$singleview = '_container';
		$singlemodule = 'containermodule';
		if (exponent_sessions_isset('source_select') && defined('SELECTOR')) {
			$source_select = exponent_sessions_get('source_select');
			$singleview = $source_select['view'];
			$singlemodule = $source_select['module'];
			$clickable_mods = $source_select['showmodules'];
			if (!is_array($clickable_mods)) $clickable_mods = null;
			$dest = $source_select['dest'];
		}
		
		global $db;
		
		$container = null;
		$container_key = serialize( $loc );
		$cache = exponent_sessions_getCacheValue('containermodule');		
		if (!isset($this) || !isset($this->_hasParent) || $this->_hasParent == 0) {
			// Top level container.			
			if(!isset($cache['top'][$container_key])) {        		
				$container = $db->selectObject('container',"external='".serialize(null)."' AND internal='".$container_key."'");
				//if container isn't here already, then create it.
	            if ($container == null) {
					$container->external = serialize(null);
					$container->internal = serialize($loc);
					$container->view = $view;
					$container->title = $title;
					$container->id = $db->insertObject($container,'container');
				}
				$cache['top'][$container_key] = $container;
				exponent_sessions_setCacheValue('containermodule', $cache);
        	}else{
        		$container = $cache['top'][$container_key];
        	}
			if (!defined('PREVIEW_READONLY') || defined('SELECTOR')) $view = empty($container->view) ? $view : $container->view;
			$title = $container->title;
		}

		$template = new template('containermodule',$view,$loc,$cache);
		if ($dest) $template->assign('dest',$dest);
		$template->assign('singleview',$singleview);
		$template->assign('singlemodule',$singlemodule);
		
		$template->assign('top',$container);
		
		$containers = array();
       
        	if(!isset($cache[$container_key])) {
		    	foreach ($db->selectObjects('container',"external='" . $container_key . "'") as $c) {
				if ($c->is_private == 0 || exponent_permissions_check('view',exponent_core_makeLocation($loc->mod,$loc->src,$c->id))) {
				    $containers[$c->rank] = $c;
			    	}
		    	}
        		$cache[$container_key] = $containers;
        		exponent_sessions_setCacheValue('containermodule', $cache);
        	} else {
            		$containers = $cache[$container_key];            
        	}
 
		if (!defined('SYS_WORKFLOW')) include_once(BASE.'subsystems/workflow.php');
		ksort($containers);
		foreach (array_keys($containers) as $i) {
			$location = unserialize($containers[$i]->internal);

			// check to see if this is a controller or module
			$iscontroller = controllerExists($location->mod);
			$modclass = $iscontroller ? getControllerClassName($location->mod) : $location->mod;

			if (class_exists($modclass)) {
				$mod = new $modclass();
				
				ob_start();
					$mod->_hasParent = 1;
					if ($iscontroller) {
						renderAction(array('controller'=>$location->mod, 'action'=>$containers[$i]->action, 'src'=>$location->src, 'view'=>$containers[$i]->view, 'moduletitle'=>$containers[$i]->title));
					} else {
						$mod->show($containers[$i]->view,$location,$containers[$i]->title);
					}

				$containers[$i]->output = trim(ob_get_contents());
				ob_end_clean();
				
				$policy = exponent_workflow_getPolicy($modclass,$location->src);
				
				$containers[$i]->info = array(
					'module'=>$mod->name(),
					'source'=>$location->src,
					'hasContent'=>$mod->hasContent(),
					'hasSources'=>$mod->hasSources(),
					'hasViews'=>$mod->hasViews(),
					'class'=>$modclass,
					'supportsWorkflow'=>($mod->supportsWorkflow()?1:0),
					'workflowPolicy'=>($policy ? $policy->name : ''),
					'workflowUsesDefault'=>(exponent_workflow_moduleUsesDefaultPolicy($location->mod,$location->src) ? 1 : 0),
					'clickable'=>($clickable_mods == null || in_array($modclass,$clickable_mods)),
					'hasConfig'=>$db->tableExists($modclass."_config")
				);
			} else {
				$containers[$i]->output = sprintf($i18n['mod_not_found'],$location->mod);
				$containers[$i]->info = array(
					'module'=>sprintf($i18n['unknown'],$location->mod),
					'source'=>$location->src,
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
			
			$cloc = null;
			$cloc->mod = $loc->mod;
			$cloc->src = $loc->src;
			$cloc->int = $containers[$i]->id;
            $location->mod = str_replace('Controller','',$location->mod);
			$containers[$i]->permissions = array(
				'administrate'=>(exponent_permissions_check('administrate',$location) ? 1 : 0),
				'configure'=>(exponent_permissions_check('configure',$location) ? 1 : 0)
			);
		}
	
		$template->assign('containers',$containers);
		$template->assign('hasParent',(isset($this) && isset($this->_hasParent) ? 1 : 0));
		$template->register_permissions(
			array('administrate','add_module','edit_module','delete_module','order_modules'),
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
				$iloc = exponent_core_makeLocation($oldinternal->mod,'@random'.uniqid(''));
				$c->internal = serialize($iloc);
				$db->insertObject($c,'container');
			
				// Now copy over content
				if (call_user_func(array($oldinternal->mod,'hasContent')) == true) {
					call_user_func(array($oldinternal->mod,'copyContent'),$oldinternal,$iloc);
					// Incrementors!
					exponent_core_incrementLocationReference($iloc,$section); // SECTION
				}
			} else {
				$db->insertObject($c,'container');
				exponent_core_incrementLocationReference($iloc,$section); // SECTION
			}
		}
	}
	
	function spiderContent($item = null) {
		// Do nothing, no content
		return false;
	}
	
	function wrapOutput($modclass,$view,$loc = null,$title = '') {
	    global $db;
		if (defined('SOURCE_SELECTOR') && strtolower($modclass) != 'containermodule') {
			$container = null;
			$mod = new $modclass();
			
			ob_start();
			if (controllerExists($modclass)) {
			    $action = $db->selectValue('container', 'action', "internal='".serialize($loc)."'");
			    renderAction(array('controller'=>$modclass,'action'=>$action,'view'=>$view));
			} else {
			    $mod->show($view,$loc,$title);
			}
			
			
			$container->output = ob_get_contents();
			ob_end_clean();
			
			
			$source_select = exponent_sessions_get('source_select');
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
