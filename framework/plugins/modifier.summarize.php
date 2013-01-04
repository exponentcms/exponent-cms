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
 * Smarty {summarize} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     summarize<br>
 * Purpose:  shorten and flatten a string removing some or all markup
 *
 * @param $string
 * @param $strtype
 * @param $type
 *
 * @return array
 */
function smarty_modifier_summarize($string, $strtype, $type) {
	$sep = ($strtype == "html" ? array("</p>","</div>") : array("\r\n","\n","\r"));
	$origstring = $string;
	
	switch ($type) {
		case "para":
			foreach ($sep as $s) {
				$para = explode($s,$string);
				$string = $para[0];
			}
			if (strlen($string) < strlen($origstring)-4) {$string .= " ...";}
//			return str_replace("&amp;#160;"," ",htmlentities(expString::convertSmartQuotes(strip_tags($string)),ENT_QUOTES));
			return expString::convertSmartQuotes(strip_tags($string));
			break;
		case "paralinks":
			foreach ($sep as $s) {
				$para = explode($s,$string);
				$string = $para[0];
			}
			if (strlen($string) < strlen($origstring)-4) {$string .= " ...";}
//			return str_replace("&#160;"," ",htmlspecialchars_decode(htmlentities(expString::convertSmartQuotes(strip_tags($string,'<a>')),ENT_QUOTES)));
			return expString::convertSmartQuotes(strip_tags($string,'<a>'));
			break;
		default:
			$words = explode(" ",strip_tags($string));
			$string = implode(" ",array_slice($words,0,$type+0));
			if (strlen($string) < strlen($origstring)-4) {$string .= " ...";}
//			return str_replace("&amp;#160;"," ",htmlentities(expString::convertSmartQuotes($string),ENT_QUOTES));
			return expString::convertSmartQuotes($string);
			break;
	}
}

?>
