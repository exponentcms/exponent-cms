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
	"invoice_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	"user_id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_INDEX=>10),
	"sessionticket_ticket"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>255,
		DB_INDEX=>10),
	"to"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	"from"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	"comments"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100000),
	"updated"=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	"purchased"=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	"shipped"=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
	"shipping_tracking_number"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
    "shippingmethods_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
	'order_status_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'order_type_id'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'subtotal'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'total_discounts'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'total'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'tax'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'shipping_total'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'surcharge_total'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'grand_total'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'orig_referrer'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>2000),
    'poster'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'created_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'editor'=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
    'edited_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    "order_references"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>2000),
    "sales_rep_1_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    "sales_rep_2_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    "sales_rep_3_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    "return_count"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>2000),
        
);

?>
