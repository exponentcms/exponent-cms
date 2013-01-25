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
        foreach ($params['menu'] as $key=>$page) {
            $menu .= build_menu($page);
        }
        return $menu;
    }
}

function build_menu($page) {
    global $sectionObj;

    //FIXME we need to take into account the active flag
    $menu = '';
    if (empty($page->itemdata) && empty($page->submenu)) {  // this is a menu item
        $menu = '<li';
        if ($sectionObj->id == $page->id) $menu .= ' class="active"';
        $menu .= '><a href="'.$page->url.'"'.($page->new_window?' target="_blank"':'').'>'.$page->text.'</a></li>'."\n";
    } else {                                                // this is a submenu item
        $menu = '<li class="dropdown';
        if ($sectionObj->id == $page->id) $menu .= ' active';
        $menu .= '"><a href="'.$page->url.'" class="dropdown-toggle" data-toggle="dropdown"'.($page->new_window?' target="_blank"':'').'>'.$page->text.'<b class="caret"></b></a>'."\n";
        $menu .= '<ul class="dropdown-menu">'."\n";
        if ($page->url != "#") {
            $topmenu = new stdClass();
            $topmenu->id = $page->id;
            $topmenu->text = $page->text;
            $topmenu->url = $page->url;
            $menu .= build_menu($topmenu);
        }
        if (!empty($page->itemdata)) {
            foreach ($page->itemdata as $subpage) {
                $menu .= build_menu($subpage);
            }
        } elseif (!empty($page->submenu->itemdata)) {
            foreach ($page->submenu->itemdata as $subpage) {
                $menu .= build_menu($subpage);
            }
        }
        $menu .= '</ul>'."\n".'</li>'."\n";
    }

    return $menu;
}
	
?>
