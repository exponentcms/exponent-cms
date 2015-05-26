<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
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
 * Purpose:  process exp menu hierarchy to display a twitter bootstrap menu navbar
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return string
 */
function smarty_function_bootstrap_navbar($params,&$smarty) {
    //load the {img} plugin
    foreach ($smarty->smarty->plugins_dir as $value) {
        $filepath = $value ."/function.img.php";
        if (file_exists($filepath)) {
            require_once $filepath;
            break;
        }
    }

    $menu = '';

    if (empty($params['menu'])) {
        return $menu;
    } else {
        foreach ($params['menu'] as $page) {
            $menu .= build_menu($page,$params);
        }
        expJavascript::pushToFoot(array(
            "unique"  => 'bootstrap-dropdown',
            "bootstrap"=> 'dropdown',
        ));
        expJavascript::pushToFoot(array(
            "unique"  => 'bootstrap-collapse',
            "bootstrap"=> 'collapse',
        ));
        return $menu;
    }
}

function build_menu($page,$params) {
    global $sectionObj;

    if (!empty($page->expFile[0]->id)) {
        $img_parm = array("h"=>16,"w"=>16,"zc"=>1,"file_id"=>$page->expFile[0]->id,"return"=>1,"class"=>'img_left');
        $img = smarty_function_img($img_parm,$smarty);
    } else {
        $img = '';
    }
    if (empty($page->itemdata) && empty($page->submenu) && (empty($page->type) || (!empty($page->type) && $page->type != 3))) {  // this is a menu item
        $menu = '<li tabindex="-1"';
        if ($sectionObj->id == $page->id) $menu .= ' class="active"';
        if ($page->url == "#") $menu .= ' class="disabled"';
        $menu .= '><a href="'.$page->url.'"'.($page->new_window?' target="_blank"':'').'>'.$img.$page->text.'</a></li>'."\n";
    } elseif ((empty($page->type) || (!empty($page->type) && $page->type != 3))) {                                                // this is a submenu item
        if (!empty($page->depth)) {
            $menu = '<li class="dropdown-submenu';
        } else {
            $menu = '<li class="dropdown';
        }
        if ($sectionObj->id == $page->id) $menu .= ' active';
        $menu .= '"><a href="'.$page->url.'" class="dropdown-toggle" data-toggle="dropdown"'.($page->new_window?' target="_blank"':'').'>'.$img.$page->text;
        if (empty($page->depth)) $menu .= '<b class="caret"></b>';
        $menu .= '</a>'."\n".'<ul class="dropdown-menu'.($params['menualign']=='right'?' pull-right':'').'">'."\n";
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
    } elseif ($page->type == 3) {                                                                                                   // this is a freeform item
        if ($page->width == 2) {
            $view = 'showall_Two Column';
        } elseif ($page->width == 3) {
            $view = 'showall_Three Column';
        } elseif ($page->width == 4) {
            $view = 'showall_Four Column';
        } else {
            $view = 'showall';
        }
        $menu = '
        <li class="dropdown' . (empty($page->width) ? ' yamm-fw' : '') . ($page->class == "right" ? ' pull-right ' : '') . '">';
        $menu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'. $img . $page->text . '<b class="caret"></b></a>';
        $menu .= '<ul class="dropdown-menu"><li><div class="yamm-content">';
        if (bs3())
            $menu .= '<div class="row"><div class="col-sm-12">';
        elseif (bs2())
            $menu .= '<div class="row-fluid"><div class="span12">';
        $menu .= expTheme::module(array("module"=>"container","action"=>"showall","view"=>$view,"source"=>"menuitem-" . $page->id,"chrome"=>true,"params"=>array("no_output"=>true)));
        $menu .= '</div></div></div></li></ul></li>';
    } else {
        $menu = '';
    }

    return $menu;
}
	
?>
