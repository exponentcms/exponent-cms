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

//if (!isset($_POST['mods'])) {
//	echo gt('You must select at least one module to export files for.');
//	return;
//}

include_once(BASE.'external/Tar.php');

$files = array();
//foreach (array_keys($_POST['mods']) as $mod) {
//	foreach ($db->selectObjects('file',"directory LIKE 'files/".$mod."%'") as $file) {
	foreach ($db->selectObjects('expFiles',1) as $file) {
		$files[] = BASE.$file->directory.'/'.$file->filename;
	}
//}

$fname = tempnam(BASE.'/tmp','exporter_files_');
$tar = new Archive_Tar($fname,'gz');
$tar->createModify($files,'',BASE);

$filename = str_replace(
	array('__DOMAIN__','__DB__'),
	array(str_replace('.','_',HOSTNAME),DB_NAME),
	$_POST['filename']);
$filename = preg_replace('/[^A-Za-z0-9_.-]/','-',strftime($filename,time()).'.tar.gz');

ob_end_clean();

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
	
$fh = fopen($fname,'rb');
while (!feof($fh)) {
	echo fread($fh,8192);
}
fclose($fh);
unlink($fname);

exit(''); // Exit, since we are exporting.

?>