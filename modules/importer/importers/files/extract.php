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

$dest_dir = $_POST['dest_dir'];
$files = array();
if (!defined('SYS_FILES')) require_once(BASE.'subsystems/files.php');
foreach (array_keys($_POST['mods']) as $mod) {
	$files[$mod] = array(
		'',
		array()
	);
	if (class_exists($mod)) {
		$files[$mod][0] = call_user_func(array($mod,'name'));
	}
	foreach (array_keys(exponent_files_listFlat($dest_dir.'/files/'.$mod,1,null,array(),$dest_dir.'/files/'.$mod.'/')) as $file) {
		$files[$mod][1][$file] = exponent_files_canCreate(BASE.'files/'.$mod.'/'.$file);
	}
}

exponent_sessions_set('dest_dir',$dest_dir);
exponent_sessions_set('files_data',$files);

$template = new template('importer','_files_verifyFiles');
$template->assign('files_data',$files);
$template->output();


?>