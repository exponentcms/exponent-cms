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
 * @subpackage Definitions
 * @package Core
 */
return array(
	'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	'name'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'skin'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'data'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
	'plugins'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>255),
	'scayt_on'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'paste_word'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'active'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'stylesset'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>255),
    'formattags'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>255),
    'fontnames'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>255),
);

?>