<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
        for ($i = 0, $iMax = strlen($string); $i < $iMax; $i++) {
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
        if (is_array($str)) {
            $rst = array();
            foreach ($str as $key=>$st) {
                $rst[$key] = self::parseAndTrim($st, $unescape);
            }
            return $rst;
        }

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
    public static function escape($value) {
        global $db;

        if ($db->havedb) {
            return $db->escapeString($value);
        }

        $return = '';
        for ($i = 0, $iMax = strlen($value); $i < $iMax; $i++) {
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
    public static function summarize($string, $strtype='html', $type='para', $more='...') {
        $sep = ($strtype == "html" ? array("</p>", "</div>") : array("\r\n", "\n", "\r"));
        $origstring = $string;

        switch ($type) {
            case "para":
                foreach ($sep as $s) {
                    $para = explode($s, $string);
                    $string = $para[0];
                }
                if (strlen($string) < strlen($origstring)) {
                    $string .= " " . $more;
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
                    $string .= " " . $more;
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
                    $string .= " " . $more;
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
                    for ($j = 0, $jMax = strlen($string); $j < $jMax; $j++) {

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
                    $string .= " " . $more;
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
//        global $db;

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
//	        $str = self::escape(trim(str_replace("�", "&trade;", $str)));
//        } elseif(DB_ENGINE=='mysql') {
//            $str = self::escape(trim(str_replace("�", "&trade;", $str)));
//        } else {
//	        $str = trim(str_replace("�", "&trade;", $str));
//        }
        $str = self::escape(trim(str_replace("�", "&trade;", $str)));
        //echo "2<br>"; eDebug($str,die);
        return $str;
    }

    public static function outputField($val, $eof = ',', $isHTML = false) {
        $newVal = self::parseAndTrimExport($val, $isHTML);
        if ($newVal != '') return '"' . $newVal . '"' . $eof;
        else return $eof;
    }

    public static function stripLineEndings($val) {
        return preg_replace('/\r\n/', ' ', trim($val));
    }

    /**
     * Scrub input string for possible security issues.
     *
     * @static
     * @param $data string
     * @return string
     */
    public static function sanitize(&$data) {
//        return $data;

        if (is_array($data)) {
            $saved_params = array();
            if (!empty($data['controller']) && $data['controller'] == 'snippet') {
                $saved_params['body'] = $data['body'];  // store snippet body
            }
            foreach ($data as $var=>$val) {
//                $data[$var] = self::sanitize($val);
                $data[$var] = self::xss_clean($val);
            }
            if (!empty($saved_params)) {
                $data = array_merge($data, $saved_params);  // add stored snippet body
            }
        } else {
            if (empty($data)) {
                return $data;
            }

            $data = self::xss_clean($data);

            //fixme orig exp method
//            if(0) {
//                // remove whitespaces and tags
////            $data = strip_tags(trim($data));
//                // remove whitespaces and script tags
//                $data = self::strip_tags_content(trim($data), '<script>', true);
////            $data = self::strip_tags_content(trim($data), '<iframe>', true);
//
//                // apply stripslashes if magic_quotes_gpc is enabled
//                if (get_magic_quotes_gpc()) {
//                    $data = stripslashes($data);
//                }
//
//                $data = self::escape($data);
//
//                // re-escape newlines
//                $data = str_replace(array('\r', '\n'), array("\r", "\n"), $data);
//            }
        }
        return $data;
    }

    // xss_clean //

    /**
  	 * Character set
  	 *
  	 * Will be overridden by the constructor.
  	 *
  	 * @var	string
  	 */
  	public static $charset = 'UTF-8';

    /**
   	 * XSS Hash
   	 *
   	 * Random Hash for protecting URLs.
   	 *
   	 * @var	string
   	 */
   	protected static $_xss_hash;

    /**
   	 * List of never allowed strings
   	 *
   	 * @var	array
   	 */
    protected static $_never_allowed_str =	array(
   		'document.cookie'	=> '[removed]',
   		'document.write'	=> '[removed]',
   		'.parentNode'		=> '[removed]',
   		'.innerHTML'		=> '[removed]',
   		'-moz-binding'		=> '[removed]',
   		'<!--'				=> '&lt;!--',
   		'-->'				=> '--&gt;',
   		'<![CDATA['			=> '&lt;![CDATA[',
   		'<comment>'			=> '&lt;comment&gt;'
   	);

   	/**
   	 * List of never allowed regex replacements
   	 *
   	 * @var	array
   	 */
    protected static $_never_allowed_regex = array(
   		'javascript\s*:',
   		'(document|(document\.)?window)\.(location|on\w*)',
   		'expression\s*(\(|&\#40;)', // CSS and IE
   		'vbscript\s*:', // IE, surprise!
   		'wscript\s*:', // IE
   		'jscript\s*:', // IE
   		'vbs\s*:', // IE
   		'Redirect\s+30\d',
   		"([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
   	);

    /**
   	 * XSS Clean
   	 *
   	 * Sanitizes data so that Cross Site Scripting Hacks can be
   	 * prevented.  This method does a fair amount of work but
   	 * it is extremely thorough, designed to prevent even the
   	 * most obscure XSS attempts.  Nothing is ever 100% foolproof,
   	 * of course, but I haven't been able to get anything passed
   	 * the filter.
   	 *
   	 * Note: Should only be used to deal with data upon submission.
   	 *	 It's not something that should be used for general
   	 *	 runtime processing.
   	 *
   	 * @link	http://channel.bitflux.ch/wiki/XSS_Prevention
   	 * 		Based in part on some code and ideas from Bitflux.
   	 *
   	 * @link	http://ha.ckers.org/xss.html
   	 * 		To help develop this script I used this great list of
   	 *		vulnerabilities along with a few other hacks I've
   	 *		harvested from examining vulnerabilities in other programs.
   	 *
   	 * @param	string|string[]	$str		Input data
   	 * @param 	bool		$is_image	Whether the input is an image
   	 * @return	string
   	 */
   	public static function xss_clean($str, $is_image = FALSE)
   	{
   		// Is the string an array?
   		if (is_array($str))
   		{
   			while (list($key) = each($str))
   			{
                if (preg_match('/^[a-zA-Z0-9_\x7f-\xff]*$/', $key)) {  // check for valid array name
                    $str[$key] = self::xss_clean($str[$key]);
                } else {
                    return null;
                }
   			}

   			return $str;
   		}

   		// Remove Invisible Characters
   		$str = self::remove_invisible_characters($str);

   		/*
   		 * URL Decode
   		 *
   		 * Just in case stuff like this is submitted:
   		 *
   		 * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
   		 *
   		 * Note: Use rawurldecode() so it does not remove plus signs
   		 */
   		do
   		{
   			$str = rawurldecode($str);
   		}
   		while (preg_match('/%[0-9a-f]{2,}/i', $str));

   		/*
   		 * Convert character entities to ASCII
   		 *
   		 * This permits our tests below to work reliably.
   		 * We only convert entities that are within tags since
   		 * these are the ones that will pose security problems.
   		 */
   		$str = preg_replace_callback("/[^a-z0-9>]+[a-z0-9]+=([\'\"]).*?\\1/si", array('self', '_convert_attribute'), $str);
   		$str = preg_replace_callback('/<\w+.*/si', array('self', '_decode_entity'), $str);

   		// Remove Invisible Characters Again!
   		$str = self::remove_invisible_characters($str);

   		/*
   		 * Convert all tabs to spaces
   		 *
   		 * This prevents strings like this: ja	vascript
   		 * NOTE: we deal with spaces between characters later.
   		 * NOTE: preg_replace was found to be amazingly slow here on
   		 * large blocks of data, so we use str_replace.
   		 */
   		$str = str_replace("\t", ' ', $str);

   		// Capture converted string for later comparison
   		$converted_string = $str;

   		// Remove Strings that are never allowed
   		$str = self::_do_never_allowed($str);

   		/*
   		 * Makes PHP tags safe
   		 *
   		 * Note: XML tags are inadvertently replaced too:
   		 *
   		 * <?xml
   		 *
   		 * But it doesn't seem to pose a problem.
   		 */
   		if ($is_image === TRUE)
   		{
   			// Images have a tendency to have the PHP short opening and
   			// closing tags every so often so we skip those and only
   			// do the long opening tags.
   			$str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
   		}
   		else
   		{
   			$str = str_replace(array('<?', '?'.'>'), array('&lt;?', '?&gt;'), $str);
   		}

   		/*
   		 * Compact any exploded words
   		 *
   		 * This corrects words like:  j a v a s c r i p t
   		 * These words are compacted back to their correct state.
   		 */
   		$words = array(
   			'javascript', 'expression', 'vbscript', 'jscript', 'wscript',
   			'vbs', 'script', 'base64', 'applet', 'alert', 'document',
   			'write', 'cookie', 'window', 'confirm', 'prompt', 'eval'
   		);

   		foreach ($words as $word)
   		{
   			$word = implode('\s*', str_split($word)).'\s*';

   			// We only want to do this when it is followed by a non-word character
   			// That way valid stuff like "dealer to" does not become "dealerto"
   			$str = preg_replace_callback('#('.substr($word, 0, -3).')(\W)#is', array('self', '_compact_exploded_words'), $str);
   		}

   		/*
   		 * Remove disallowed Javascript in links or img tags
   		 * We used to do some version comparisons and use of stripos(),
   		 * but it is dog slow compared to these simplified non-capturing
   		 * preg_match(), especially if the pattern exists in the string
   		 *
   		 * Note: It was reported that not only space characters, but all in
   		 * the following pattern can be parsed as separators between a tag name
   		 * and its attributes: [\d\s"\'`;,\/\=\(\x00\x0B\x09\x0C]
   		 * ... however, remove_invisible_characters() above already strips the
   		 * hex-encoded ones, so we'll skip them below.
   		 */
   		do
   		{
   			$original = $str;

   			if (preg_match('/<a/i', $str))
   			{
   				$str = preg_replace_callback('#<a[^a-z0-9>]+([^>]*?)(?:>|$)#si', array('self', '_js_link_removal'), $str);
   			}

   			if (preg_match('/<img/i', $str))
   			{
   				$str = preg_replace_callback('#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si', array('self', '_js_img_removal'), $str);
   			}

   			if (preg_match('/script|xss/i', $str))
   			{
   				$str = preg_replace('#</*(?:script|xss).*?>#si', '[removed]', $str);
   			}
   		}
   		while ($original !== $str);
   		unset($original);

   		/*
   		 * Sanitize naughty HTML elements
   		 *
   		 * If a tag containing any of the words in the list
   		 * below is found, the tag gets converted to entities.
   		 *
   		 * So this: <blink>
   		 * Becomes: &lt;blink&gt;
   		 */
   		$pattern = '#'
   			.'<((?<slash>/*\s*)(?<tagName>[a-z0-9]+)(?=[^a-z0-9]|$)' // tag start and name, followed by a non-tag character
   			.'[^\s\042\047a-z0-9>/=]*' // a valid attribute character immediately after the tag would count as a separator
   			// optional attributes
   			.'(?<attributes>(?:[\s\042\047/=]*' // non-attribute characters, excluding > (tag close) for obvious reasons
   			.'[^\s\042\047>/=]+' // attribute characters
   			// optional attribute-value
   				.'(?:\s*=' // attribute-value separator
   					.'(?:[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*))' // single, double or non-quoted value
   				.')?' // end optional attribute-value group
   			.')*)' // end optional attributes group
   			.'[^>]*)(?<closeTag>\>)?#isS';

   		// Note: It would be nice to optimize this for speed, BUT
   		//       only matching the naughty elements here results in
   		//       false positives and in turn - vulnerabilities!
   		do
   		{
   			$old_str = $str;
   			$str = preg_replace_callback($pattern, array('self', '_sanitize_naughty_html'), $str);
   		}
   		while ($old_str !== $str);
   		unset($old_str);

   		/*
   		 * Sanitize naughty scripting elements
   		 *
   		 * Similar to above, only instead of looking for
   		 * tags it looks for PHP and JavaScript commands
   		 * that are disallowed. Rather than removing the
   		 * code, it simply converts the parenthesis to entities
   		 * rendering the code un-executable.
   		 *
   		 * For example:	eval('some code')
   		 * Becomes:	eval&#40;'some code'&#41;
   		 */
   		$str = preg_replace(
   			'#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
   			'\\1\\2&#40;\\3&#41;',
   			$str
   		);

   		// Final clean up
   		// This adds a bit of extra precaution in case
   		// something got through the above filters
   		$str = self::_do_never_allowed($str);

   		/*
   		 * Images are Handled in a Special Way
   		 * - Essentially, we want to know that after all of the character
   		 * conversion is done whether any unwanted, likely XSS, code was found.
   		 * If not, we return TRUE, as the image is clean.
   		 * However, if the string post-conversion does not matched the
   		 * string post-removal of XSS, then it fails, as there was unwanted XSS
   		 * code found and removed/changed during processing.
   		 */
   		if ($is_image === TRUE)
   		{
   			return ($str === $converted_string);
   		}

   		return $str;
   	}

    /**
   	 * Do Never Allowed
   	 *
   	 * @used-by	CI_Security::xss_clean()
   	 * @param 	string
   	 * @return 	string
   	 */
   	protected static function _do_never_allowed($str)
   	{
   		$str = str_replace(array_keys(self::$_never_allowed_str), self::$_never_allowed_str, $str);

   		foreach (self::$_never_allowed_regex as $regex)
   		{
   			$str = preg_replace('#'.$regex.'#is', '[removed]', $str);
   		}

   		return $str;
   	}

	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public static function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}

    /**
   	 * HTML Entity Decode Callback
   	 *
   	 * @used-by	CI_Security::xss_clean()
   	 * @param	array	$match
   	 * @return	string
   	 */
   	protected static function _decode_entity($match)
   	{
   		// Protect GET variables in URLs
   		// 901119URL5918AMP18930PROTECT8198
   		$match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', self::xss_hash().'\\1=\\2', $match[0]);

   		// Decode, then un-protect URL GET vars
   		return str_replace(
            self::xss_hash(),
   			'&',
            self::entity_decode($match, self::$charset)
   		);
   	}

    /**
   	 * XSS Hash
   	 *
   	 * Generates the XSS hash if needed and returns it.
   	 *
   	 * @see		CI_Security::$_xss_hash
   	 * @return	string	XSS hash
   	 */
   	public static function xss_hash()
   	{
   		if (self::$_xss_hash === NULL)
   		{
   			$rand = self::get_random_bytes(16);
            self::$_xss_hash = ($rand === FALSE)
   				? md5(uniqid(mt_rand(), TRUE))
   				: bin2hex($rand);
   		}

   		return self::$_xss_hash;
   	}

    /**
   	 * HTML Entities Decode
   	 *
   	 * A replacement for html_entity_decode()
   	 *
   	 * The reason we are not using html_entity_decode() by itself is because
   	 * while it is not technically correct to leave out the semicolon
   	 * at the end of an entity most browsers will still interpret the entity
   	 * correctly. html_entity_decode() does not convert entities without
   	 * semicolons, so we are left with our own little solution here. Bummer.
   	 *
   	 * @link	http://php.net/html-entity-decode
   	 *
   	 * @param	string	$str		Input
   	 * @param	string	$charset	Character set
   	 * @return	string
   	 */
   	public static function entity_decode($str, $charset = NULL)
   	{
   		if (strpos($str, '&') === FALSE)
   		{
   			return $str;
   		}

   		static $_entities;

   		isset($charset) OR $charset = self::$charset;
   		$flag = expCore::is_php('5.4')
   			? ENT_COMPAT | ENT_HTML5
   			: ENT_COMPAT;

   		do
   		{
   			$str_compare = $str;

   			// Decode standard entities, avoiding false positives
   			if (preg_match_all('/&[a-z]{2,}(?![a-z;])/i', $str, $matches))
   			{
   				if ( ! isset($_entities))
   				{
   					$_entities = array_map(
   						'strtolower',
                        expCore::is_php('5.3.4')
   							? get_html_translation_table(HTML_ENTITIES, $flag, $charset)
   							: get_html_translation_table(HTML_ENTITIES, $flag)
   					);

   					// If we're not on PHP 5.4+, add the possibly dangerous HTML 5
   					// entities to the array manually
   					if ($flag === ENT_COMPAT)
   					{
   						$_entities[':'] = '&colon;';
   						$_entities['('] = '&lpar;';
   						$_entities[')'] = '&rpar;';
   						$_entities["\n"] = '&newline;';
   						$_entities["\t"] = '&tab;';
   					}
   				}

   				$replace = array();
   				$matches = array_unique(array_map('strtolower', $matches[0]));
   				foreach ($matches as &$match)
   				{
   					if (($char = array_search($match.';', $_entities, TRUE)) !== FALSE)
   					{
   						$replace[$match] = $char;
   					}
   				}

   				$str = str_ireplace(array_keys($replace), array_values($replace), $str);
   			}

   			// Decode numeric & UTF16 two byte entities
   			$str = html_entity_decode(
   				preg_replace('/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\d{2,4}(?![0-9;]))))/iS', '$1;', $str),
   				$flag,
   				$charset
   			);
   		}
   		while ($str_compare !== $str);
   		return $str;
   	}

    /**
   	 * Get random bytes
   	 *
   	 * @param	int	$length	Output length
   	 * @return	string
   	 */
   	public static function get_random_bytes($length)
   	{
   		if (empty($length) OR ! ctype_digit((string) $length))
   		{
   			return FALSE;
   		}

        if (function_exists('random_bytes'))
        {
            try
            {
                // The cast is required to avoid TypeError
                return random_bytes((int) $length);
            }
            catch (Exception $e)
            {
                // If random_bytes() can't do the job, we can't either ...
                // There's no point in using fallbacks.
                log_message('error', $e->getMessage());
                return FALSE;
            }
        }

   		// Unfortunately, none of the following PRNGs is guaranteed to exist ...
   		if (defined('MCRYPT_DEV_URANDOM') && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== FALSE)
   		{
   			return $output;
   		}


   		if (is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== FALSE)
   		{
   			// Try not to waste entropy ...
            expCore::is_php('5.4') && stream_set_chunk_size($fp, $length);
   			$output = fread($fp, $length);
   			fclose($fp);
   			if ($output !== FALSE)
   			{
   				return $output;
   			}
   		}

   		if (function_exists('openssl_random_pseudo_bytes'))
   		{
   			return openssl_random_pseudo_bytes($length);
   		}

   		return FALSE;
   	}

    /**
   	 * Attribute Conversion
   	 *
   	 * @used-by	CI_Security::xss_clean()
   	 * @param	array	$match
   	 * @return	string
   	 */
   	protected static function _convert_attribute($match)
   	{
   		return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
   	}

    /**
   	 * Compact Exploded Words
   	 *
   	 * Callback method for xss_clean() to remove whitespace from
   	 * things like 'j a v a s c r i p t'.
   	 *
   	 * @used-by	CI_Security::xss_clean()
   	 * @param	array	$matches
   	 * @return	string
   	 */
   	protected static function _compact_exploded_words($matches)
   	{
   		return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
   	}

    /**
   	 * JS Link Removal
   	 *
   	 * Callback method for xss_clean() to sanitize links.
   	 *
   	 * This limits the PCRE backtracks, making it more performance friendly
   	 * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
   	 * PHP 5.2+ on link-heavy strings.
   	 *
   	 * @used-by	CI_Security::xss_clean()
   	 * @param	array	$match
   	 * @return	string
   	 */
   	protected static function _js_link_removal($match)
   	{
   		return str_replace(
   			$match[1],
   			preg_replace(
   				'#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
   				'',
   				self::_filter_attributes($match[1])
   			),
   			$match[0]
   		);
   	}

    /**
   	 * JS Image Removal
   	 *
   	 * Callback method for xss_clean() to sanitize image tags.
   	 *
   	 * This limits the PCRE backtracks, making it more performance friendly
   	 * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
   	 * PHP 5.2+ on image tag heavy strings.
   	 *
   	 * @used-by	CI_Security::xss_clean()
   	 * @param	array	$match
   	 * @return	string
   	 */
   	protected static function _js_img_removal($match)
   	{
   		return str_replace(
   			$match[1],
   			preg_replace(
   				'#src=.*?(?:(?:alert|prompt|confirm|eval)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
   				'',
   				self::_filter_attributes($match[1])
   			),
   			$match[0]
   		);
   	}

    /**
   	 * Filter Attributes
   	 *
   	 * Filters tag attributes for consistency and safety.
   	 *
   	 * @used-by	CI_Security::_js_img_removal()
   	 * @used-by	CI_Security::_js_link_removal()
   	 * @param	string	$str
   	 * @return	string
   	 */
   	protected static function _filter_attributes($str)
   	{
   		$out = '';
   		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
   		{
   			foreach ($matches[0] as $match)
   			{
   				$out .= preg_replace('#/\*.*?\*/#s', '', $match);
   			}
   		}

   		return $out;
   	}

    /**
   	 * Sanitize Naughty HTML
   	 *
   	 * Callback method for xss_clean() to remove naughty HTML elements.
   	 *
   	 * @used-by	CI_Security::xss_clean()
   	 * @param	array	$matches
   	 * @return	string
   	 */
   	protected static function _sanitize_naughty_html($matches)
   	{
   		static $naughty_tags    = array(
   			'alert', 'prompt', 'confirm', 'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound',
   			'blink', 'body', 'embed', 'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer',
   			'input', 'button', 'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object',
   			'plaintext', 'script', 'textarea', 'title', 'math', 'video', 'svg', 'xml', 'xss'
            //,'style', 'iframe'
   		);

   		static $evil_attributes = array(
   			'on\w+', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime'
            //, 'style'
   		);

   		// First, escape unclosed tags
   		if (empty($matches['closeTag']))
   		{
   			return '&lt;'.$matches[1];
   		}
   		// Is the element that we caught naughty? If so, escape it
   		elseif (in_array(strtolower($matches['tagName']), $naughty_tags, TRUE))
   		{
   			return '&lt;'.$matches[1].'&gt;';
   		}
   		// For other tags, see if their attributes are "evil" and strip those
   		elseif (isset($matches['attributes']))
   		{
   			// We'll store the already fitlered attributes here
   			$attributes = array();

   			// Attribute-catching pattern
   			$attributes_pattern = '#'
   				.'(?<name>[^\s\042\047>/=]+)' // attribute characters
   				// optional attribute-value
   				.'(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))' // attribute-value separator
   				.'#i';

   			// Blacklist pattern for evil attribute names
   			$is_evil_pattern = '#^('.implode('|', $evil_attributes).')$#i';

   			// Each iteration filters a single attribute
   			do
   			{
   				// Strip any non-alpha characters that may preceed an attribute.
   				// Browsers often parse these incorrectly and that has been a
   				// of numerous XSS issues we've had.
   				$matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);

   				if ( ! preg_match($attributes_pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE))
   				{
   					// No (valid) attribute found? Discard everything else inside the tag
   					break;
   				}

   				if (
   					// Is it indeed an "evil" attribute?
   					preg_match($is_evil_pattern, $attribute['name'][0])
   					// Or does it have an equals sign, but no value and not quoted? Strip that too!
   					OR (trim($attribute['value'][0]) === '')
   				)
   				{
   					$attributes[] = 'xss=removed';
   				}
   				else
   				{
   					$attributes[] = $attribute[0][0];
   				}

   				$matches['attributes'] = substr($matches['attributes'], $attribute[0][1] + strlen($attribute[0][0]));
   			}
   			while ($matches['attributes'] !== '');

   			$attributes = empty($attributes)
   				? ''
   				: ' '.implode(' ', $attributes);
   			return '<'.$matches['slash'].$matches['tagName'].$attributes.'>';
   		}

   		return $matches[0];
   	}

}

?>