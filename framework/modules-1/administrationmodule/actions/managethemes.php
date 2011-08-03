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
/** @define "BASE" "../../.." */

// Part of the Extensions category

if (!defined('EXPONENT')) exit('');
if (exponent_permissions_check('extensions',exponent_core_makeLocation('administrationmodule'))) {
	exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
	$themes = array();
	if (is_readable(BASE.'themes')) {
		$dh = opendir(BASE.'themes');
		while (($file = readdir($dh)) !== false) {
			if (is_readable(BASE."themes/$file/class.php")) {
				include_once(BASE."themes/$file/class.php");
				
				$theme = new $file();
				
				$t = null;
				$t->name = $theme->name();
				$t->description = $theme->description();
				$t->author = $theme->author();
				$t->preview = is_readable(BASE."themes/$file/preview.jpg") ? "themes/$file/preview.jpg" : "themes/" . DISPLAY_THEME . "/noprev.jpg";
				$themes[$file] = $t;
			}
		}
	}
	
	$template = new template('administrationmodule','_thememanager',$loc);
	
	$template->assign('themes',$themes);
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>