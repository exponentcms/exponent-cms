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
 * Smarty {remove_space} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     remove_space<br>
 * Purpose:  remove spaces, replacing them with underscores
 *
 * @param array
 * @return array
 */
function smarty_modifier_remove_space($string) {
		return str_replace(' ', '_', $string);
}

?>