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
 * Smarty {highlight} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     highlight<br>
 * Purpose:  highlight selected segments of text
 *
 * @param string $text
 * @param string $word
 *
 * @return array
 */
function smarty_modifier_highlight($text='', $word='') {
   if(strlen($text) > 0 && strlen($word) > 0)
   {
//      return preg_replace('/\b('.preg_quote($word).')\b/', '<span class="highlight">${1}</span>', $text);
       return preg_replace('/\b('.preg_quote($word).')\b/', '<span style="background-color: #ffff55;" class="highlight">${1}</span>', $text);
   }
   return($text);
}

?>
