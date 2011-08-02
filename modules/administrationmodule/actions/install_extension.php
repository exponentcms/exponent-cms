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
	
	$i18n = exponent_lang_loadFile('modules/administrationmodule/actions/install_extension.php');
		
	if ($_FILES['mod_archive']['error'] != UPLOAD_ERR_OK) {
		
		switch($_FILES['mod_archive']['error']) {
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
		$basename = basename($_FILES['mod_archive']['name']);
		// Check future radio buttons
		// for now, try auto-detect
		$compression = null;
		$ext = '';
		if (substr($basename,-4,4) == '.tar') {
			$compression = null;
			$ext = '.tar';
		} else if (substr($basename,-7,7) == '.tar.gz') {
			$compression = 'gz';
			$ext = '.tar.gz';
		} else if (substr($basename,-4,4) == '.tgz') {
			$compression = 'gz';
			$ext = '.tgz';
		} else if (substr($basename,-8,8) == '.tar.bz2') {
			$compression = 'bz2';
			$ext = '.tar.bz2';
		} else if (substr($basename,-4,4) == '.zip') {
			$compression = 'zip';
			$ext = '.zip';
		}
		
		if ($ext == '') {
			echo $i18n['bad_archive'].'<br />';
		} else {
//			if (!defined('SYS_FILES')) require_once(BASE.'framework/core/subsystems-1/files.php');
			require_once(BASE.'framework/core/subsystems-1/files.php');

			// Look for stale sessid directories:
			$sessid = session_id();
			if (file_exists(BASE."extensionuploads/$sessid") && is_dir(BASE."extensionuploads/$sessid")) exponent_files_removeDirectory("extensionuploads/$sessid");
			$return = exponent_files_makeDirectory("extensionuploads/$sessid");
			if ($return != SYS_FILES_SUCCESS) {
				switch ($return) {
					case SYS_FILES_FOUNDFILE:
					case SYS_FILES_FOUNDDIR:
						echo $i18n['file_in_parh'].'<br />';
						break;
					case SYS_FILES_NOTWRITABLE:
						echo $i18n['dest_not_w'].'<br />';
						break;
					case SYS_FILES_NOTREADABLE:
						echo $i18n['dest_not_r'].'<br />';
						break;
				}
			}
			
			$dest = BASE."extensionuploads/$sessid/archive$ext";
			move_uploaded_file($_FILES['mod_archive']['tmp_name'],$dest);
			
			if ($compression != 'zip') {// If not zip, must be tar
				include_once(BASE.'external/Tar.php');
				
				$tar = new Archive_Tar($dest,$compression);
				
				PEAR::setErrorHandling(PEAR_ERROR_PRINT);
				$return = $tar->extract(dirname($dest));
				if (!$return) {
					echo '<br />'.$i18n['error_tar'].'<br />';
				} else {
					header('Location: ' . URL_FULL . 'index.php?module=administrationmodule&action=verify_extension&type=tar');
				}
			} else { // must be zip
				include_once(BASE.'external/Zip.php');
				
				$zip = new Archive_Zip($dest);
				
				PEAR::setErrorHandling(PEAR_ERROR_PRINT);
				if ($zip->extract(array('add_path'=>dirname($dest))) == 0) {
					echo '<br />'.$i18n['error_zip'].':<br />';
					echo $zip->_error_code . ' : ' . $zip->_error_string . '<br />';
				} else {
					header('Location: ' . URL_FULL . 'index.php?module=administrationmodule&action=verify_extension&type=zip');
				}
			}
		}
	}
} else {
	echo SITE_403_HTML;
}

?>