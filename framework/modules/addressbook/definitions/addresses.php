<?php

##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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
	'user_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
        DB_INDEX=>10),
	'is_default'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'is_shipping'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'is_billing'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'firstname'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100,
		DB_FULLTEXT=>true),
	'middlename'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'lastname'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100,
		DB_FULLTEXT=>true),
	'organization'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100,
		DB_FULLTEXT=>true),
	'address1'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150),
	'address2'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150),
	'address_type'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150),
	'city'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'state'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>2),
    'non_us_state'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
	'zip'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10),
	'country'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>50),
	'phone'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>20),
    'phone2'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>20),
    'fax'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>20),
	'email'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150,
		DB_FULLTEXT=>true),
);

?>
