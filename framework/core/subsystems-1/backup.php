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
/** @define "BASE" "../../.." */

/* exdoc
 * SYS Flag for Backup Subsystem.
 * The definition of this constant lets other parts
 * of the system know that the Backup Subsystem
 * has been included for use.
 * @node Subsystems:Backup
 */
//define('SYS_BACKUP',1);

/* exdoc
 * The EQL header string for object dump file formats.
 * This header defines the version of EQL native to
 * the current implementation of the Backup Subsystem.
 * @node Subsystems:Backup
 */
define('EQL_HEADER','EQL-Exponent Query Language');

/* exdoc
 * This function takes a database object and dumps
 * all of the records in all of the tables into a string.
 * The contents of the string are suitable for storage
 * in a file or other permanent mechanism, and is in
 * the EQL format natively handled by the current
 * implementation.
 *
 * @param Database $db The database object to dump to EQL.
 * @node Subsystems:Backup
 */
function exponent_backup_dumpDatabase($db,$tables = null,$force_version = null) {
	$dump = EQL_HEADER."\r\n";
	if ($force_version == null) {
		$dump .= 'VERSION:'.EXPONENT."\r\n\r\n";
	} else {
		$dump .= 'VERSION:'.$force_version."\r\n\r\n";
	}

	if (!is_array($tables)) {
		$tables = $db->getTables();
		if (!function_exists('tmp_removePrefix')) {
			function tmp_removePrefix($tbl) {
				return substr($tbl,strlen(DB_TABLE_PREFIX)+1);
				// we add 1, because DB_TABLE_PREFIX  no longer has the trailing
				// '_' character - that is automatically added by the database class.
			}
		}
		$tables = array_map('tmp_removePrefix',$tables);
	}
	usort($tables,'strnatcmp');
	foreach ($tables as $table) {
		$dump .= 'TABLE:'.$table."\r\n";
		foreach ($db->selectObjects($table) as $obj) {
			$dump .= 'RECORD:'.str_replace(array("\r","\n"),array('\r','\n'),serialize($obj))."\r\n";
		}
		$dump .= "\r\n";
	}
	return $dump;
}

/* exdoc
 * This function restores a database (overwriting all data in
 * any existing tables) from an EQL object dump.  Returns true if
 * the restore was a success and false if something went horribly wrong
 * (unable to read file, etc.)  Even if true is returned, there is a chance
 * that some errors were encountered.  Check $errors to be sure everything
 * was fine.
 *
 * @param Database $db The database to restore to
 * @param string $file The filename of the EQL file to restore from
 * @param array $errors A referenced array that stores errors.  Whatever
 * 	variable is passed in this argument will contain all errors encounterd
 *	during the parse/restore.
 * @node Subsystems:Backup
 */
function exponent_backup_restoreDatabase($db,$file,&$errors,$force_version = null) {
	$errors = array();

	$i18n = exponent_lang_loadFile('subsystems/backup.php');

	if (is_readable($file)) {
		$lines = @file($file);

		// Sanity check
		if (count($lines) < 2 || trim($lines[0]) != EQL_HEADER) {
			$errors[] = $i18n['bad_eql'];
			return false;
		}

		if ($force_version == null) {
			$version = explode(':',trim($lines[1]));
			$eql_version = $version[1]+0;
		} else {
			$eql_version = $force_version;
		}
		$current_version = EXPONENT+0;

		$clear_function = '';
		$fprefix = '';
		// Check version and include necessary converters
		//FIXME We probably need to reject v1.0 eql files
		if ($eql_version != $current_version) {
			include_once(BASE.'framework/core/subsystems-1/backup/'.$eql_version.'.php');
			$fprefix = 'exponent_backup_'.implode('',explode('.',$eql_version)).'_';
			if (function_exists($fprefix.'clearedTable')) {
				$clear_function = $fprefix.'clearedTable';
			}
		}

		$table = '';
		$table_function = '';
		for ($i = 2; $i < count($lines); $i++) {
			$line_number = $i;
			$line = trim($lines[$i]);
			if ($line != '') {
				$pair = explode(':',$line);
				$pair[1] = implode(':',array_slice($pair,1));
				$pair = array_slice($pair,0,2);

				if ($pair[0] == 'TABLE') {
					$table = $pair[1];
					if ($fprefix != '') {
						$table_function = $fprefix.$table;
					}
					if ($db->tableExists($table)) {
						$db->delete($table);
						if ($clear_function != '') {
							$clear_function($db,$table);
						}
					} else {
						//FIXME Needs to account for definitions in modules folder
						if (!file_exists(BASE.'framework/core/definitions/'.$table.'.php')) {
							$errors[] = sprintf($i18n['no_definition'],$table,$line_number);
						} else if (!is_readable(BASE.'framework/core/definitions/'.$table.'.php')) {
							$errors[] = sprintf($i18n['unreadable_definition'],$table,'framework/core/definitions/'.$table.'.php',$line_number);
						} else {
							$dd = include(BASE.'framework/core/definitions/'.$table.'.php');
							$info = (is_readable(BASE.'framework/core/definitions/'.$table.'.info.php') ? include(BASE.'framework/core/definitions/'.$table.'.info.php') : array());
							$db->createTable($table,$dd,$info);
						}
					}
				} else if ($pair[0] == 'RECORD') {
					// Here we need to check the conversion scripts.
					$pair[1] = str_replace('\r\n',"\r\n",$pair[1]);
					$object = unserialize($pair[1]);
					if (function_exists($table_function)) {
						$table_function($db,$object);
					} else {
						$db->insertObject($object,$table);
					}
				} else {
					$errors[] = sprintf($i18n['invalid_type'],$pair[0],$line_number);
				}
			}
		}
		if ($eql_version != $current_version) {
			include_once(BASE.'framework/core/subsystems-1/backup/normalize.php');
		}
		return true;
	} else {
		$errors[] = $i18n['eql_not_r'];
		return false;
	}
}

?>