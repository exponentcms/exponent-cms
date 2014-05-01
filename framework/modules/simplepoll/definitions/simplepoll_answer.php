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
	'simplepoll_question_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
        DB_INDEX=>10),
	'answer'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>4500),
	'rank'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'vote_count'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER)
);

?>