<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# Exponent is distributed in the hope that it
# will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR
# PURPOSE.  See the GNU General Public License
# for more details.
#
# You should have received a copy of the GNU
# General Public License along with Exponent; if
# not, write to:
#
# Free Software Foundation, Inc.,
# 59 Temple Place,
# Suite 330,
# Boston, MA 02111-1307  USA
#
# $Id: function.currency_symbol.php,v 1.1 2005/07/15 00:59:27 cvs Exp $
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