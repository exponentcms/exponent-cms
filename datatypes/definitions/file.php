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
	'directory'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>250),
	'filename'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>250),
	'name'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>250),
	'collection_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'mimetype'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'poster'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'posted'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	'filesize'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'accesscount'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'last_accessed'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	// IMAGES ONLY
	'image_width'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'image_height'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'is_image'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN)
);

?>