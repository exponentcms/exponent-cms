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

define("DATABASE_TABLE_EXISTED",		1);
define("DATABASE_TABLE_INSTALLED",		2);
define("DATABASE_TABLE_FAILED",			3);
define("DATABASE_TABLE_ALTERED",		4);

/**
 * Database Subsystem
 *
 * Handles all database abstraction in Exponent.
 *
 * @package		Subsystems
 * @subpackage	Database
 *
 * @author		James Hunt
 * @copyright		2004-2011





 OIC Group, Inc.
 * @version		0.95
 */

/**
 * SYS flag for Database Subsystem
 *
 * The definition of this constant lets other parts of the subsystem know
 * that the Database Subsystem has been included for use.
 */
define('SYS_DATABASE',1);

/**
 * Database Field Type specifier
 *
 * An index for the Exponent Data Definition Language.
 * This index indicates what type of column should be created
 * in the table.
 */
define('DB_FIELD_TYPE',	0);

/**
 * Database Field Length specifier
 *
 * An index for the Exponent Data Definition Language.
 * This index indicates the length of the column.  Currently,
 * this is only applicable to textual field types.
 */
define('DB_FIELD_LEN',	1);

/**
 * Database Field Default specifier
 *
 * An index for the Exponent Data Definition Language.
 * This index indicates the default value of a field in the table.
 */
define('DB_DEFAULT',	2);

/**
 * Database Incremental Field specifier
 *
 * An index for the Exponent Data Definition Language.
 * This index specifies that the field should automatically
 * increment its value.  This is ONLY applicable to ID fields
 * that are marked as PRIMARY.
 *
 * @see DB_PRIMARY
 * @see DB_DEF_ID
 */
define('DB_INCREMENT',	3);

/**
 * Database Primary Key Field specifier
 *
 * An index for the Exponent Data Definition Language.
 * This index specifies that the field should be treated as the
 * primary key for the table.  There can only be one primary
 * key field per table.
 *
 * @see DB_DEF_ID
 * @see DB_INCREMENT
 */
define('DB_PRIMARY',	4);

/**
 * ????
 */
define('DB_UNIQUE',	5);

/**
 * ????
 */
define('DB_INDEX',		6);

/**
 * ??????
 */
define('DB_FULLTEXT',		7);

/**
 * ??????
 */
define('DB_DEF_IGNORE',	100);

/**
 * Field Type specifier: Numeric ID
 *
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be a numeric ID.
 */
define('DB_DEF_ID',	101);

/**
 * Field Type specifier: Text
 *
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be a string of characters.
 * If used, the DB_FIELD_LEN index must also be specified.
 *
 * @see DB_FIELD_TYPE
 * @see DB_FIELD_LEN
 */
define('DB_DEF_STRING',	102);

/**
 * Field Type specifier: Integer
 *
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be an integer.
 */
define('DB_DEF_INTEGER',	103);

/**
 * Field Type specifier: Boolean
 *
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be a boolean (1 or 0, true or
 * false).
 */
define('DB_DEF_BOOLEAN',	104);

/**
 * Field Type specifier: Timestamp
 *
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should store a UNIX timestamp,
 * in order to portably manage dates and/or times.
 */
define('DB_DEF_TIMESTAMP',	105);

/**
 * Field Type specifier: Decimal
 *
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should store a decimal number.
 */
define('DB_DEF_DECIMAL',	106);

/**
 * Table Alteration Error Message - 200 : Alter Not Needed
 *
 * A message constant returned by parts of the Database Subsystem
 * indicating that a table alteration need not take place.
 */
define('TABLE_ALTER_NOT_NEEDED',	200);

/**
 * Table Alteration Error Message - 201 : Alter Succeeded
 *
 * A message constant returned by parts of the Database Subsystem
 * indicating that a table alteration succeeded.
 */
define('TABLE_ALTER_SUCCEEDED',	201);

/**
 * Table Alteration Error Message - 201 : Alter Succeeded
 *
 * A message constant returned by parts of the Database Subsystem
 * indicating that a table alteration failed.
 */
define('TABLE_ALTER_FAILED',	202);


/**
 * Table Meta Info : Workflow Table
 *
 * If specified as true in a table info array, the workflow tables will
 * be created to match.
 */
define('DB_TABLE_WORKFLOW',	300);

/**
 * Table Meta Info : Table Comment
 *
 * If specified in a table info array, a comment will be inserted
 * for the table (if the database engine in use supports table comments)
 */
define('DB_TABLE_COMMENT',	301);

/**
 * Form Meta Info : Form Field Type
 * 
 * This will specify what field type to use for a form.  Handy for scaffolding
 * when you have special needs for the form's input elements.
 */
define('FORM_FIELD_TYPE', 400);
define('FORM_FIELD_FILTER', 401);
define('FORM_FIELD_ONCLICK', 402);
define('FORM_FIELD_NAME', 403);
define('FORM_FIELD_LABEL', 404);
define('DECIMAL_MONEY', 405);
define('MONEY', 406);


if (!defined('DB_ENGINE')) {
	$backends = array_keys(exponent_database_backends(1));
	if (count($backends)) {
		define('DB_ENGINE',$backends[0]);
	} else {
		define('DB_ENGINE','NOTSUPPORTED');
	}
}

(include_once(BASE.'subsystems/database/'.DB_ENGINE.'.php')) or exit('None of the installed Exponent Database Backends will work with this server\'s version of PHP.');

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
function exponent_database_backends($valid_only = 1) {
	$options = array();
	$dh = opendir(BASE.'subsystems/database');
	while (($file = readdir($dh)) !== false) {
		if (is_file(BASE.'subsystems/database/'.$file) && is_readable(BASE.'subsystems/database/'.$file) && substr($file,-9,9) == '.info.php') {
			$info = include(BASE.'subsystems/database/'.$file);
			if ($info['is_valid'] == 1 || !$valid_only) {
				$options[substr($file,0,-9)] = $info['name'];
			}
		}
	}
	return $options;
}

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
 * @return mysqli_database the exponent database object
 */
function exponent_database_connect($username,$password,$hostname,$database,$dbclass = '',$new=false) {
	if ($dbclass == '' || $dbclass == null) $dbclass = DB_ENGINE;
	(include_once(BASE.'subsystems/database/'.$dbclass.'.php')) or exit('The specified database backend  ('.$dbclass.') is not supported by Exponent');
	$dbclass .= '_database';
	$newdb = new $dbclass();
	$newdb->connect($username,$password,$hostname,$database,$new);
	return $newdb;
}

?>
