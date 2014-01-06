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
 *
 * @package    Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {icon} function plugin
 *
 * Type:     function<br>
 * Name:     icon<br>
 * Purpose:  create an icon type link
 *
 * @param         $params
 * @param \Smarty $smarty
 */
if (!function_exists('smarty_function_icon')) {
    function smarty_function_icon($params, &$smarty) {
        $loc = $smarty->getTemplateVars('__loc');
        if (isset($params['record'])) {
            $record = $params['record'];
            $params['id'] = $record->id;
        }

        if (!empty($record) && empty($params['id'])) {
            $params['id'] = $record->id;
        }

        // setup the link params
        if (!isset($params['controller'])) {
            if (!isset($params['module'])) $params['module'] = $loc->mod;
            if (expModules::controllerExists($params['module'])) {
                $params['controller'] = expModules::getControllerName($params['module']);
                unset($params['module']);
            }
        }
        // guess the src if it is not set
        if (!isset($params['src'])) {
            if (!empty($record)) {
                $modloc = expUnserialize($record->location_data);
                $params['src'] = $modloc->src;
            } else if (!empty($params['controller']) || @call_user_func(array(expModules::getModuleClassName($loc->mod), 'hasSources'))) {
                $params['src'] = $loc->src;
            } elseif (!empty($params['module']) || @call_user_func(array(expModules::getModuleClassName($loc->mod), 'hasSources'))) {
                $params['src'] = $loc->src;
            }
        }
        $config = $smarty->getTemplateVars('config');
        $noeditagg = 0;
        if (is_object($config)) {
            $noeditagg = !empty($config->noeditagg) ? $config->noeditagg : 0;
        } elseif (is_array($config)) {
            $noeditagg = !empty($config['noeditagg']) ? $config['noeditagg'] : 0;
        }
        if ($noeditagg && ($smarty->getTemplateVars('__loc')->src != $params['src'])) return;

        if (!isset($params['int'])) $params['int'] = $loc->int;

        // attempt to translate the alt, text, & title
        if (!empty($params['alt'])) {
            $params['alt'] = gt($params['alt']);
        }
        if (!empty($params['text'])) {
            if ($params['text'] == "notext") {
                $params['text'] = '';
                if (empty($params['img']) && !empty($params['action'])) {
                    $params['img'] = $params['action'] . '.png';
                }
                if (empty($params['title'])) {
                    $params['title'] = (empty($text) ? gt(ucfirst($params['action'])) . ' ' . gt('this') . ' ' . $smarty->getTemplateVars('modelname') . ' ' . gt('item') : $text);
                }
            } else $params['text'] = gt($params['text']);
        }
        if (!empty($params['title'])) {
            $params['title'] = gt($params['title']);
        }

        // figure out whether to use the edit icon or text, alt tags, etc.
        $alt = (empty($params['alt'])) ? '' : $params['alt'];
        $class = (empty($params['class']) && empty($params['img'])) ? $params['action'] : (!empty($params['class']) ? $params['class'] : '');
        $text = (empty($params['text'])) ? '' : $params['text'];
        $title = (empty($params['title'])) ? (empty($text) ? gt(ucfirst($class)) . ' ' . gt('this') . ' ' . $smarty->getTemplateVars('modelname') . ' ' . gt('item') : $text) : $params['title'];
        if (!empty($params['hash'])) {
//	    $hash = $params['hash'];
            unset($params['hash']);
        }

        if  (empty($params['img']) && empty($params['text'])) {
            $img = gt(ucfirst($class));
        } else if (!empty($params['img'])) {
            $imgtmp = explode('.',$params['img']);
            $class = $imgtmp[0];
            $img = '';
//	    $img 	= '<img class="'.$class.' btn" src="'.ICON_RELATIVE.$params['img'].'" title="'.$title.'" alt="'.$alt.'"'.XHTML_CLOSING.'>';
//            $img = '<img class="' . $class . ' " src="' . ICON_RELATIVE . $params['img'] . '" title="' . $title . '" alt="' . $alt . '"' . XHTML_CLOSING . '>';
        } else {
            $img = '';
        }

        $linktext = $img . $text;
        
        if (BTN_SIZE == 'large' || (!empty($params['size']) && $params['size'] == 'large')) {
            $btn_size = '';  // actually default size, NOT true boostrap large
            $icon_size = 'icon-large';
        } elseif (BTN_SIZE == 'small' || (!empty($params['size']) && $params['size'] == 'small')) {
            $btn_size = 'btn-mini';
            $icon_size = '';
        } else { // medium
            $btn_size = 'btn-small';
            $icon_size = 'icon-large';
        }

//        $btn_type = '';
//        switch ($class) {
//            case 'delete' :
//            case 'deletetitle' :
//                $class = "remove-sign";
//                $btn_type = "btn-danger";  // red
//                break;
//            case 'add' :
//            case 'addtitle' :
//            case 'switchtheme add' :
//                $class = "plus-sign";
//                $btn_type = "btn-success";  // green
//                break;
//            case 'copy' :
//                $class = "copy";
//                break;
//            case 'downloadfile' :
//            case 'export' :
//                $class = "download-alt";
//                break;
//            case 'uploadfile' :
//            case 'import' :
//                $class = "upload-alt";
//                break;
//            case 'manage' :
//                $class = "briefcase";
//                break;
//            case 'merge' :
//            case 'arrow_merge' :
//                $class = "signin";
//                break;
//            case 'reranklink' :
//            case 'alphasort' :
//                $class = "sort";
//                break;
//            case 'configure' :
//                $class = "wrench";
//                break;
//            case 'view' :
//                $class = "search";
//                break;
//            case 'page_next' :
//                $class ='double-angle-right';
//                break;
//            case 'page_prev' :
//                $class = 'double-angle-left';
//                break;
//            case 'change_password' :
//                $class = 'key';
//                break;
//            case 'clean' :
//                $class = 'check';
//                break;
//            case 'groupperms' :
//                $class = 'group';
//                break;
//            case 'monthviewlink' :
//            case 'weekviewlink' :
//                $class = 'calendar';
//                break;
//            case 'listviewlink' :
//                $class = 'list';
//                break;
//            case 'adminviewlink' :
//                $class = 'cogs';
//                break;
//        }
        $icon = expTheme::buttonIcon($class);
        if (!empty($params['style']) ) $icon->type = $params['style'];
        if (!empty($params['icon']) ) $icon->class = $params['icon'];
        if (!empty($params['color']) ) $icon->type = expTheme::buttonColor($params['color']);  // color was specifically set

        // we need to unset these vars before we pass the params array off to makeLink
        unset($params['alt']);
        unset($params['title']);
        unset($params['text']);
        unset($params['img']);
        unset($params['class']);
        unset($params['record']);
        unset($params['style']);
        unset($params['icon']);
        unset($params['size']);
        unset($params['color']);
        $onclick = !empty($params['onclick']) ? $params['onclick'] : '';
        unset($params['onclick']);
        $secure = !empty($params['secure']) ? $params['secure'] : false;
        unset($params['secure']);
        $button = !empty($params['button']) ? $params['button'] : false;
        unset($params['button']);
        //eDebug($params);
        if (!empty($params['name'])) {
            $name = ' id="'.$params['name'].'"';
        } else {
            $name = '';
        }
        if(!empty($params['action']) && $params['action'] == 'scriptaction') {
            echo '<a'.$name.' href="#" title="' . $title . '" class=" btn '.$icon->type.' '.$btn_size.'"';
            if (!empty($onclick))
                echo ' onclick="' . $onclick . '"';
            echo '><i class="icon-'.$icon->class.' '.$icon_size.'"></i> ' . $linktext . '</a>';
        } elseif ((!empty($params['action']) && $params['action'] != 'scriptaction') || $button) {
            if ($params['action'] == 'copy') {
                $params['copy'] = true;
                $params['action'] = 'edit';
            }
            if (!empty($params['link'])) {
                $link = $params['link'];
            } else {
                $link = makeLink($params,$secure);
            }
            echo '<a'.$name.' href="' . $link . '" title="' . $title . '" class=" btn '.$icon->type.' '.$btn_size.'"';
            if (($params['action'] == "delete" || $params['action'] == "merge" || $icon->class == "delete" || $icon->class == "merge") && empty($onclick))
                echo ' onclick="return confirm(\'' . gt('Are you sure you want to') . ' ' . $params['action'] . ' ' . gt('this') . ' ' . $smarty->getTemplateVars('modelname') . ' ' . gt('item') . '?\');"';
//            if ($params['action'] == "merge" && empty($onclick))
//                echo ' onclick="return confirm(\'' . gt('Are you sure you want to merge this') . ' ' . $smarty->getTemplateVars('modelname') . ' ' . gt('item') . '?\');"';
            if (!empty($onclick))
                echo ' onclick="' . $onclick . '"';
            echo '><i class="icon-'.$icon->class.' '.$icon_size.'"></i> ' . $linktext . '</a>';
        } else {
            echo '<div class=" btn disabled '.$icon->type.' '.$btn_size.'"><i class="icon-'.$icon->class.' '.$icon_size.'"></i> ' .$linktext.'</div>';
        }
    }
}

?>
