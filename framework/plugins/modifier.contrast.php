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
 * Smarty {contrast} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     contrast<br>
 * Purpose:  Calculate a contrasting/complementary color from the one passed
 *           using either the default 50% rule or YIQ
 *
 * @param string $hexcolor
 * @param string $dark
 * @param string $light
 * @param bool   $yiq
 *
 * @return array
 */

function smarty_modifier_contrast($hexcolor, $dark = '#000000', $light = '#FFFFFF', $yiq = false) {
    if (!$yiq) {
        return (hexdec($hexcolor) > 0xffffff / 2) ? $dark : $light;
    } else {
        $r = hexdec(substr($hexcolor,0,2));
       	$g = hexdec(substr($hexcolor,2,2));
       	$b = hexdec(substr($hexcolor,4,2));
       	$yiq = (($r*299)+($g*587)+($b*114))/1000;
       	return ($yiq >= 128) ? $dark : $light;
    }
}

?>
