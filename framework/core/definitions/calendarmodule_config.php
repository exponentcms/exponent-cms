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
	'location_data'=>array(
    	DB_FIELD_TYPE=>DB_DEF_STRING,
    	DB_FIELD_LEN=>200,
        DB_INDEX=>10),
	'enable_categories'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'enable_feedback'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'enable_tags'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'collections'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>1000,
        DB_INDEX=>10),
	'group_by_tags'=>array(
        DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'show_tags'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>1000,
        DB_INDEX=>10),
	'aggregate'=>array(
        DB_FIELD_TYPE=>DB_DEF_STRING,
        DB_FIELD_LEN=>1000),
	'enable_rss'=>array(
	    DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'enable_ical'=>array(
	    DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'feed_title'=>array(
    	DB_FIELD_TYPE=>DB_DEF_STRING,
	    DB_FIELD_LEN=>75),
	'feed_desc'=>array(
    	DB_FIELD_TYPE=>DB_DEF_STRING,
	    DB_FIELD_LEN=>200),	
	'rss_limit'=>array(
	    DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'rss_cachetime'=>array(
	    DB_FIELD_TYPE=>DB_DEF_INTEGER),		
	'reminder_notify'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>200),		
	"email_title_reminder"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>250),
	"email_from_reminder"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	"email_address_reminder"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	"email_reply_reminder"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100),
	"email_showdetail"=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	"email_signature"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>500),	
    'printlink'=>array(
   	    DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'hidemoduletitle'=>array(
   		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
    'moduledescription'=>array(
   		DB_FIELD_TYPE=>DB_DEF_STRING,
   		DB_FIELD_LEN=>10000),
);

?>