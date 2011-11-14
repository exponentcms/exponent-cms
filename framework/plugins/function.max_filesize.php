<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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
 * Smarty {max_filesize} function plugin
 *
 * Type:     function<br>
 * Name:     max_filesize<br>
 * Purpose:  display php max filesize
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_max_filesize($params,&$smarty) {
    $size = ini_get("upload_max_filesize");
	$size_msg = "";
	$type = substr($size,-1,1);
	$shorthand_size = substr($size,0,-1);
	switch ($type) {
		case 'M':
			$size_msg = $shorthand_size . ' MB';
			break;
		case 'K':
			$size_msg = $shorthand_size . ' kB';
			break;
		case 'G': // PHP5 +
			$size_msg = $shorthand_size . ' GB';
			break;
		default:
			if ($size >= 1024*1024*1024) { // Gigs
				$size_msg = round(($size / (1024*1024*1024)),2) . " GB";
			} else if ($size >= 1024*1024) { // Megs
				$size_msg = round(($size / (1024*1024)),2) . " MB";
			} else if ($size >= 1024) { // Kilo
				$size_msg = round(($size / 1024),2) . " kB";
			} else {
				$size_msg = $size . " bytes";
			}
	}

	return $size_msg;
}

?>

