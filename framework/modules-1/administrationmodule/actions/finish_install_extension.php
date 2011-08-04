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

	$template = new template('administrationmodule','_upload_finalSummary',$loc);

	$sessid = session_id();
	if (!file_exists(BASE."extensionuploads/$sessid") || !is_dir(BASE."extensionuploads/$sessid")) {
		$template->assign('nofiles',1);
	} else {
//		if (!defined('SYS_FILES')) require_once(BASE.'framework/core/subsystems-1/files.php');
		require_once(BASE.'framework/core/subsystems-1/files.php');
		$success = array();
		foreach (array_keys(expFile::listFlat(BASE."extensionuploads/$sessid",true,null,array(),BASE."extensionuploads/$sessid")) as $file) {
			if ($file != '/archive.tar' && $file != '/archive.tar.gz' && $file != 'archive.tar.bz2' && $file != '/archive.zip') {
				expFile::makeDirectory(dirname($file));
				$success[$file] = copy(BASE."extensionuploads/$sessid".$file,BASE.substr($file,1));
				if (basename($file) == 'views_c') chmod(BASE.substr($file,1),0777);
			}
		}
		
		$del_return = expFile::removeDirectory(BASE."extensionuploads/$sessid");
		echo $del_return;
		
		$template->assign('nofiles',0);
		$template->assign('success',$success);
		
		$template->assign('redirect',expHistory::getLastNotEditable());
		
		ob_start();
		include(BASE . 'framework/modules-1/administrationmodule/actions/installtables.php');
		ob_end_clean();
	}
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>