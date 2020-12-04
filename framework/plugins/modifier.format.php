<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * Smarty {format} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     format<br>
 * Purpose:  format a string to a date (system) or currency
 *
 * @param        array
 * @param string $format
 *
 * @return string
 */
function smarty_modifier_format($string, $format=null) {
    switch ($format) {
        case 'date':
            if (!empty($string))
                return strftime(DISPLAY_DATETIME_FORMAT, $string);
            else
                return ''; // 0 isn't really a date
            break;
        case 'currency' :
            $string = ($string=="") ? 0 : $string;
            if (is_numeric($string)) {
                return expCore::getCurrencySymbol() . number_format($string, 2, ".", ",");
            } else {
                return $string;
            }

            break;
        default:
            return $string;
    }
}

?>