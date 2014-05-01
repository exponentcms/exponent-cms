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
 * Smarty {format_tooltip} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     format_tooltip<br>
 * Purpose:  shorten and strip a string
 *
 * @param string $text
 * @param int    $length
 *
 * @return array
 */
function smarty_modifier_format_tooltip($text='', $length=77) {
	$text = strip_tags($text);
	if (strlen($text) > $length) {
		$text = substr($text, 0, $length)."...";
	}
	return $text;
}

?>
