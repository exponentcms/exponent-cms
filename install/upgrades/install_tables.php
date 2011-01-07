<?php
##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class install_tables {
	protected $from_version = '0.96.3';
	protected $to_version = '1110.97.0'; //set this to something ridiculously high so it always runs

	function name() {
		return "Upgrade Database Tables";
	}

	function checkVersion($version) {
		// if this upgrade applies to only one version then check to see if we have a match
		if ($this->from_version == $this->to_version && $version == $this->from_version) {
			return true;
		} elseif ($version >= $this->from_version && $version <= $this->to_version) {
			return true;
		} else {
			return false;
		}
	}

	function upgrade() {
		global $db;
		/* This code is copied from installtables.php in the admin module, which has permissions.
		* Be sure to copy any changes there if modifying this code */

		define("TMP_TABLE_EXISTED",             1);
		define("TMP_TABLE_INSTALLED",   2);
		define("TMP_TABLE_FAILED",              3);
		define("TMP_TABLE_ALTERED",             4);

		$retcode['created'] = 0;
		$retcode['not_altered'] = 0;
		$retcode['altered'] = 0;

		$dirs = array(
			BASE."datatypes/definitions",
			BASE."framework/core/database/definitions",
		);
		
		$tables = array();
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
								$retcode['created'] += 1;
				                        }
				                } else {
				                        foreach ($db->alterTable($tablename,$dd,$info) as $key=>$status) {
				                                if ($status == TABLE_ALTER_FAILED){
				                                        $tables[$key] = $status;
				                                }else{
				                                        $tables[$key] = ($status == TABLE_ALTER_NOT_NEEDED ? DATABASE_TABLE_EXISTED : DATABASE_TABLE_ALTERED);
									if ($status == TABLE_ALTER_NOT_NEEDED) {
										$retcode['not_altered'] += 1;
									} elseif ($status == DATABASE_TABLE_ALTERED) {
										$retcode['altered'] += 1;
									}                             
				                                }

				                        }
				                }
				        }
				}
			}
		}

		$retval = 'Tables Created: '.$retcode['created'].'<br> Tables Altered: '.$retcode['altered'].'<br> Tables Not Altered: '.$retcode['not_altered'];
		return $retval;
	}
}

?>
