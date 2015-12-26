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
 * @subpackage Function
 */

/**
 * Smarty {breadcrum} function plugin
 *
 * Type:     function<br>
 * Name:     breadcrum<br>
 * Purpose:  return a 'breadcrumb' widget
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_breadcrumb($params,&$smarty) {
    $active = isset($params['active']) ? $params['active'] : 1;
    $items = !empty($params['items']) ? $params['items'] : array();

    $html = '<div class="cart-breadcrumb ' . (!empty($params['style'])?$params['style']:'') . '">';
    $i = 0;
    foreach ($items as $key=>$item) {
        if (is_array($item)) {
            if (!empty($item['link'])) {
                $html .= '<a href="' . $item['link'] . '"';
            } else {
                $html .= '<a ';
            }
        } else {
            $html .= '<a ';
        }
        if ($i == $active) $html .= ' class="active"';
        if (is_array($item)) {
            $title = $item['title'];
        } else {
            $title = $item;
        }
        $html .= 'title="' . $title . ' ' . gt('Step') . '">';
        if ($i != $active) {
            $title = '&#160;<span class="breadcrumb-title">' .$title . '</span>';
            if (bs3()) {
                $title = '&#160;<span class="hidden-xs hidden-sm">' . $title . '</span>';
            }
        }
        $html .= $title . '</a>';
        $i++;
    }
    $html .= '</div>';

    expCSS::pushToHead(array(
//	    "unique"=>"admin-container",
	    "link"=>PATH_RELATIVE."framework/modules/ecommerce/assets/css/cart-breadcrumb.css",
	    )
	);

    echo $html;
}

?>
