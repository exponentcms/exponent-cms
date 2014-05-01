<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
	'option_master_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'product_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'optiongroup_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'title'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'updown'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'modtype'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),
	'amount'=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	'enable'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'is_default'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
);

?>
