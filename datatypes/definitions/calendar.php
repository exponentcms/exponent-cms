<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

return array(
	'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	'location_data'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200,
                DB_INDEX=>10),
	'title'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'body'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
	'eventstart'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP,
                DB_INDEX=>0),
	'eventend'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP,
                DB_INDEX=>0),
	'posted'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	'poster'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'edited'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	'editor'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'approved'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_allday'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_featured'=>array(
                DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_recurring'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'category_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'feedback_form'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'feedback_email'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1000),
	'tags'=>array(
                DB_FIELD_TYPE=>DB_DEF_STRING,
                DB_FIELD_LEN=>10000),
	'file_id'=>array(
                DB_FIELD_TYPE=>DB_DEF_ID),
);

?>
