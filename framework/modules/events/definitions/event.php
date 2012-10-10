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
    'title'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>100),
   	'body'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>100000),
    'poster'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'created_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'editor'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'edited_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'location_data'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200,
		DB_INDEX=>10),
	'eventstart'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP,
		DB_INDEX=>0),
	'eventend'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP,
		DB_INDEX=>0),
	'is_allday'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_featured'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_recurring'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'feedback_form'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'feedback_email'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1000),
);

?>
