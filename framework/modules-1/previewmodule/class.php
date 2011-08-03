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

class previewmodule {
	function name() { return exponent_lang_loadKey('modules/previewmodule/class.php','module_name'); }
	function description() { return exponent_lang_loadKey('modules/previewmodule/class.php','module_description'); }
	function author() { return 'James Hunt'; }
	
	function hasContent() { return false; }
	function hasSources() { return false; }
	function hasViews()   { return true; }
	
	function supportsWorkflow() { return false; }
	
	function permissions($internal = '') {
	
	}
	
	function deleteIn($loc) {
		// Do nothing, no content
	}
	
	function copyContent($from_loc,$to_loc) {
		// Do nothing, no content
	}
	
	function show($view,$loc = null, $title = '') {
		$template = new template('previewmodule',$view,$loc);
		
		$level = 99;
		if (expSession::is_set('uilevel')) {
			$level = expSession::get('uilevel');
		}
		$template->assign('editMode',expSession::loggedIn() && $level != UILEVEL_PREVIEW);
		$template->assign('title',$title);
		$template->assign('previewMode',($level == UILEVEL_PREVIEW));
		
		$template->output($view);
	}
	
	function spiderContent($item = null) {
		// Do nothing, no content
		return false;
	}
	
}