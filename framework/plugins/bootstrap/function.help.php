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
 * Smarty {help} function plugin
 *
 * Type:     function<br>
 * Name:     help<br>
 * Purpose:  create a help link
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_help($params,&$smarty) {

    if (HELP_ACTIVE) {
        // figure out the params
        $doc = !empty($params['doc']) ? $params['doc'] : (!empty($params['module']) ? $params['module'] : $smarty->getTemplateVars('__loc')->mod);
        $text = empty($params['text']) ? '&#160;' : $params['text'];
        $title = empty($params['title']) ? $text : (empty($params['text']) ? gt('Get Help with').' '.ucwords($doc) : $params['title']);
        $class  = 'helplink';
        $class .= isset($params['class']) ? $params['class'] : 'btn '.expTheme::buttonStyle('purple');

        if (!empty($params['page'])) {
            $link = HELP_URL.$params['page'];
        } else {
            $link = help::makeHelpLink($doc);
        }

        echo '<a class="' . $class. '" title="'.$title.'" href="'.$link.'" target="_blank"><i class="icon-question-sign '.expTheme::iconSize().'"></i> '.$text.'</a>';
        expCSS::pushToHead(array(
		    "csscore"=>"admin-global",
        ));
    }
}

?>

