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
	"id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	"orders_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_INDEX=>10),
	"user_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'product_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_INDEX=>10),
	'product_type'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1000,
		DB_INDEX=>10),
	'products_name'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>255,
		DB_INDEX=>20),
    "products_model"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
    "products_warehouse_location"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
    "products_status"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100), 
	'products_price'=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'products_price_adjusted'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'products_tax'=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"shippingmethods_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'quantity'=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'extra_data'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
    'user_input_fields'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>10000),
	'options'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
);

?>
