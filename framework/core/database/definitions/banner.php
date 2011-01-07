<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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
	'title'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'body'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100000),
	'url'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'companies_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'impressions'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'clicks'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'impression_limit'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'click_limit'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'poster'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'created_at'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	'editor'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'edited_at'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP)
);

?>
