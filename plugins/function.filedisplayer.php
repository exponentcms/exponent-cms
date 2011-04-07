<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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

function smarty_function_filedisplayer($params,&$smarty) {
    $config = $smarty->_tpl_vars['config'];
    
    // make sure we have a view, and files..otherwise return nada.
    if (empty($params['view']) || empty($params['files'])) return "";
    
    // get the view, pass params and render & return it.
    $view = isset($params['view']) ? $params['view'] : 'Downloadable Files';
	$title = isset($params['title']) ? $params['title'] : '';

    $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
    $config['uniqueid'] = str_replace($badvals, "", $smarty->_tpl_vars[__loc]->src).$params['record']->id;
    if ($config['pio'] && $params['is_listing']) {
        $tmp = reset($params['files']);
        unset($params['files']);
        $params['files'][] = $tmp;
    };
    
    $float = $config['float']=="No Float"?"":"float:".strtolower($config['float']).";";
    $width = !empty($config['width'])?$config['width']:"200";
    
    switch ($config['float']) {
        case 'Left':
            $margin = "margin-right:".$config['margin']."px;";
            break;
        
        case 'Right':
            $margin = "margin-left:".$config['margin']."px;";
            break;

        default:
            $margin = "";
            break;
    }
    
    $html = '<div class="display-files" style="'.$float.'width:'.$width.'px;'.$margin.'">';
    $template = get_common_template($view, $smarty->_tpl_vars[__loc], 'file');
	$template->assign('files', $params['files']);
	$template->assign('style', $params['style']);
	$template->assign('config', $config);
	$template->assign('params', $params);
	$html .= $template->render();
    $html .= "</div>";
	echo $html;
}

?>
