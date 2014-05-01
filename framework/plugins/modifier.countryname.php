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
 * @subpackage Modifier
 */

/**
 * Smarty {countryname} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     countryname<br>
 * Purpose:  lookup a country's name based a country id
 *
 * @param        array
 * @param string $col
 *
 * @return array
 */
function smarty_modifier_countryname($country,$col='name') {
	global $db;
	if ($col != 'name') $col = 'code';
	return $db->selectValue('geo_country', $col, 'id='.intval($country));
}

?>
