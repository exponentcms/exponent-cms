<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * @package Core
 */
return array(
	'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_INCREMENT=>true,
		DB_PRIMARY=>true),
	'name'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'sef_name'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'subtheme'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'public'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'active'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'new_window'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'parent'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_INDEX=>10),
	'rank'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'page_title'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'keywords'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
	'description'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
	'secured'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'alias_type'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'external_link'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1024),
	'internal_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID)
);

?>
