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
 * Smarty {get_cart_summary} function plugin
 *
 * Type:     function<br>
 * Name:     get_cart_summary<br>
 * Purpose:  get summary of cart contents
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_get_cart_summary($params,&$smarty) {
	$product = new $params['item']->product_type($params['item']->product_id);
	echo $product->cartSummary($params['item']);
}

?>

