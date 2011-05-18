<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

if (!defined('DISPLAY_LANGUAGE')) define('DISPLAY_LANGUAGE','en');
if (!defined('SLINGBAR_TOP')) define('SLINGBAR_TOP','1');
if (!defined('LANGUAGE')) define("LANGUAGE",'English - US');
if (!defined('WRITE_LANG_TEMPLATE')) define('WRITE_LANG_TEMPLATE','0');
if (!defined('DISPLAY_THEME_REAL')) define('DISPLAY_THEME_REAL','retrotheme');
if (!defined('DISPLAY_ATTRIBUTION')) define('DISPLAY_ATTRIBUTION','username');
if (!defined('DISPLAY_DATETIME_FORMAT')) define('DISPLAY_DATETIME_FORMAT','%D -- %T');
if (!defined('DISPLAY_DATE_FORMAT')) define('DISPLAY_DATE_FORMAT','%D');
if (!defined('DISPLAY_TIME_FORMAT')) define('DISPLAY_TIME_FORMAT','%l:%M%p');
if (!defined('DISPLAY_START_OF_WEEK')) define('DISPLAY_START_OF_WEEK','0');
if (!defined('DISPLAY_DEFAULT_TIMEZONE')) define ('DISPLAY_DEFAULT_TIMEZONE', function_exists('date_default_timezone_get') ? @date_default_timezone_get() : null);
if (!defined('THUMB_QUALITY')) define('THUMB_QUALITY','75');
?>
