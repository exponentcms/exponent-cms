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
    if (expSession::get('framework') == 'bootstrap3') {
        if (BTN_SIZE != 'large' || (!empty($params['size']) && $params['size'] != 'large')) {
            $btn_size = 'btn-mini';
            $icon_size = '';
        } else {
            $btn_size = 'btn-small';
            $icon_size = 'icon-large';
        }
        $btn_type = expCore::buttonColor($params['color']);
        $btn_class = 'btn ' . $btn_size . ' ' . $btn_type;
    } else {
        $btn_size = !empty($params['size']) ? $params['size'] : BTN_SIZE;
        $btn_color = !empty($params['color']) ? $params['color'] : BTN_COLOR;
        $btn_class = "awesome " . $btn_size . " " . $btn_color;
    }
    echo $btn_class;
}

?>
