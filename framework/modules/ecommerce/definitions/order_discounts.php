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
        DB_FIELD_TYPE=>DB_DEF_ID),
    "discounts_id"=>array(
        DB_FIELD_TYPE=>DB_DEF_ID,
        DB_INDEX=>10),        
    'title'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>200),
    'body'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>100000),
    "coupon_code"=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>200),     
    'discount_total'=>array(
        DB_FIELD_TYPE=>DB_DEF_DECIMAL,
        FORM_FIELD_FILTER=>MONEY),    
    "created_at"=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    "edited_at"=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
);

?>
