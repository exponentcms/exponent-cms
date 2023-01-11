<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * Smarty {kilobytes} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     kilobytes<br>
 * Purpose:  convert to kilobytes
 *
 * @param array
 *
 * @return string
 *
 * @package Smarty-Plugins
 * @subpackage Modifier
 */
function smarty_modifier_kilobytes($bytes) {
	return round($bytes/1024, 2);
}

?>
