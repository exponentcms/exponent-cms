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
 * Smarty {selectobjects} function plugin
 *
 * Type:     function<br>
 * Name:     selectobjects<br>
 * Purpose:  select and assign objects
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_selectobjects($params,&$smarty) {
	global $db;
	$where = isset($params['where']) ? $params['where'] : null;
	$where = isset($params['orderby']) ? $params['orderby'] : null;
	$arr = $db->selectObjects($params['table'], $params['where'], $params['orderby']);
	$smarty->assign($params['item'], $arr);
}

?>
