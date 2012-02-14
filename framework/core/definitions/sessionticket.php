<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * @subpackage Core-Definitions
 * @package Framework
 */
return array(
	'ticket'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>23, // uniqid('',true) returns 23-char strings
		DB_PRIMARY=>true),
	'uid'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'last_active'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	'refresh'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'referrer'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1000),
	'ip_address'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>15),
	'start_time'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	'browser'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>250),
/*	'last_section'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'last_action'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'last_module'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'last_action_descriptive'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>250)
*/	
);

?>