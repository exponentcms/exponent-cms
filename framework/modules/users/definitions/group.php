<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
	'name'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
    'redirect'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>200),
	'description'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1000),
	'inclusive'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'prevent_uploads'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'prevent_profile_change'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'hide_exp_menu'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'hide_files_menu'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'hide_pages_menu'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'hide_slingbar'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
//    'display_recyclebin'=>array(
//   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'tax_exempt'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
);

?>
