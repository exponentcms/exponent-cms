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

if (!defined("EXPONENT")) exit("");

// PERM CHECK
	$source_select = array();
	$module = "containermodule";
	$view = "_sourcePicker";
	$clickable_mods = null; // Show all
	$dest = null;
	
	if (exponent_sessions_isset("source_select") && (defined("SOURCE_SELECTOR") || defined("CONTENT_SELECTOR"))) {
		$source_select = exponent_sessions_get("source_select");
		$view = $source_select["view"];
		$module = $source_select["module"];
		$clickable_mods = $source_select["showmodules"];
		$dest = $source_select['dest'];
		
	}

   exponent_sessions_clearAllUsersSessionCache('containermodule');

	$orphans = array();
	foreach ($db->selectObjects("locationref","module='".preg_replace('/[^A-Za-z0-9_]/','',$_GET['module'])."' AND refcount=0") as $orphan) {
		$obj = null;
		$loc = exponent_core_makeLocation($orphan->module,$orphan->source,$orphan->internal);
		
		if (class_exists($orphan->module)) {
			$modclass = $orphan->module;
			$mod = new $modclass();			
			if (class_exists($modclass)) {			    
			    ob_start();
			    if (controllerExists($modclass)) {
                    renderAction(array('controller'=>$modclass, 'action'=>'showall','src'=>$orphan->source));                
                } else {                    
			        $mod->show("Default",$loc);
			    }
			    $obj->output = ob_get_contents();
			    ob_end_clean();
		    }
			
			$obj->info = array(
				"module"=>$mod->name(),
				"source"=>$orphan->source,
				"hasContent"=>$mod->hasContent(),
				"hasSources"=>$mod->hasSources(),
				"hasViews"=>$mod->hasViews(),
				"class"=>$modclass,
				"clickable"=>(($clickable_mods == null || in_array($modclass,$clickable_mods))?1:0)
			);
		} else {
			$i18n = exponent_lang_loadFile('modules/containermodule/class.php');
			$obj->output = sprintf($i18n['mod_not_found'],$orphan->module);
			$containers[$i]->info = array(
					"module"=>"Unknown:".$location->mod,
					"source"=>$orphan->source,
					"hasContent"=>0,
					"hasSources"=>0,
					"hasViews"=>0,
					"class"=>$modclass,
					"clickable"=>false
				);
		}
		$obj->moduleLocation = $loc;
		$orphans[] = $obj;
	}

	$template = new template("containermodule","Default");
	$template->assign("singleview",$view);
	$template->assign("singlemodule",$module);

	if ($dest) $template->assign("dest",$dest);
	$template->assign("containers",$orphans);
	$template->output();
// END PERM CHECK

?>
