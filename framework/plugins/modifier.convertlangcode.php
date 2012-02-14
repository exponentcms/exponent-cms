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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Modifier
 */

/**
 * Smarty {convertlangcode} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     convertlangcode<br>
 * Purpose:  attempt to convert language codes between different formats
 *
 * @param $lang_code
 * @param string $target
 * @return array
 */
function smarty_modifier_convertlangcode($lang_code, $target = "iso639-1") {
	return $lang_code; //FIXME this plugin isn't used, but this will at least return something
//	return exponent_lang_convertLangCode($lang_code, $target);
}

?>