<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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
 * Smarty {filedisplayer} function plugin
 *
 * Type:     function<br>
 * Name:     filedisplayer<br>
 * Purpose:  display files
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_filedisplayer($params,&$smarty) {
    $config = $smarty->getTemplateVars('config');
    
    // make sure we have a view, and files..otherwise return nada.
    if (empty($params['view']) || empty($params['files'])) return "";
    
    // get the view, pass params and render & return it.
    $view = isset($params['view']) ? $params['view'] : 'Downloadable Files';
	$title = isset($params['title']) ? $params['title'] : '';

    $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
    $config['uniqueid'] = str_replace($badvals, "", $smarty->getTemplateVars('__loc')->src).$params['record']->id;
    if ($config['pio'] && $params['is_listing']) {
        $tmp = reset($params['files']);
        unset($params['files']);
        $params['files'][] = $tmp;
    };
    
    $float = $config['ffloat']=="No Float"?"":"float:".strtolower($config['ffloat']).";";
    $width = !empty($config['fwidth'])?$config['fwidth']:"200";
    
    switch ($config['ffloat']) {
        case 'Left':
            $margin = "margin-right:".$config['fmargin']."px;";
            break;
        
        case 'Right':
            $margin = "margin-left:".$config['fmargin']."px;";
            break;

        default:
            $margin = "";
            break;
    }
    
    $html = '<div class="display-files" style="'.$float.'width:'.$width.'px;'.$margin.'">';
    $template = get_common_template($view, $smarty->getTemplateVars('__loc'), 'file');
	$template->assign('files', $params['files']);
	$template->assign('style', $params['style']);
	$template->assign('config', $config);
	$template->assign('params', $params);
	$html .= $template->render();
    $html .= "</div>";
	echo $html;
}

?>
