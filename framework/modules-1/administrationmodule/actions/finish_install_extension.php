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
/** @define "BASE" "../../../.." */

// Part of the Extensions category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('extensions',exponent_core_makeLocation('administrationmodule'))) {

	$sessid = session_id();
//	if (!file_exists(BASE."extensionuploads/$sessid") || !is_dir(BASE."extensionuploads/$sessid")) {
	if (!file_exists(BASE."tmp/extensionuploads/$sessid") || !is_dir(BASE."tmp/extensionuploads/$sessid")) {
		$template = new template('administrationmodule','_upload_finalSummary',$loc);
		$template->assign('nofiles',1);
	} else {
		$success = array();
//		foreach (array_keys(expFile::listFlat(BASE."extensionuploads/$sessid",true,null,array(),BASE."extensionuploads/$sessid")) as $file) {
		foreach (array_keys(expFile::listFlat(BASE."tmp/extensionuploads/$sessid",true,null,array(),BASE."tmp/extensionuploads/$sessid")) as $file) {
			if ($file != '/archive.tar' && $file != '/archive.tar.gz' && $file != 'archive.tar.bz2' && $file != '/archive.zip') {
				expFile::makeDirectory(dirname($file));
//				$success[$file] = copy(BASE."extensionuploads/$sessid".$file,BASE.substr($file,1));
				$success[$file] = copy(BASE."tmp/extensionuploads/$sessid".$file,BASE.substr($file,1));
				if (basename($file) == 'views_c') chmod(BASE.substr($file,1),0777);
			}
		}
		
//		$del_return = expFile::removeDirectory(BASE."extensionuploads/$sessid");
		$del_return = expFile::removeDirectory(BASE."tmp/extensionuploads/$sessid");
		echo $del_return;

		ob_start();
		include(BASE . 'framework/modules-1/administrationmodule/actions/installtables.php');
		ob_end_clean();

		$template = new template('administrationmodule','_upload_finalSummary',$loc);
		$template->assign('nofiles',0);
		$template->assign('success',$success);
		$template->assign('redirect',expHistory::getLastNotEditable());
	}
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>