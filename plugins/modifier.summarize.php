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
			return str_replace("&amp;#160;"," ",htmlentities(convert_smart_quotes(strip_tags($string)),ENT_QUOTES));
			break;
		case "paralinks":
			foreach ($sep as $s) {
				$para = explode($s,$string);
				$string = $para[0];
			}
			if (strlen($string) < strlen($origstring)-4) {$string .= " ...";}
			return str_replace("&#160;"," ",htmlspecialchars_decode(htmlentities(convert_smart_quotes(strip_tags($string,'<a>')),ENT_QUOTES)));
			break;			
		default:
			$words = explode(" ",strip_tags($string));
			$string = implode(" ",array_slice($words,0,$type+0));
			if (strlen($string) < strlen($origstring)-4) {$string .= " ...";}
			return str_replace("&amp;#160;"," ",htmlentities(convert_smart_quotes($string),ENT_QUOTES));
			break;
	}
}
	 
function convert_smart_quotes($str) {
	 // $search = array(chr(145),
					 // chr(146),
					 // chr(147),
					 // chr(148),
					 // chr(150),
					 // chr(151),
					 // chr(133),
					 // chr(149));
	 // $replace = array("'z",
					  // "'z",
					  // "\"z",
					  // "\"z",
					  // "-z",
					  // "-z",
					  // "...",
					  // "&bull;");
	 // return str_replace($search, $replace, $str);

	$find[] = '“';  // left side double smart quote
	$find[] = '”';  // right side double smart quote
	$find[] = '‘';  // left side single smart quote
	$find[] = '’';  // right side single smart quote
	$find[] = '…';  // elipsis
	$find[] = '—';  // em dash
	$find[] = '–';  // en dash

	$replace[] = '"';
	$replace[] = '"';
	$replace[] = "'";
	$replace[] = "'";
	$replace[] = "...";
	$replace[] = "-";
	$replace[] = "-";

	return str_replace($find, $replace, $str);
}

?>
