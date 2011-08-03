<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright 2006 Maxim Mueller
# Written and Designed by James Hunt
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
if (!defined('EXPONENT')) exit('');

return array(
	'uid'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true),
	'SITE_WYSIWYG_EDITOR'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>20),
	'USE_LANG'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>6)
);

?>