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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Modifier
 */

/**
 * Smarty {highlight} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     highlight<br>
 * Purpose:  highlight selected phrases in text
 *
 * @param string $text
 * @param string $word phrase(s) to highlight
 * @param string $highlight type of highlight (style/class) to place inside <span> tag
 *
 * @return array
 */
function smarty_modifier_highlight($text='', $word='', $highlight='html5') {
   if(strlen($text) > 0 && strlen($word) > 0) {
       $highlight = empty($highlight) ? 'style="background-color:#ffff55;"' : $highlight;
       $words = explode(' ',$word);
       $words = array_unique($words);  // no need to highlight duplicated words more than once
       foreach ($words as $phrase) {   // highlight each word
           $phrase = str_replace(array('+','-','*'), '', $phrase);
           if ($highlight == 'html5' && $phrase != 'mark') {
               $text = preg_replace('/('.preg_quote($phrase).')/i', '<mark>${1}</mark>', $text);
           } else {
               $text = preg_replace('/('.preg_quote($phrase).')/i', '<span '.$highlight.'>${1}</span>', $text);
           }
       }
   }
   return($text);
}

?>
