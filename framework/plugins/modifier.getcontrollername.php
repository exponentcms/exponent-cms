<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * Smarty {getcontrollername} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     getcontrollername<br>
 * Purpose:  Return the module name for this module
 * 
 * @param string $str
 * @return string
 */
function smarty_modifier_getcontrollername($str) {
	return expModules::getControllerName($str);
}
