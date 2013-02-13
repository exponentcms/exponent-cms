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
            $params['text'] = gt($params['text']);
        }
        if (!empty($params['title'])) {
            $params['title'] = gt($params['title']);
        }

        // figure out whether to use the edit icon or text, alt tags, etc.
        $alt = (empty($params['alt'])) ? '' : $params['alt'];
        $class = (empty($params['class']) && empty($params['img'])) ? $params['action'] : $params['class'];
        $text = (empty($params['text'])) ? '' : $params['text'];
        $title = (empty($params['title'])) ? (empty($text) ? gt(ucfirst($class)) . ' ' . gt('this') . ' ' . $smarty->getTemplateVars('modelname') . ' ' . gt('item') : $text) : $params['title'];
        if (!empty($params['hash'])) {
//	    $hash = $params['hash'];
            unset($params['hash']);
        }

        if (empty($params['img']) && empty($params['text'])) {
            $img = gt(ucfirst($class));
        } else if (!empty($params['img'])) {
//	    $img 	= '<img class="'.$class.' btn" src="'.ICON_RELATIVE.$params['img'].'" title="'.$title.'" alt="'.$alt.'"'.XHTML_CLOSING.'>';
            $img = '<img class="' . $class . ' " src="' . ICON_RELATIVE . $params['img'] . '" title="' . $title . '" alt="' . $alt . '"' . XHTML_CLOSING . '>';
        } else $img = '';

        $linktext = $img . $text;

        $btn = '';
        switch ($class) {
            case 'delete' :
            case 'deletetitle' :
                $class = "remove-sign";
                $btn = " btn-danger";
                break;
            case 'add' :
            case 'addtitle' :
            case 'switchtheme add' :
                $class = "plus-sign";
                $btn = " btn-success";
                break;
            case 'copy' :
                $class = "copy";
                break;
            case 'downloadfile' :
                $class = "download-alt";
                break;
            case 'uploadfile' :
            case 'imxport' :
                $class = "upload-alt";
                break;
            case 'manage' :
                $class = "briefcase";
                break;
            case 'merge' :
                $class = "signin";
                break;
            case 'reranklink' :
            case 'alphasort' :
                $class = "sort";
                break;
            case 'configure' :
                $class = "wrench";
                break;
            case 'view' :
                $class = "zoom-in";
                break;
        }
        // we need to unset these vars before we pass the params array off to makeLink
        unset($params['alt']);
        unset($params['title']);
        unset($params['text']);
        unset($params['img']);
        unset($params['class']);
        unset($params['record']);
        unset($params['record']);
        $onclick = !empty($params['onclick']) ? $params['onclick'] : '';
        unset($params['onclick']);
        //eDebug($params);
        if (!empty($params['action'])) {
            if ($params['action'] == 'copy') {
                $params['copy'] = true;
                $params['action'] = 'edit';
            }
            echo '<a href="' . expCore::makeLink($params) . '" title="' . $title . '" class=" btn'.$btn.' icon-'.$class.' '.BTN_SIZE.'"';
            if (($params['action'] == "delete" || $params['action'] == "merge") && empty($onclick))
                echo ' onclick="return confirm(\'' . gt('Are you sure you want to') . ' ' . $params['action'] . ' ' . gt('this') . ' ' . $smarty->getTemplateVars('modelname') . ' ' . gt('item') . '?\');"';
//            if ($params['action'] == "merge" && empty($onclick))
//                echo ' onclick="return confirm(\'' . gt('Are you sure you want to merge this') . ' ' . $smarty->getTemplateVars('modelname') . ' ' . gt('item') . '?\');"';
            if (!empty($onclick))
                echo ' onclick="' . $onclick . '"';
            echo '> ' . $linktext . '</a>';
        } else {
            echo $linktext;
        }
    }
}

?>
