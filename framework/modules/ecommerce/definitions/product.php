<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
    'poster'=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
    'created_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'editor'=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
    'edited_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    "previous_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
	"title"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100,
//		DB_INDEX=>10,
		DB_FULLTEXT=>true),  //fixme is this needed?
    "feed_title"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100,
        DB_INDEX=>10),
	"google_product_type"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100,
        DB_INDEX=>10),
	"bing_product_type"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100,
        DB_INDEX=>10),
	"nextag_product_type"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100,
        DB_INDEX=>10),
	"shopzilla_product_type"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100,
        DB_INDEX=>10),
	"shopping_product_type"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100,
        DB_INDEX=>10),
	"pricegrabber_product_type"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100,
        DB_INDEX=>10),
	'sef_url'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150,
		DB_INDEX=>10,
//		DB_FULLTEXT=>true
	),
    'canonical'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>800),
	'meta_title'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>255),
	'meta_keywords'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
	'meta_description'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
    'meta_fb'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>10000),
	'meta_tw'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
    'noindex'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'nofollow'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"body"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000,
//		DB_INDEX=>10,
		DB_FULLTEXT=>true),  //fixme is this needed?
    "feed_body"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>10000),
//	"summary"=>array(
//		DB_FIELD_TYPE=>DB_DEF_STRING,
//		DB_FIELD_LEN=>500),
	"featured_body"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1000),
	"model"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100,
		DB_INDEX=>10,
		DB_FULLTEXT=>true),  //fixme is this needed?
    "product_status_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
    "active_type"=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
	"no_shipping"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"required_shipping_calculator_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	"required_shipping_method"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	"weight"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"height"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"width"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"length"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    "surcharge"=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"tax_class_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	"base_price"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL,
		FORM_FIELD_FILTER=>MONEY),
	"use_special_price"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"special_price"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"availability_note"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>500),
    "image_alt_tag"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>500),
	"quantity"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"quantity_discount_num_items"=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	"quantity_discount_num_items_mod"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>5),
	"quantity_discount_amount"=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
	"quantity_discount_amount_mod"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>1),
	"quantity_discount_apply"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"availability_type"=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	"allow_partial"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"is_hidden"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"is_featured"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"show_options"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"segregate_options"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"companies_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_INDEX=>10),
	"minimum_order_quantity"=>array(
    	DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    "multiple_order_quantity"=>array(
       	DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'product_type'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100,
		DB_INDEX=>10),
    'main_image_functionality'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>5),
	"product_type_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
    'extra_fields'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10000),
    'user_input_fields'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>10000),
    "parent_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_ID,
        DB_INDEX=>10),
    "child_rank"=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    "warehouse_location"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
);

?>
