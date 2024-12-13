<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * Smarty {loading} function plugin
 *
 * Type:     function<br>
 * Name:     loading<br>
 * Purpose:  create framework appropriate loading element for animation
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_loading($params,&$smarty) {
    if (!isset($params['title'])) {
        $title = gt('Loading');
   	} else {
   		$title = $params['title'];
   	}

    if (bs2()) {
        $spinner = '<i class="icon icon-large icon-spinner icon-spin"></i> ';
        expCSS::pushToHead(array(
           "corecss"=>"loading",
        ));
    } elseif (bs3()) {
        $spinner = '<i class="fa fa-lg fa-spinner fa-pulse"></i> ';
        expCSS::pushToHead(array(
           "corecss"=>"loading",
        ));
    } elseif (bs5() && USE_BOOTSTRAP_ICONS) {
        $spinner = '<i class="bi bi-lg bi-arrow-clockwise bi-pulse"></i> ';
        expCSS::pushToHead(array(
           "corecss"=>"loading",
        ));
    } elseif (bs4() || bs5()) {
        $spinner = '<i class="fas fa-lg fa-spinner fa-pulse"></i> ';
        expCSS::pushToHead(array(
           "corecss"=>"loading",
        ));
    } else {
        $spinner ='';
    }

    if (!empty($params['span'])) {
        echo '<span class="loadingdiv">', $spinner, $title, '</span>';
        expCSS::pushToHead(array(
           "css"=>".loadingdiv {padding:0;padding-left:6px;}",
        ));
    } else {
        echo '<div class="loadingdiv">', $spinner, $title, '</div>';
    }

    if (bs() && !bs2()) {
        expJavascript::pushToFoot(
            array(
                'unique' => 'loadingremove',
                'jquery' => 1,
                'content'=> "
            $('.loadingdiv').remove();
                ",
                'bootstrap' => 'tab,transition'
            )
        );
    }
}

?>