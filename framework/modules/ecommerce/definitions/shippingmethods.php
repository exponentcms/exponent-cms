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
    //FIXME needed to activate the has_many assignment
//    'orders_id'=>array(
//        DB_FIELD_TYPE=>DB_DEF_ID,
//        DB_INDEX=>10),

    // shipping address
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

    // shipping details
	'shippingcalculator_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'option'=>array(  // method by id, may include carrier if multi_carrier
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>50),
	'option_title'=>array(  // method by title
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
    'carrier'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
	'shipping_cost'=>array(  // estimated cost on order
		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'delivery' => array(  // expected delivery date  //FIXME is this a 'shipping_options' item??
        DB_FIELD_TYPE => DB_DEF_TIMESTAMP),
    //FIXME we probably will need to add a package rate id, tracking number, etc...
    'shipping_options'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>10000),
//NOTE for easypost this would include
//from shipment create
//  * shipment_id
//  * shipment_rates (array???)
//from shipment buy
//  * shipment_cost  // actual cost
//  * shipment_date
//  * shipment_tracking_number
//  * shipment_label (url)
//we set this
//  * shipment_status (created, purchased, cancelled/refund)
//from pickup create
//  * pickup_id
//  * pickup_instructions
//  * pickup_rates (array???)
//from pickup buy
//  * pickup_cost  // actual cost
//  * pickup_date
//we set this
//  * pickup_status (created, purchased, cancelled)
    'predefinedpackage' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 100),
    'height'=>array(
     	  DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'width'=>array(
     	  DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'length'=>array(
   	  DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    'weight'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),

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
