<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
    'event_starttime'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'event_endtime'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'signup_cutoff'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'number_of_registrants'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'registrants'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>10000),
    'poster'=>array(
        DB_FIELD_TYPE=>DB_DEF_INTEGER),
    'created_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'edited_at'=>array(
        DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
    'location_data'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>250,
        DB_INDEX=>10),
);

?>
