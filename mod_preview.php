<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','mod_preview.php');

// Initialize the Exponent Framework
include_once('exponent.php');

$i18n = exponent_lang_loadFile('mod_preview.php');

$SYS_FLOW_REDIRECTIONPATH='previewreadonly';

if (is_readable(BASE.'themes/' . DISPLAY_THEME . '/module_preview.php')) {
	// Include the Theme's module_preview.php file if it exists.  Otherwise, we will include the default file later.
	include_once('themes/' . DISPLAY_THEME . '/module_preview.php');
} else if (is_readable(BASE.'module_preview.php')) {
	// Include the default module_preview.php, because we didn't find one in the theme.
	include_once(BASE . 'module_preview.php');
} else {
	echo $i18n['no_preview'];
}

?>