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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

$i18n = exponent_lang_loadFile('modules/importer/importers/files/process.php');

if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
	switch($_FILES['file']['error']) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			echo $i18n['file_too_large'].'<br />';
			break;
		case UPLOAD_ERR_PARTIAL:
			echo $i18n['partial_file'].'<br />';
			break;
		case UPLOAD_ERR_NO_FILE:
			echo $i18n['no_file'].'<br />';
			break;
	}
} else {
	$basename = basename($_FILES['file']['name']);
	
	include_once(BASE.'external/Tar.php');
	$tar = new Archive_Tar($_FILES['file']['tmp_name'],'gz');
	
	$dest_dir = BASE.'tmp/'.uniqid('');
	@mkdir($dest_dir);
	if (!file_exists($dest_dir)) {
		echo $i18n['file_cant_mkdir'];
	} else {
		$return = $tar->extract($dest_dir);
		if (!$return) {
			echo '<br />'.$i18n['error_tar'].'<br />';
		} else if (!file_exists($dest_dir.'/files') || !is_dir($dest_dir.'/files')) {
			echo '<br />'.$i18n['bad_archive'].'<br />';
		} else {
			// Show the form for specifying which mod types to 'extract'
			
			$mods = array(); // Stores the mod classname, the files list, and the module's real name
			
//			if (!defined('SYS_FILES')) require_once(BASE.'framework/core/subsystems-1/files.php');
			require_once(BASE.'framework/core/subsystems-1/files.php');

			$dh = opendir($dest_dir.'/files');
			while (($file = readdir($dh)) !== false) {
				if ($file{0} != '.' && is_dir($dest_dir.'/files/'.$file)) {
					$mods[$file] = array(
						'',
						array_keys(expFile::listFlat($dest_dir.'/files/'.$file,1,null,array(),$dest_dir.'/files/'.$file.'/'))
					);
					if (class_exists($file)) {
						$mods[$file][0] = call_user_func(array($file,'name')); // $file is the class name of the module
					}
				}
			}
			
			$template = new template('importer','_files_selectModList');
			$template->assign('dest_dir',$dest_dir);
			$template->assign('file_data',$mods);
			$template->output();
		}
	}
}
	
?>
