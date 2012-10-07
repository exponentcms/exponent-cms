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
        if (empty($params['module'])) {
            $module = $smarty->getTemplateVars('__loc')->mod;
        } else {
            $module = $params['module'];
        }

        // figure out the params
        $text = empty($params['text']) ? '&#160;' : $params['text'];

        $title = empty($params['title']) ? gt('Get Help').' for '.$params['module'] : $params['title'];

        $class  = 'helplink';
        $class .= empty($params['class']) ? '' : $params['class'];

        $link = help::makeHelpLink($module);
        if (!empty($params['page'])) {
            echo '<a class="'.$class.'" '.$title.' href="'.HELP_URL.$params['page'].'" target="_blank">'.$text.'</a>';
        } else {
            echo '<a class="'.$class.'" '.$title.' href="'.$link.'" target="_blank">'.$text.'</a>';
        }
        
        expCSS::pushToHead(array(
		    "csscore"=>"admin-global",
        ));
    }
}

?>

