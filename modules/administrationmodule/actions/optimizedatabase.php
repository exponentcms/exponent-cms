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

// Part of the Database category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('database',exponent_core_makeLocation('administrationmodule'))) {
	$before = $db->databaseInfo();
	foreach (array_keys($before) as $table) {
		$db->optimize($table);
	}
	$after = $db->databaseInfo();
	
	$template = new template('administrationmodule','_optimizedatabase',$loc);
	$template->assign('before',$before);
	$template->assign('after',$after);
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>