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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Modifier
 */

/**
 * Smarty {currency} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     currency<br>
 * Purpose:  format a number as currency
 *
 * @param $number
 * @param $decimals
 *
 * @return array
 */
function smarty_modifier_currency($number,$decimals=2) {
    $number = ($number=="") ? 0 : $number;
	return expCore::getCurrencySymbol() . number_format($number,$decimals,".",",");
}

?>