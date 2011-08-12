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

// Part of the Database category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('database',exponent_core_makeLocation('administrationmodule'))) {
	expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);

//	$nullrefs = $db->selectObjects('locationref','refcount=0');
	$nullrefs = $db->selectObjects('sectionref','refcount=0');
	$mods = array();
	$have_bad_orphans = false;
	foreach ($nullrefs as $nullref) {
		$modclass = $nullref->module;
		
		$have_bad_orphans = false;
		
		if (!isset($mods[$nullref->module])) {
			if (class_exists($modclass)) {
				$mod = new $modclass();
				$mods[$nullref->module] = array(
					'name'=>$mod->name(),
					'modules'=>array()
				);
			} else $have_bad_orphans = true;
		}
		if (class_exists($modclass)) {
            ob_start();
            //FIXME: Glue code for old modules
            if (controllerExists($modclass)) {
                renderAction(array('controller'=>$nullref->module, 'action'=>'showall','src'=>$nullref->source));                
            } else {			
    			call_user_func(array($modclass,'show'),DEFAULT_VIEW,exponent_core_makeLocation($modclass,$nullref->source));
            }
            
            $mods[$nullref->module]['modules'][$nullref->source] = ob_get_contents();
            ob_end_clean();
		}
	}
	
	$template = new template('administrationmodule','_orphanedcontent');
	$template->assign('modules',$mods);
	$template->assign('have_bad_orphans',$have_bad_orphans);
	
	$template->output();	
} else {
	echo SITE_403_HTML;
}

?>
