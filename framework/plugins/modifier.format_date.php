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
 * Smarty {format_date} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     format_date<br>
 * Purpose:  format a date
 *
 * @param        array
 * @param string $format
 *
 * @return array
 */
function smarty_modifier_format_date($timestamp,$format=DISPLAY_DATE_FORMAT) {
	// Do some sort of mangling of the format for windows.
	// reference the PHP_OS constant to figure that one out.
	if (strtolower(substr(PHP_OS,0,3)) == 'win') {
		// We are running on a windows platform.  Run the replacements
		
		// Preserve the '%%'
		$toks = explode('%%',$format);
		for ($i = 0; $i < count($toks); $i++) {
			$toks[$i] = str_replace(
				array('%D','%e','%g','%G','%h','%r','%R','%T','%l'),
				array('%m/%d/%y','%#d','%y','%Y','%b','%I:%M:%S %p','%H:%M','%H:%M:%S','%#I'),
				$toks[$i]);
		}
		$format = implode('%%',$toks);
	}
	return strftime($format,$timestamp);
}

?>