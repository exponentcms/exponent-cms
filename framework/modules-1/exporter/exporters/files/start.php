<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

//$mods = array();
//$dh = opendir(BASE.'files');
//while (($file = readdir($dh)) !== false) {
//	if (is_dir(BASE.'files/'.$file) && $file{0} != '.' && class_exists($file)) {
//		$mods[$file] = call_user_func(array($file,'name'));
//	}
//}
//uasort($mods,'strnatcmp');

$template = new template('exporter','_files_modList',$loc);
//$template->assign('mods',$mods);
$template->assign('user',$user);
$template->output();

?>