<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
 * Smarty {statename} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     statename<br>
 * Purpose:  lookup a state's name based o state id
 *
 * @param        array
 * @param string $col
 *
 * @return string
 */
function smarty_modifier_statename($state,$col='name') {
	global $db;
	if ($col != 'name') $col = 'code';
	return $db->selectValue('geo_region', $col, 'id='.(int)($state));
}

?>
