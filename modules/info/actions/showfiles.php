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

// Part of the Administration Control Panel : Extensions category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('extensions',exponent_core_makeLocation('administrationmodule'))) {
//	if (!defined('SYS_INFO')) require_once(BASE.'framework/core/subsystems-1/info.php');
	require_once(BASE.'framework/core/subsystems-1/info.php');
	$files = exponent_info_files($_GET['type'],$_GET['name']);
	if (is_array($files)) ksort($files);
	
	$template = new template('info','_checksums',$loc);

	if (is_array($files)) {
		$actual = exponent_info_fileChecksums($files);
		foreach (array_keys($files) as $f) if (is_int($files[$f])) $files[$f] = "";
		$relative = array();
		foreach (array_keys($files) as $file) {
			$relative[$file] = array(
				'dir'=>str_replace(array(BASE,' '),array('','&nbsp;'),dirname($file).'/'),
				'file'=>str_replace(' ','&nbsp;',basename($file))
			);
		}
		foreach (array_keys($files) as $f) {
			if (!is_string($files[$f])) $files[$f] = '';
		}
		$template->assign('files',$files);
		$template->assign('checksums',$actual);
		$template->assign('relative',$relative);
	} else {
		$template->assign('error',$files);
	}
	$template->output();
}

?>
