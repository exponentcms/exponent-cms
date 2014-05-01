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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {plural} function plugin
 *
 * Type:     function<br>
 * Name:     plural<br>
 * Purpose:  test and return a singular or plural form of phrase
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_plural($params,&$smarty) {
	if ($params['count'] == 1) return $params['singular'];
	else return $params['plural'];
}

?>