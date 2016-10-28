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
            "unique"  => 'bootstrap-transition',
            "bootstrap"=> 'transition',
        ));
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
        $img_parm = array("h"=>16,"w"=>16,"zc"=>1,"file_id"=>$page->expFile[0]->id,"return"=>1,"class"=>'img_left',"alt"=>$page->text);
        $img = smarty_function_img($img_parm,$smarty);
    } elseif (bs3() && !empty($page->glyph)) {
        $img = '<i class="fa fa-fw ' . $page->glyph . '" aria-hidden="true"></i> ';
    } else {
        $img = '';
    }
    if (!empty($img) && !empty($page->glyph_only)) {
        $menu_item = $img . '<span class="sr-only">' . $page->text . '</span>';
    } else {
        $menu_item = $img . $page->text;
    }
    if ((empty($page->itemdata) && empty($page->submenu) && (empty($page->type) || (!empty($page->type) && $page->type != 3))) || $page->depth + 1 == $params['length']) {  // this is a menu item
        $menu = '<li tabindex="-1"';
        if (isset($page->depth))
            $menu .= ' role="menuitem"';
        if ($sectionObj->id == $page->id) $menu .= ' class="active"';
        if ($page->url == "#") $menu .= ' class="disabled"';
        $menu .= '><a href="'.$page->url.'"'.($page->new_window?' target="_blank"':'').'>' . $menu_item . '</a></li>'."\n";
    } elseif ((empty($page->type) || (!empty($page->type) && $page->type != 3))) {                                                // this is a submenu item
        if (isset($page->depth) && $page->depth + 1 < $params['length']) {
            $menu = '<li class="dropdown-submenu';
        } else {
            $menu = '<li class="dropdown';
        }
        if ($sectionObj->id == $page->id) $menu .= ' active';
        $menu .= '"><a href="'.$page->url.'" id="dropdownMenu' . $page->id . '" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"'.($page->new_window?' target="_blank"':'').'>' . $menu_item;
        if (empty($page->depth) && $params['length'] > 1) $menu .= '<b class="caret"></b>';
        $menu .= '</a>'."\n".'<ul class="dropdown-menu'.($params['menualign']=='right'?' pull-right':'').'" role="menu" aria-labelledby="dropdownMenu' . $page->id . '">'."\n";
        if ($page->url != "#") {  // we also need a 'menu item' for active parent pages
            $topmenu = new stdClass();
            $topmenu->id = $page->id;
            $topmenu->text = $page->text;
            $topmenu->url = $page->url;
            if ((!isset($page->depth) && $params['length'] > 1) || $page->depth + 1 < $params['length']) {
                $menu .= build_menu($topmenu, $params);
            }
        }
        if ((!isset($page->depth) && $params['length'] > 1) || $page->depth + 1 < $params['length']) {
            if (!empty($page->itemdata)) {
                foreach ($page->itemdata as $subpage) {
                    $menu .= build_menu($subpage, $params);
                }
            } elseif (!empty($page->submenu->itemdata)) {
                foreach ($page->submenu->itemdata as $subpage) {
                    $menu .= build_menu($subpage, $params);
                }
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
        $menu .= '<a href="#" id="dropdownMenu' . $page->id . '" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'. $menu_item . '<b class="caret"></b></a>';
        $menu .= '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu' . $page->id . '"><li role="menuitem"><div class="yamm-content">';
        if (bs3())
            $menu .= '<div class="row"><div class="col-sm-12">';
        elseif (bs2())
            $menu .= '<div class="row-fluid"><div class="span12">';
        if (SELECTOR == 1) {
            $menu .= '<h5 style="color:red">' . gt("Free form menu items are not displayed in selector view") . '</h5>';
        } else {
            $menu .= expTheme::module(array("module"=>"container","action"=>"showall","view"=>$view,"source"=>"menuitem-" . $page->id,"params"=>array("no_output"=>true)));
        }
        $menu .= '</div></div></div></li></ul></li>';
    } else {
        $menu = '';
    }

    return $menu;
}

?>
