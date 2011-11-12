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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {get_object} function plugin
 *
 * Type:     function<br>
 * Name:     get_object<br>
 * Purpose:  get and assign object
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_get_object($params,&$smarty) {
	$obj = new $params['object'];
	$smarty->assign($params['assign'],$obj);
}

?>
