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
 * Smarty {smart_heading} function plugin
 *
 * Type:     function<br>
 * Name:     smart_heading<br>
 * Purpose:  Apply heading level smartly, e.g. only one H1 per page and sidebar content is lower heading level
 *
 * @param $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_smart_heading($params, &$smarty) {
    global $page_heading_top;

    if (empty($params['title'])) {
        die("<strong style='color:red'>" . gt("The 'title' parameter is required for the {smart_heading} plugin.") . "</strong>");
    }
    if (empty($params['level'])) {
        $params['level'] = 1;
    }

    $heading_level = get_level($params['level']);
    if ($heading_level === 'h1') {
        $page_heading_top = true;
    }

    echo '<' . HEADER_LEVEL[$heading_level] . '>' . $params['title'] .  '</' . HEADER_LEVEL[$heading_level] . '>';
}

function get_level($requested_level) {
    global $page_main_section, $page_heading_top;

    if (is_int($requested_level)) {
        $counter = $requested_level;
    } else {
        $counter = 0;
        foreach (HEADER_LEVEL as $idx=>$hdg) {
            $counter++;
            if ($hdg === $requested_level)
                break;
        }
    }
    if ($page_heading_top) { // we've already used the h1 tag
        $counter--;
    }
    $keys = array_keys(HEADER_LEVEL);
    if ($page_main_section) { // sidebar smaller than main content
        $headinglevel = HEADER_LEVEL[$keys[$counter - 1]];
    } else {
        $headinglevel = HEADER_LEVEL[$keys[$counter]];
    }

    return $headinglevel;
}

?>

