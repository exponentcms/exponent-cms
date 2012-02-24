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
 * @subpackage Function
 */

/**
 * Smarty {currencty_symbol} function plugin
 *
 * Type:     function<br>
 * Name:     currency_symbol<br>
 * Purpose:  create appropriate currency symbol
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_currency_symbol($params,&$smarty) {
	global $db;
	switch (ECOM_CURRENCY) {
		case "USD":
		case "CAD":
		case "AUD":
			echo "$";
			break;
		case "EUR":
			echo "&euro;";
			break;
		case "GBP":
			echo "&#163;";
			break;
		case "JPY":
			echo "&#165;";
			break;
		default:
			echo "$";
	}
}

?>