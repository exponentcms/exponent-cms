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
 * Smarty {pagelinks} function plugin
 *
 * Type:     function<br>
 * Name:     pagelinks<br>
 * Purpose:  display page links
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_pagelinks($params, &$smarty) {
    $config = $smarty->getTemplateVars('config');
    if (!empty($params['more']) && (!empty($config['pagelinks']) && $config['pagelinks'] != "Disable page links")) {
//        if ($params['paginate']->total_pages < 2 && $config['multipageonly'] == 1) {
//        } else {
            $text  = !empty($params['text']) ? $params['text'] : 'More ...';
            $title = !empty($params['title']) ? $params['title'] : $text;
        if (bs()) {
            if (bs4() ||bs5()) {
            echo '<a href="' . $params['paginate']->morelink . '" title="' . $title . '" class="'.expTheme::buttonStyle().'">'. expTheme::iconStyle('view', $text) . '</a>';
            } else {
                echo '<a href="' . $params['paginate']->morelink . '" title="' . $title . '" class="showall '.expTheme::buttonStyle().'">' . $text . '</a>';
            }
        } else {
            echo '<a href="' . $params['paginate']->morelink . '" title="' . $title . '" class="showall">' . $text . '</a>';
        }
//        }
    } elseif (empty($config['pagelinks']) || (!empty($config['pagelinks']) && $config['pagelinks'] == "Top and Bottom")) {
        if ($params['paginate']->total_pages < 2 && $config['multipageonly'] == 1) {
        } else {
            echo $params['paginate']->links;
        }
    } else if (!empty($params['top']) && (!empty($config['pagelinks']) && $config['pagelinks'] == "Top Only")) {
        if ($params['paginate']->total_pages < 2 && $config['multipageonly'] == 1) {
        } else {
            echo $params['paginate']->links;
        }
    } else if (!empty($params['bottom']) && (!empty($config['pagelinks']) && $config['pagelinks'] == "Bottom Only")) {
        if ($params['paginate']->total_pages < 2 && $config['multipageonly'] == 1) {
        } else {
            echo $params['paginate']->links;
        }
    }
}

?>
