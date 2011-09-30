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

/**
 * This is the expDatabase subsystem
 * Handles all database abstraction in Exponent.
 *
 * @subpackage Core-Subsystems
 * @package Framework
 */
class expDatabase {

	/**
	 * Connect to the Exponent database
	 *
	 * This function attempts to connect to the exponent database,
	 * and then returns the database object to the caller.
	 *
	 * @param string $username the database username
	 * @param string $password the database password
	 * @param string $hostname the url of the database server
	 * @param string $database the name of the database
	 * @param string $dbclass
	 * @param bool $new
	 * @return \expDatabase the exponent database object
	 */
	public static function connect($username,$password,$hostname,$database,$dbclass = '',$new=false) {
		if (!defined('DB_ENGINE')) {
			$backends = array_keys(self::backends(1));
			if (count($backends)) {
				define('DB_ENGINE',$backends[0]);
			} else {
				define('DB_ENGINE','NOTSUPPORTED');
			}
		}
		(include_once(BASE.'framework/core/subsystems/database/'.DB_ENGINE.'.php')) or exit('None of the installed Exponent Database Backends will work with this server\'s version of PHP.');
		if ($dbclass == '' || $dbclass == null) $dbclass = DB_ENGINE;
		(include_once(BASE.'framework/core/subsystems/database/'.$dbclass.'.php')) or exit('The specified database backend  ('.$dbclass.') is not supported by Exponent');
		$dbclass .= '_database';
		$newdb = new $dbclass();
		$newdb->connect($username,$password,$hostname,$database,$new);
		return $newdb;
	}

	/**
	 * List all available database backends
	 *
	 * This function looks for available database engines,
	 * and then returns an array to the caller.
	 *
	 * @param int $valid_only
	 * @return Array An associative array of engine identifiers.
	 *	The internal engine name is the key, and the external
	 *	descriptive name is the value.
	 */
	public static function backends($valid_only = 1) {
		$options = array();
		$dh = opendir(BASE.'framework/core/subsystems-1/database');
		while (($file = readdir($dh)) !== false) {
			if (is_file(BASE.'framework/core/subsystems-1/database/'.$file) && is_readable(BASE.'framework/core/subsystems-1/database/'.$file) && substr($file,-9,9) == '.info.php') {
				$info = include(BASE.'framework/core/subsystems-1/database/'.$file);
				if ($info['is_valid'] == 1 || !$valid_only) {
					$options[substr($file,0,-9)] = $info['name'];
				}
			}
		}
		return $options;
	}

}

?>
