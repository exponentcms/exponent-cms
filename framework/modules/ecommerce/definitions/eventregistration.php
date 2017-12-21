<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
    'eventdate'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'eventenddate'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'event_starttime'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'event_endtime'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'signup_cutoff'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
//    'num_guest_allowed'=>array(
//        DB_FIELD_TYPE=>DB_DEF_INTEGER),
//    'number_of_registrants'=>array(
//        DB_FIELD_TYPE=>DB_DEF_INTEGER),
//    'registrants'=>array(
//        DB_FIELD_TYPE=>DB_DEF_STRING,
//        DB_FIELD_LEN=>10000),
    'location'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>250),
    'forms_id'    => array(
        DB_FIELD_TYPE => DB_DEF_ID),
    "multi_registrant"=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    "require_terms_and_condition"=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    "terms_and_condition"=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>10000),
    "terms_and_condition_toggle"=>array(
   		DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'earlydiscountdate'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
   	"early_discount_amount"=>array(
   		DB_FIELD_TYPE=>DB_DEF_DECIMAL),
   	"early_discount_amount_mod"=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>1),
   	"use_early_price"=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'poster'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'created_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'editor'=>array(
  		DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'edited_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'location_data'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>250,
        DB_INDEX=>10),
);

?>
