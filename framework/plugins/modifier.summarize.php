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
 *
 * @package    Smarty-Plugins
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

?>
