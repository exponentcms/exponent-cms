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

if (!isset($_POST['tables'])) { // No checkboxes clicked, and got past the JS check
	echo gt('You must choose at least one table to export.');
} else { // All good
	require_once(BASE.'framework/core/subsystems-1/backup.php');

	$filename = str_replace(
		array('__DOMAIN__','__DB__'),
		array(str_replace('.','_',HOSTNAME),DB_NAME),
		$_POST['filename']);
	$filename = preg_replace('/[^A-Za-z0-9_.-]/','-',strftime($filename,time()).'.eql');
	
	ob_end_clean();
	ob_start("ob_gzhandler");

	if (isset($_POST['save_sample'])) { // Save as a theme sample is checked off
		$path = BASE . "themes/".DISPLAY_THEME_REAL."/sample.eql";
		if (!$eql = fopen ($path, "ab")) {
			flash('error',"Error opening eql file for writing ($path).");
		} else {
			$eqlfile = exponent_backup_dumpDatabase($db,array_keys($_POST['tables']));
			if (fwrite ($eql, $eqlfile)  === FALSE) {
				flash('error',"Error writing to eql file ($path).");
			}
			fclose ($eql);
			flash('message',"Sample database (eql file) saved to '".DISPLAY_THEME."' theme.");
			expHistory::back();
		}
	} else {
		// This code was lifted from phpMyAdmin, but this is Open Source, right?

		// 'application/octet-stream' is the registered IANA type but
		//        MSIE and Opera seems to prefer 'application/octetstream'
		$mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';

		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		// IE need specific headers
		if (EXPONENT_USER_BROWSER == 'IE') {
			header('Content-Disposition: inline; filename="' . $filename . '"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Pragma: no-cache');
		}
		echo exponent_backup_dumpDatabase($db,array_keys($_POST['tables']));
		exit(''); // Exit, since we are exporting
	}
}

?>