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
    'title'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),
    'body'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100000),
    'enabled'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'coupon_code'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>50),
    'startdate'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'startdate_time'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'enddate'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'enddate_time'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'never_expires'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'uses_per_coupon'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'uses_per_user'=>array( 
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'allow_other_coupons'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'group_ids'=>array( 
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100000),
    'minimum_order_amount'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL,
        FORM_FIELD_FILTER=>MONEY),
    'action_type'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),   
    'discount_amount'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL,
        FORM_FIELD_FILTER=>MONEY),
    'shipping_discount_amount'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL,
        FORM_FIELD_FILTER=>MONEY),
    'discount_percent'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL),
    "required_shipping_calculator_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_ID),
    "required_shipping_method"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100),    
    'created_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'edited_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
);

?>
