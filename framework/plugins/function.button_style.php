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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {button_style} function plugin
 *
 * Type:     function<br>
 * Name:     button_style<br>
 * Purpose:  return correct button style for current framework
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_button_style($params,&$smarty) {
    $btn_color = !empty($params['color']) ? $params['color'] : BTN_COLOR;
    $btn_size = !empty($params['size']) ? $params['size'] : BTN_SIZE;
    if (expSession::get('framework') == 'bootstrap' || expSession::get('framework') == 'bootstrap3') {
        $btn_class = 'btn ' . ' ' . expTheme::buttonColor($btn_color) . ' ' . expTheme::buttonSize($btn_size);
    } else {
        $btn_class = "awesome " . $btn_color . " " . $btn_size;
    }
    echo $btn_class;
}

?>
