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

expDatabase::fix_table_names();
$tables = $db->getTables();
if (!function_exists('tmp_removePrefix')) {
	function tmp_removePrefix($tbl) {
		// we add 1, because DB_TABLE_PREFIX  no longer has the trailing
		// '_' character - that is automatically added by the database class.
		return substr($tbl,strlen(DB_TABLE_PREFIX)+1);
	}
}
$tables = array_map('tmp_removePrefix',$tables);
usort($tables,'strnatcmp');

$template = new template('exporter','_eql_tableList',$loc);
$template->assign('user',$user);
$template->assign('tables',$tables);
$template->output();

?>