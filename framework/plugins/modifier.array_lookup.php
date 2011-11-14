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
 * @subpackage Modifier
 */

/**
 * Smarty {array_lookup} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     array_lookup<br>
 * Purpose:  lookup a key within an array
 *
 * @param string $value
 * @param array  $from
 * @param int    $index
 *
 * @return array
 */
function smarty_modifier_array_lookup($value='', $from=array(), $index=0) {
    if (array_key_exists($value, $from)) {
        return $from[$value][$index];
    }
    return '';
}
?>