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

$dest_dir = $_POST['dest_dir'];
$files = array();
foreach (array_keys($_POST['mods']) as $file) {
	$files[$file] = expFile::canCreate(BASE.'files/'.$file);
//	if (class_exists($mod)) {
//		$files[$mod][0] = call_user_func(array($mod,'name'));
//	}
//	foreach (array_keys(expFile::listFlat($dest_dir.'/files',1,null,array(),$dest_dir.'/files/')) as $file) {
//		$files[$mod][1][$file] = expFile::canCreate(BASE.'files/'.$file);
//	}
}

expSession::set('dest_dir',$dest_dir);
expSession::set('files_data',$files);

$template = new template('importer','_files_verifyFiles');
$template->assign('files_data',$files);
$template->output();


?>