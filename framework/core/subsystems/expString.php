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
 * This is the class expString
 *
 * @package Subsystems
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

        $replace[] = '"';
       	$replace[] = '"';
       	$replace[] = "'";
       	$replace[] = "'";
       	$replace[] = "...";
       	$replace[] = "-";
       	$replace[] = "-";

        $find[] = '“';  // left side double smart quote
        $find[] = '”';  // right side double smart quote
        $find[] = '‘';  // left side single smart quote
        $find[] = '’';  // right side single smart quote
        $find[] = '…';  // ellipsis
        $find[] = '—';  // em dash
        $find[] = '–';  // en dash

        $replace[] = '"';
        $replace[] = '"';
        $replace[] = "'";
        $replace[] = "'";
        $replace[] = "...";
        $replace[] = "-";
        $replace[] = "-";

//        $find[] = chr(145);
//        $find[] = chr(146);
//        $find[] = chr(147);
//        $find[] = chr(148);
//        $find[] = chr(150);
//        $find[] = chr(151);
//        $find[] = chr(133);
//        $find[] = chr(149);
//        $find[] = chr(11);
//
//        $replace[] = "'";
//        $replace[] = "'";
//        $replace[] = "\"";
//        $replace[] = "\"";
//        $replace[] = "-";
//        $replace[] = "-";
//        $replace[] = "...";
//        $replace[] = "&bull;";
//        $replace[] = "\n";

    	return str_replace($find, $replace, $str);
    }

    /**
     * Scrub input string for possible security issues.
     *
     * @static
     * @param $data string
     * @return string
     */
    public static function sanitize(&$data) {
        global $db;

        if (empty($data)) return $data;

        // remove whitespaces and tags
//        $data = strip_tags(trim($data));
        // remove whitespaces and script tags
        $data = self::strip_tags_content(trim($data), '<script>', true);

        // apply stripslashes if magic_quotes_gpc is enabled
        if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            $data = stripslashes($data);
        }

//        // a mySQL connection is required before using this function
//        if ($db->havedb) {
//            $data = $db->escapeString($data);
//        } else {
//            $data = self::escape($data);
//        }
//
//        // re-escape newlines
//        $data = str_replace(array('\r', '\n'), array("\r", "\n"), $data);

        // remove old tag fragments
//        $data = str_replace(array('\">','\"/>'), '', $data);
        return $data;
    }

    /**
     * Scrub an input array for possible security issues.
     *
     * @static
     * @param $data array
     * @return string/array
     */
    public static function sanitize_array(array &$data)
    {
        if (is_string($data)) return self::sanitize($data);

        // code snippets must be allowed to pass <script> tags
        $saved_params = array();
        if (!empty($data['controller']) && $data['controller'] == 'snippet') {
            $saved_params['body'] = $data['body'];
        }
        array_walk_recursive($data, array('self','sanitize'));
        if (!empty($saved_params)) {
            $data = array_merge($data, $saved_params);
        }
        return $data;
    }

    /**
     * Enhanced variation of strip_tags with 'invert' option to remove specific tags
     *
     * @param $text
     * @param string $tags
     * @param bool $invert
     * @return mixed
     */
    public static function strip_tags_content($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) AND count($tags) > 0) {
            if ($invert == false) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == false) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    /**\
     * Replace any non-ascii character with its hex code with NO active db connection
     */
    function escape($value) {
        $return = '';
        for($i = 0; $i < strlen($value); ++$i) {
            $char = $value[$i];
            $ord = ord($char);
            if($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
                $return .= $char;
            else
                $return .= '\\x' . dechex($ord);
        }
        return $return;
    }

    /**
     * Summarize or short a long string
     *
     * @param        $string
     * @param string $strtype
     * @param string $type
     *
     * @return string
     */
    public static function summarize($string, $strtype='html', $type='para') {
        $sep = ($strtype == "html" ? array("</p>", "</div>") : array("\r\n", "\n", "\r"));
        $origstring = $string;

        switch ($type) {
            case "para":
                foreach ($sep as $s) {
                    $para = explode($s, $string);
                    $string = $para[0];
                }
                if (strlen($string) < strlen($origstring)) {
                    $string .= " ...";
                }
    //			return str_replace("&amp;#160;"," ",htmlentities(expString::convertSmartQuotes(strip_tags($string)),ENT_QUOTES));
                return expString::convertSmartQuotes(strip_tags($string));
                break;
            case "paralinks":
                foreach ($sep as $s) {
                    $para = explode($s, $string);
                    $string = $para[0];
                }
                if (strlen($string) < strlen($origstring)) {
                    $string .= " ...";
                }
    //			return str_replace("&#160;"," ",htmlspecialchars_decode(htmlentities(expString::convertSmartQuotes(strip_tags($string,'<a>')),ENT_QUOTES)));
                return expString::convertSmartQuotes(strip_tags($string, '<a>'));
                break;
            case "parapaged":
//               $s = '<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>';
                $s = '<div style="page-break-after: always';
                $para = explode($s, $string);
                $string = $para[0];
                return expString::convertSmartQuotes($string);
                break;
            case "parahtml":
                foreach ($sep as $s) {
                    $para = explode($s, $string);
                    $string = $para[0];
                }
                if (strlen($string) < strlen($origstring)) {
                    $string .= " ...";
                }
                if (!empty($string)) {
                    $isText = true;
                    $ret = "";
                    $i = 0;
    //                    $currentChar = "";
    //                    $lastSpacePosition = -1;
    //                    $lastChar = "";
                    $tagsArray = array();
                    $currentTag = "";
    //                    $tagLevel = 0;
    //                    $noTagLength = strlen(strip_tags($string));

                    // Parser loop
                    for ($j = 0; $j < strlen($string); $j++) {

                        $currentChar = substr($string, $j, 1);
                        $ret .= $currentChar;

                        // Lesser than event
                        if ($currentChar == "<") $isText = false;

                        // Character handler
                        if ($isText) {

                            // Memorize last space position
                            if ($currentChar == " ") {
                                $lastSpacePosition = $j;
                            } else {
                                $lastChar = $currentChar;
                            }

                            $i++;
                        } else {
                            $currentTag .= $currentChar;
                        }

                        // Greater than event
                        if ($currentChar == ">") {
                            $isText = true;

                            // Opening tag handler
                            if ((strpos($currentTag, "<") !== FALSE) &&
                                (strpos($currentTag, "/>") === FALSE) &&
                                (strpos($currentTag, "</") === FALSE)
                            ) {

                                // Tag has attribute(s)
                                if (strpos($currentTag, " ") !== FALSE) {
                                    $currentTag = substr($currentTag, 1, strpos($currentTag, " ") - 1);
                                } else {
                                    // Tag doesn't have attribute(s)
                                    $currentTag = substr($currentTag, 1, -1);
                                }

                                array_push($tagsArray, $currentTag);

                            } else if (strpos($currentTag, "</") !== FALSE) {
                                array_pop($tagsArray);
                            }

                            $currentTag = "";
                        }
                    }
                    // Cut HTML string at last space position
                    //                if ($length < $noTagLength) {
                    //                    if ($lastSpacePosition != -1) {
                    //                        $ret = substr($string, 0, $lastSpacePosition);
                    //                    } else {
                    //                        $ret = substr($string, $j);
                    //                    }
                    //                }
                    if (sizeof($tagsArray) != 0) {
                        // Close broken XHTML elements
                        while (sizeof($tagsArray) != 0) {
                            if (sizeof($tagsArray) > 1) {
                                $aTag = array_pop($tagsArray);
                                $string .= "</" . $aTag . ">";
                            } // You may add more tags here to put the link and added text before the closing tag
                            elseif ($aTag == 'p' || 'div') {
                                $aTag = array_pop($tagsArray);
                                $string .= "</" . $aTag . ">";
                            } else {
                                $aTag = array_pop($tagsArray);
                                $string .= "</" . $aTag . ">";
                            }
                        }
                    }
                }
                return expString::convertSmartQuotes($string);
                break;
            default:
                $words = explode(" ", strip_tags($string));
                $string = implode(" ", array_slice($words, 0, $type + 0));
                if (strlen($string) < strlen($origstring)) {
                    $string .= " ...";
                }
    //			return str_replace("&amp;#160;"," ",htmlentities(expString::convertSmartQuotes($string),ENT_QUOTES));
                return expString::convertSmartQuotes($string);
                break;
        }
    }

    public static function parseAndTrimExport($str, $isHTML = false) { //�Death from above�? �
        //echo "1<br>"; eDebug($str);

        $str = str_replace("�", "&rsquo;", $str);
        $str = str_replace("�", "&lsquo;", $str);
        $str = str_replace("�", "&#174;", $str);
        $str = str_replace("�", "-", $str);
        $str = str_replace("�", "&#151;", $str);
        $str = str_replace("�", "&rdquo;", $str);
        $str = str_replace("�", "&ldquo;", $str);
        $str = str_replace("\r\n", " ", $str);
        $str = str_replace("\t", " ", $str);
        $str = str_replace(",", "\,", $str);
        $str = str_replace("�", "&#188;", $str);
        $str = str_replace("�", "&#189;", $str);
        $str = str_replace("�", "&#190;", $str);

        if (!$isHTML) {
            $str = str_replace('\"', "&quot;", $str);
            $str = str_replace('"', "&quot;", $str);
        } else {
            $str = str_replace('"', '""', $str);
        }

        //$str = htmlspecialchars($str);
        //$str = utf8_encode($str);
        $str = trim(str_replace("�", "&trade;", $str));
        //echo "2<br>"; eDebug($str,die);
        return $str;
    }

    public static function parseAndTrimImport($str, $isHTML = false) { //�Death from above�? �
        //echo "1<br>"; eDebug($str);
        global $db;

        $str = str_replace("�", "&rsquo;", $str);
        $str = str_replace("�", "&lsquo;", $str);
        $str = str_replace("�", "&#174;", $str);
        $str = str_replace("�", "-", $str);
        $str = str_replace("�", "&#151;", $str);
        $str = str_replace("�", "&rdquo;", $str);
        $str = str_replace("�", "&ldquo;", $str);
        $str = str_replace("\r\n", " ", $str);
        $str = str_replace("\,", ",", $str);
        $str = str_replace('""', '"', $str); //do this no matter what...in case someone added a quote in a non HTML field
        if (!$isHTML) {
            //if HTML, then leave the single quotes alone, otheriwse replace w/ special Char
            $str = str_replace('"', "&quot;", $str);
        }
        $str = str_replace("�", "&#188;", $str);
        $str = str_replace("�", "&#189;", $str);
        $str = str_replace("�", "&#190;", $str);
        //$str = htmlspecialchars($str);
        //$str = utf8_encode($str);
//        if (DB_ENGINE=='mysqli') {
//	        $str = @mysqli_real_escape_string($db->connection,trim(str_replace("�", "&trade;", $str)));
//        } elseif(DB_ENGINE=='mysql') {
//            $str = @mysql_real_escape_string(trim(str_replace("�", "&trade;", $str)),$db->connection);
//        } else {
//	        $str = trim(str_replace("�", "&trade;", $str));
//        }
        $str = @$db->escapeString($db->connection, trim(str_replace("�", "&trade;", $str)));
        //echo "2<br>"; eDebug($str,die);
        return $str;
    }

    public static function outputField($val, $eof = ',', $isHTML = false) {
        $newVal = self::parseAndTrimExport($val, $isHTML);
        if ($newVal != '') return '"' . $newVal . '"' . $eof;
        else return $eof;
    }

}

?>