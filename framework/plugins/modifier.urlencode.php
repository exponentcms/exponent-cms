<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * Smarty {urlencode} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     urlencode<br>
 * Purpose:  urlencode a string
 *
 * @param $string
 * @param $ignore_whitespace
 *
 * @return string
 *
 * @package Smarty-Plugins
 * @subpackage Modifier
 */
function smarty_modifier_urlencode($string,$ignore_whitespace=false) {
	if ($ignore_whitespace) $string = trim($string);
	return urlencode($string);
}

?>