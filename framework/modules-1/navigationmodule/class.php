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
/** @define "BASE" "../../.." */

class navigationmodule {
    function name() { return self::displayname(); }
	static function displayname() { return 'Navigator (Deprecated)'; }
    static function author() { return 'OIC Group, Inc'; }
    static function description() { return 'Allows users to navigate through pages on the site, and allows Administrators to manage the site page structure / hierarchy.'; }
    static function hasContent() { return false; }
	static function hasSources() { return false; }
    static function hasViews()   { return true; }  //FIXME we should turn this OFF, but may need it for old sites?
    static function supportsWorkflow() { return false; }
	
	function permissions($internal = '') {
		return array(
			'manage'=>gt('Manage'),
			'view'=>gt('View Page'),
		);
	}
	
	static function show($view,$loc = null,$title = '') {
        // new 2.0 emulation layer
        if (file_exists(BASE.'framework/modules/navigation/views/navigation/'.'showall_'.$view.'.tpl') || file_exists(THEME_ABSOLUTE.'modules/navigation/views/navigation/'.'showall_'.$view.'.tpl')) {
            renderAction(array('controller'=>'navigation','action'=>'showall','view'=>'showall_'.$view));
            return;
        }

        // old 1.0 method
		global $db, $sectionObj, $user;
		$id = $sectionObj->id;
		$current = null;
		
		switch( $view ) {
  			case "Breadcrumb":
				//Show not only the location of a page in the hierarchy but also the location of a standalone page
				global $sections;
				$current = $db->selectObject('section',' id= '.$id);
			
				if( $current->parent == -1 ) { // standalone page
					$sections = navigationController::levelTemplate(-1,0);  //FIXME why are we changing the global $sessions?
					foreach ($sections as $section) {
						if ($section->id == $id) {
							$current = $section;
							break;
						}
					}
				} else {
					$sections = navigationController::levelTemplate(0,0);  //FIXME global $sections is initialized this way
					foreach ($sections as $section) {
						if ($section->id == $id) {
							$current = $section;
							break;
						}
					}
				}
			break;
			default:
				global $sections;
				if ($sectionObj->parent == -1) {
					$current = $sectionObj;
				} else {
					foreach ($sections as $section) {
						if ($section->id == $id) {
							$current = $section;
							break;
						}
					}
				}

			break;
		}

		$template = new template('navigationmodule',$view,$loc);
		$template->assign('sections',$sections);
		$template->assign('current',$current);
		$template->assign('num_sections', count($sections));
		$template->assign('canManage',$user->isAdmin());
		$template->assign('moduletitle',$title);
		$template->output();
	}
	
	function deleteIn($loc) {
		// Do nothing, no content
	}
	
	static function copyContent($fromloc,$toloc) {
		// Do nothing, no content
	}
	
}

?>