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
 * @package Core
 */
return array(
	'expratings_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
        DB_PRIMARY=>true,
        DB_INCREMENT=>false),
	'content_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
        DB_PRIMARY=>true,
        DB_INCREMENT=>false),
	'content_type'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>50,
        DB_NOTNULL=>true,
		DB_PRIMARY=>true,
		DB_INCREMENT=>false),
	'subtype'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>50,
        DB_NOTNULL=>true,
        DB_PRIMARY=>true,
        DB_INCREMENT=>false),
	'poster'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
);

?>