<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
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
    //FIXME needed for has_many assignment
//    'orders_id'=>array(
//        DB_FIELD_TYPE=>DB_DEF_ID,
//        DB_INDEX=>10),
	'addresses_id'=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
	'firstname'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'middlename'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'lastname'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'organization'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'address1'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150),
	'address2'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150),
	'city'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'state'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>2),
    'non_us_state'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
	'zip'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>10),
	'country'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>50),
	'phone'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>20),
	'email'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>150),
	'shippingcalculator_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'option'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>50),
	'option_title'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	'shipping_cost'=>array(
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'carrier'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
    'delivery' => array(
        DB_FIELD_TYPE => DB_DEF_TIMESTAMP),
    //FIXME we probably will need to add a package rate id, tracking number, etc...
//    "shipment_id" => array(
//        DB_FIELD_TYPE => DB_DEF_STRING,
//        DB_FIELD_LEN => 100),
//    "shipped" => array(
//        DB_FIELD_TYPE => DB_DEF_TIMESTAMP),
//    "shipping_tracking_number" => array(
//        DB_FIELD_TYPE => DB_DEF_STRING,
//        DB_FIELD_LEN => 100),
//    "weight"=>array(
//        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
//    "height"=>array(
//     	  DB_FIELD_TYPE=>DB_DEF_DECIMAL),
//    "width"=>array(
//     	  DB_FIELD_TYPE=>DB_DEF_DECIMAL),
//    "length"=>array(
//   	  DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    // shipping gift message
    'to'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>100),
   	'from'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>100),
   	'message'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>1000),
);

?>
