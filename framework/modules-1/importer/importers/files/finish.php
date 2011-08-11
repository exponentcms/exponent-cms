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

$dest_dir = expSession::get('dest_dir');
$files = expSession::get('files_data');
if (!file_exists(BASE.'files')) {
	mkdir(BASE.'files',0777);
}
foreach (array_keys($files) as $mod) {
	expFile::copyDirectoryStructure($dest_dir.'/files/'.$mod,BASE.'files/'.$mod);
	foreach (array_keys($files[$mod][1]) as $file) {
		copy($dest_dir.'/files/'.$mod.'/'.$file,BASE.'files/'.$mod.'/'.$file);
	}
}

expSession::un_set('dest_dir');
expSession::un_set('files_data');

expFile::removeDirectory($dest_dir);

$template = new template('importer','_files_final');
$template->output();

?>