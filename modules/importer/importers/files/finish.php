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

if (!defined('EXPONENT')) exit('');

$dest_dir = exponent_sessions_get('dest_dir');
$files = exponent_sessions_get('files_data');
if (!defined('SYS_FILES')) require_once(BASE.'subsystems/files.php');
if (!file_exists(BASE.'files')) {
	mkdir(BASE.'files',0777);
}
foreach (array_keys($files) as $mod) {
	exponent_files_copyDirectoryStructure($dest_dir.'/files/'.$mod,BASE.'files/'.$mod);
	foreach (array_keys($files[$mod][1]) as $file) {
		copy($dest_dir.'/files/'.$mod.'/'.$file,BASE.'files/'.$mod.'/'.$file);
	}
}

exponent_sessions_unset('dest_dir');
exponent_sessions_unset('files_data');

exponent_files_removeDirectory($dest_dir);

$template = new template('importer','_files_final');
$template->output();

?>