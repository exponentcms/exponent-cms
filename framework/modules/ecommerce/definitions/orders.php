<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
    'id' => array(
        DB_FIELD_TYPE => DB_DEF_ID,
        DB_PRIMARY => true,
        DB_INCREMENT => true
    ),
    'invoice_id' => array(
        DB_FIELD_TYPE => DB_DEF_INTEGER
    ),
    'user_id' => array(
        DB_FIELD_TYPE => DB_DEF_ID,
        DB_INDEX => 10
    ),
    'sessionticket_ticket' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 255,
        DB_INDEX => 10
    ),
    'updated' => array(
        DB_FIELD_TYPE => DB_DEF_TIMESTAMP
    ),
    'purchased' => array(
        DB_FIELD_TYPE => DB_DEF_TIMESTAMP
    ),
    //FIXME we may need to move this to the shippingmethod
    'shipped' => array(
        DB_FIELD_TYPE => DB_DEF_TIMESTAMP
    ),
    //FIXME we may need to move this to the shippingmethod
    'shipping_tracking_number' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 100
    ),
    //FIXME here because we currently only allow one package?
    'shippingmethods_id' => array(
        DB_FIELD_TYPE => DB_DEF_ID
    ),
    //FIXME we may want this since there is only one and NOT many?
//    'billingmethods_id' => array(
//        DB_FIELD_TYPE => DB_DEF_ID
//    ),
    'order_status_id' => array(
        DB_FIELD_TYPE => DB_DEF_INTEGER
    ),
    'order_type_id' => array(
        DB_FIELD_TYPE => DB_DEF_INTEGER
    ),
    'subtotal' => array(
        DB_FIELD_TYPE => DB_DEF_DECIMAL
    ),
    'total_discounts' => array(
        DB_FIELD_TYPE => DB_DEF_DECIMAL
    ),
    'total' => array(
        DB_FIELD_TYPE => DB_DEF_DECIMAL
    ),
    //FIXME is this actual or estimated, move this to the shippingmethod?
    'shipping_total' => array(
        DB_FIELD_TYPE => DB_DEF_DECIMAL
    ),
    'shipping_taxed' => array(
        DB_FIELD_TYPE => DB_DEF_BOOLEAN
    ),
    'tax' => array(
        DB_FIELD_TYPE => DB_DEF_DECIMAL
    ),
    'surcharge_total' => array(
        DB_FIELD_TYPE => DB_DEF_DECIMAL
    ),
    'grand_total' => array(
        DB_FIELD_TYPE => DB_DEF_DECIMAL
    ),
    'orig_referrer' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 2000
    ),
    'poster' => array(
        DB_FIELD_TYPE => DB_DEF_INTEGER
    ),
    'created_at' => array(
        DB_FIELD_TYPE => DB_DEF_TIMESTAMP
    ),
    'editor' => array(
        DB_FIELD_TYPE => DB_DEF_ID
    ),
    'edited_at' => array(
        DB_FIELD_TYPE => DB_DEF_TIMESTAMP
    ),
    'order_references' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 2000
    ),
    'sales_rep_1_id' => array(
        DB_FIELD_TYPE => DB_DEF_INTEGER
    ),
    'sales_rep_2_id' => array(
        DB_FIELD_TYPE => DB_DEF_INTEGER
    ),
    'sales_rep_3_id' => array(
        DB_FIELD_TYPE => DB_DEF_INTEGER
    ),
    'return_count' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 2000
    ),

    //FIXME deprecated order gift message??
    'to' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 100
    ),
    'from' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 100
    ),
    'comments' => array(
        DB_FIELD_TYPE => DB_DEF_STRING,
        DB_FIELD_LEN => 100000
    ),
);

?>
