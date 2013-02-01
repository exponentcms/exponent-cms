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
 * @package Modules
 */
return array(
    'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
        DB_PRIMARY=>true,
        DB_INCREMENT=>true),
	'module'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200,
        DB_INDEX=>10),
	'src'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200,
        DB_INDEX=>10),
    'ealert_title'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'ealert_desc'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100000),
    'autosend_ealerts'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'ealert_usebody'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
);

?>
