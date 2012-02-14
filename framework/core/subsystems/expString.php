<?php
##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * This is the class expString
 *
 * @package Framework
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expString {

    /**
     * Routine to convert string to UTF
     *
     * @static
     * @param string $string
     * @return string
     */
	static function convertUTF($string) {
		return $string = str_replace('?', '', htmlspecialchars($string, ENT_IGNORE, 'UTF-8'));
	} 

    /**
     * Routine to check if string is valid UTF string
     *
     * @static
     * @param string $string
     * @return bool
     */
	static function validUTF($string) {
		if(!mb_check_encoding($string, 'UTF-8') OR !($string === mb_convert_encoding(mb_convert_encoding($string, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {
			return false;
		}		
		return true;
	}

    /**
     * Routine to strip unreadable characters from string - ascii 32 to 126
     *
     * @static
     * @param string $string
     * @return string
     */
	static function onlyReadables($string) {
		for ($i=0;$i<strlen($string);$i++) {
			$chr = $string{$i};
			$ord = ord($chr);
			if ($ord<32 or $ord>126) {
			$chr = "~";
			$string{$i} = $chr;
			}
		}
		return str_replace("~", "", $string);
	}

    /**
     * Routine to
     *
     * @static
     * @param string $str
     * @param bool $unescape should the string also be unescaped?
     * @return mixed|string
     */
	static function parseAndTrim($str, $unescape=false) {

        $str = str_replace("<br>"," ",$str);
        $str = str_replace("</br>"," ",$str);
        $str = str_replace("<br/>"," ",$str);
        $str = str_replace("<br />"," ",$str);
        $str = str_replace("\r\n"," ",$str);
        $str = str_replace('"',"&quot;",$str);
        $str = str_replace("'","&#39;",$str);
        $str = str_replace("’","&rsquo;",$str);
        $str = str_replace("‘","&lsquo;",$str);
        $str = str_replace("®","&#174;",$str);
        $str = str_replace("–","-", $str);
        $str = str_replace("—","&#151;", $str);
        $str = str_replace("”","&rdquo;", $str);
        $str = str_replace("“","&ldquo;", $str);
        $str = str_replace("¼","&#188;",$str);
        $str = str_replace("½","&#189;",$str);
        $str = str_replace("¾","&#190;",$str);
		$str = str_replace("™","&trade;", $str);
		$str = trim($str);
		
        if ($unescape) {
			$str = stripcslashes($str);  
		} else {
	        $str = addslashes($str);
        }

        return $str;
    }

    /**
     * Routine to convert string to an XML safe string
     *
     * @static
     * @param string $str
     * @return string
     */
	static function convertXMLFeedSafeChar($str) {
		$str = str_replace("<br>","",$str);
        $str = str_replace("</br>","",$str);
        $str = str_replace("<br/>","",$str);
        $str = str_replace("<br />","",$str);
        $str = str_replace("&quot;",'"',$str);
        $str = str_replace("&#39;","'",$str);
        $str = str_replace("&rsquo;","'",$str);
        $str = str_replace("&lsquo;","'",$str);        
        $str = str_replace("&#174;","",$str);
        $str = str_replace("�","-", $str);
        $str = str_replace("�","-", $str); 
        $str = str_replace("�", '"', $str);
        $str = str_replace("&rdquo;",'"', $str);
        $str = str_replace("�", '"', $str);
        $str = str_replace("&ldquo;",'"', $str);
        $str = str_replace("\r\n"," ",$str); 
        $str = str_replace("�"," 1/4",$str);
        $str = str_replace("&#188;"," 1/4", $str);
        $str = str_replace("�"," 1/2",$str);
        $str = str_replace("&#189;"," 1/2",$str);
        $str = str_replace("�"," 3/4",$str);
        $str = str_replace("&#190;"," 3/4",$str);
        $str = str_replace("�", "(TM)", $str);
        $str = str_replace("&trade;","(TM)", $str);
        $str = str_replace("&reg;","(R)", $str);
        $str = str_replace("�","(R)",$str);        
        $str = str_replace("&","&amp;",$str);      
		$str = str_replace(">","&gt;",$str);      		
        return trim($str);
	}

    /**
     * Routine to convert any smart quotes into normal quotes
     *
     * @param string $str
     * @return string
     */
    public static function convertSmartQuotes($str) {
    	$find[] = '�';  // left side double smart quote
    	$find[] = '�';  // right side double smart quote
    	$find[] = '�';  // left side single smart quote
    	$find[] = '�';  // right side single smart quote
    	$find[] = '�';  // elipsis
    	$find[] = '�';  // em dash
    	$find[] = '�';  // en dash

        $find[] = '“';  // left side double smart quote
        $find[] = '”';  // right side double smart quote
        $find[] = '‘';  // left side single smart quote
        $find[] = '’';  // right side single smart quote
        $find[] = '…';  // ellipsis
        $find[] = '—';  // em dash
        $find[] = '–';  // en dash

        $find[] = chr(145);
        $find[] = chr(146);
        $find[] = chr(147);
        $find[] = chr(148);
        $find[] = chr(150);
        $find[] = chr(151);
        $find[] = chr(133);
        $find[] = chr(149);
        $find[] = chr(11);

    	$replace[] = '"';
    	$replace[] = '"';
    	$replace[] = "'";
    	$replace[] = "'";
    	$replace[] = "...";
    	$replace[] = "-";
    	$replace[] = "-";

        $replace[] = '"';
        $replace[] = '"';
        $replace[] = "'";
        $replace[] = "'";
        $replace[] = "...";
        $replace[] = "-";
        $replace[] = "-";

        $replace[] = "'";
        $replace[] = "'";
        $replace[] = "\"";
        $replace[] = "\"";
        $replace[] = "-";
        $replace[] = "-";
        $replace[] = "...";
        $replace[] = "&bull;";
        $replace[] = "\n";

    	return str_replace($find, $replace, $str);
    }

}
?>