<?php

##################################################
#
# Copyright (c) 2004-2020 OIC Group, Inc.
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
 * @subpackage Definitions
 * @package Modules
 */
return array(
	'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	'username'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>255),
	'password'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>128),
	'is_admin'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_acting_admin'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_locked'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'firstname'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'lastname'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'email'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>255),
	'recv_html'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"created_on"=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	"last_login"=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	'is_ldap'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'is_system_user'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
);

?>
