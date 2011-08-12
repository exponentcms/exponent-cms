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

if ($user && $user->is_admin == 1) {
	$importers = array();
	$idh = opendir(BASE.'framework/modules-1/importer/importers');
	while (($imp = readdir($idh)) !== false) {
		if (substr($imp,0,1) != '.' && is_readable(BASE.'framework/modules-1/importer/importers/'.$imp.'/start.php') && is_readable(BASE.'framework/modules-1/importer/importers/'.$imp.'/info.php')) {
			$importers[$imp] = include(BASE.'framework/modules-1/importer/importers/'.$imp.'/info.php');
		}
	}
	
	$template = new template('importer','_importers');
	$template->assign('importers',$importers);
	$template->output();
	
} else {
	echo SITE_403_HTML;
}

?>