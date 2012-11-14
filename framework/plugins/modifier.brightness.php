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
 * Smarty plugin
 *
 * @package    Smarty-Plugins
 * @subpackage Modifier
 */

/**
 * Smarty {brightness} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     brightness<br>
 * Purpose:  Calculate a lighter/darker color from the one passed
 *           +- 255 steps, default is 20 steps brighter
 *
 * @param string     $colourstr
 * @param int        $steps
 *
 * @return string
 */

function smarty_modifier_brightness($colourstr, $steps = 20) {
    $colourstr = str_replace('#', '', $colourstr);
    $rhex = substr($colourstr, 0, 2);
    $ghex = substr($colourstr, 2, 2);
    $bhex = substr($colourstr, 4, 2);

    $r = hexdec($rhex);
    $g = hexdec($ghex);
    $b = hexdec($bhex);

    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));

    return '#' . dechex($r) . dechex($g) . dechex($b);
}

?>
