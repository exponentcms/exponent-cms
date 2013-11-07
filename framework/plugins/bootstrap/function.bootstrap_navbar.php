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
 * Smarty {bootstrap_navbar} function plugin
 *
 * Type:     function<br>
 * Name:     bootstrap_navbar<br>
 * Purpose:  display a twitter bootstrap menu navbar
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return string
 */
function smarty_function_bootstrap_navbar($params,&$smarty) {
    $menu = '';

    if (empty($params['menu'])) {
        return $menu;
    } else {
        foreach ($params['menu'] as $page) {
            $menu .= build_menu($page,$params);
        }
        expJavascript::pushToFoot(array(
            "unique"  => 'bootstrap-dropdown',
            "jquery"=> '1',
            "src"=> PATH_RELATIVE . 'external/bootstrap/js/bootstrap-dropdown.js',
        ));
        expJavascript::pushToFoot(array(
            "unique"  => 'bootstrap-collapse',
            "jquery"=> '1',
            "src"=> PATH_RELATIVE . 'external/bootstrap/js/bootstrap-collapse.js',
        ));
        return $menu;
    }
}

function build_menu($page,$params) {
    global $sectionObj;

//    $menu = '';
    if (empty($page->itemdata) && empty($page->submenu) && $page->type != 3) {  // this is a menu item
        $menu = '<li tabindex="-1"';
        if ($sectionObj->id == $page->id) $menu .= ' class="active"';
        if ($page->url == "#") $menu .= ' class="disabled"';
        $menu .= '><a href="'.$page->url.'"'.($page->new_window?' target="_blank"':'').'>'.$page->text.'</a></li>'."\n";
    } elseif ($page->type != 3) {                                                // this is a submenu item
        if ($page->depth) {
            $menu = '<li class="dropdown-submenu';
        } else {
            $menu = '<li class="dropdown';
        }
        if ($sectionObj->id == $page->id) $menu .= ' active';
        $menu .= '"><a href="'.$page->url.'" class="dropdown-toggle" data-toggle="dropdown"'.($page->new_window?' target="_blank"':'').'>'.$page->text;
        if (!$page->depth) $menu .= '<b class="caret"></b>';
        $menu .= '</a>'."\n".'<ul class="dropdown-menu pull-'.$params['menualign'].'">'."\n";
        if ($page->url != "#") {  // we also need a 'menu item' for active parent pages
            $topmenu = new stdClass();
            $topmenu->id = $page->id;
            $topmenu->text = $page->text;
            $topmenu->url = $page->url;
            $menu .= build_menu($topmenu,$params);
        }
        if (!empty($page->itemdata)) {
            foreach ($page->itemdata as $subpage) {
                $menu .= build_menu($subpage,$params);
            }
        } elseif (!empty($page->submenu->itemdata)) {
            foreach ($page->submenu->itemdata as $subpage) {
                $menu .= build_menu($subpage,$params);
            }
        }
        $menu .= '</ul>'."\n".'</li>'."\n";
    }

    return $menu;
}
	
?>
