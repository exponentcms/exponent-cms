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
    
    // make sure we have a view..otherwise return nada.
    if (empty($params['view'])) return "";
    
    // get the view, pass params and render & return it.
    $view = isset($params['view']) ? $params['view'] : 'Downloadable Files';
	$title = isset($params['title']) ? $params['title'] : '';

    $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
    $config['uniqueid'] = str_replace($badvals, "", $smarty->_tpl_vars[__loc]->src).$params['id'];

	$template = get_common_template($view, $smarty->_tpl_vars[__loc], 'file');
	$template->assign('files', $params['files']);
	$template->assign('style', $params['style']);
	$template->assign('config', $config);
	$template->assign('params', $params);
	$html = $template->render();
	echo $html;
}

?>
