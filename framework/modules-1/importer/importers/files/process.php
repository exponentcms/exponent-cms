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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
	switch($_FILES['file']['error']) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			echo gt('The file you uploaded exceeded the size limits for the server.').'<br />';
			break;
		case UPLOAD_ERR_PARTIAL:
			echo gt('The file you uploaded was only partially uploaded.').'<br />';
			break;
		case UPLOAD_ERR_NO_FILE:
			echo gt('No file was uploaded.').'<br />';
			break;
	}
} else {
	$basename = basename($_FILES['file']['name']);
	
	include_once(BASE.'external/Tar.php');
	$tar = new Archive_Tar($_FILES['file']['tmp_name'],'gz');
	
	$dest_dir = BASE.'tmp/extensionuploads/'.uniqid('');
	@mkdir($dest_dir);
	if (!file_exists($dest_dir)) {
		echo gt('Unable to create temporary directory to extract files archive.');
	} else {
		$return = $tar->extract($dest_dir);
		if (!$return) {
			echo '<br />'.gt('Error extracting TAR archive').'<br />';
		} else if (!file_exists($dest_dir.'/files') || !is_dir($dest_dir.'/files')) {
			echo '<br />'.gt('Invalid archive format').'<br />';
		} else {
			// Show the form for specifying which mod types to 'extract'
			
			$mods = array(); // Stores the mod classname, the files list, and the module's real name
			
			$dh = opendir($dest_dir.'/files');
			while (($file = readdir($dh)) !== false) {
				if ($file{0} != '.' && is_dir($dest_dir.'/files/'.$file)) {
					$mods[$file] = array(
						'',
						array_keys(expFile::listFlat($dest_dir.'/files/'.$file,1,null,array(),$dest_dir.'/files/'))
					);
//					if (class_exists($file)) {
//						$mods[$file][0] = call_user_func(array($file,'name')); // $file is the class name of the module
//					}
				} elseif ($file != '.' && $file != '..') {
					$mods[$file] = array(
						'',
						$file
					);
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
