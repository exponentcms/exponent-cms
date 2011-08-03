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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('configuration',exponent_core_makeLocation('administrationmodule'))) {
	
	/* This code is copied in the install system without permissions in order to upgrade the database during 
	* install.  Be sure to copy any changes there if modifying this code */
	
	define("TMP_TABLE_EXISTED",		1);
	define("TMP_TABLE_INSTALLED",	2);
	define("TMP_TABLE_FAILED",		3);
	define("TMP_TABLE_ALTERED",		4);
	
	$tables = array();

	// first the core and 1.0 definitions
	$dirs = array(
//			BASE."datatypes/definitions",
		BASE."framework/core/definitions",
		);
	foreach ($dirs as $dir) {
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if (is_readable("$dir/$file") && is_file("$dir/$file") && substr($file,-4,4) == ".php" && substr($file,-9,9) != ".info.php") {
					$tablename = substr($file,0,-4);
					$dd = include("$dir/$file");
					$info = null;
					if (is_readable("$dir/$tablename.info.php")) $info = include("$dir/$tablename.info.php");
					if (!$db->tableExists($tablename)) {
						foreach ($db->createTable($tablename,$dd,$info) as $key=>$status) {
							$tables[$key] = $status;
						}
					} else {
						foreach ($db->alterTable($tablename,$dd,$info) as $key=>$status) {
							if (isset($tables[$key])) echo "$tablename, $key<br>";
							if ($status == TABLE_ALTER_FAILED){
								$tables[$key] = $status;
							}else{
								$tables[$key] = ($status == TABLE_ALTER_NOT_NEEDED ? DATABASE_TABLE_EXISTED : DATABASE_TABLE_ALTERED);
							}

						}
					}
				}
			}
		}
	}

	// then search for module definitions
	$newdef = BASE."framework/modules";
	if (is_readable($newdef)) {
		$dh = opendir($newdef);
		while (($file = readdir($dh)) !== false) {
			if (is_dir($newdef.'/'.$file) && ($file != '..' && $file != '.')) {
				$dirpath = $newdef.'/'.$file.'/definitions';
				if (file_exists($dirpath)) {
					$def_dir = opendir($dirpath);
					while (($def = readdir($def_dir)) !== false) {
//							eDebug("$dirpath/$def");
						if (is_readable("$dirpath/$def") && is_file("$dirpath/$def") && substr($def,-4,4) == ".php" && substr($def,-9,9) != ".info.php") {
							$tablename = substr($def,0,-4);
							$dd = include("$dirpath/$def");
							$info = null;
							if (is_readable("$dirpath/$tablename.info.php")) $info = include("$dirpath/$tablename.info.php");
							if (!$db->tableExists($tablename)) {
								foreach ($db->createTable($tablename,$dd,$info) as $key=>$status) {
									$tables[$key] = $status;
								}
							} else {
								foreach ($db->alterTable($tablename,$dd,$info) as $key=>$status) {
									if (isset($tables[$key])) echo "$tablename, $key<br>";
									if ($status == TABLE_ALTER_FAILED){
										$tables[$key] = $status;
									}else{
										$tables[$key] = ($status == TABLE_ALTER_NOT_NEEDED ? DATABASE_TABLE_EXISTED : DATABASE_TABLE_ALTERED);
									}

								}
							}
						}
					}
				}
			}
		}
	}

	exponent_sessions_clearCurrentUserSessionCache();
	ksort($tables);
	$template = new template("administrationmodule","_tableInstallSummary",$loc);
	$template->assign("status",$tables);	
	$template->output();
	
}
?>
