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
 * Smarty {chain} function plugin
 *
 * Type:     function<br>
 * Name:     chain<br>
 * Purpose:  chain/append templates
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 *
 * @deprecated
 * @package Smarty-Plugins
 * @subpackage Function
 */  //FIXME old school way of calling?
function smarty_function_chain($params,&$smarty) {
    if (empty($params['module']) && empty($params['controller']))  return false;

    if (isset($params['source'])) $params['src'] = $params['source'];

    $src = isset($params['src']) ? $params['src'] : $smarty->getTemplateVars('__loc')->src;

    if (isset($params['module'])) {  //FIXME old school only?
//        $chrome = $params['chrome'] == "none" ? true : false;
        $chrome = empty($params['chrome']) ? true : false;
        $title = isset($params['title']) ? $params['title'] : '';
        $view = isset($params['view']) ? $params['view'] : 'Default';
        $action = isset($params['action']) ? $params['action'] : null;
		$parms = (isset($params['params'])?$params['params']:null);
		if(!$parms) {
			//return;
		} else {
			eval ('$new_parms = '.$parms.';');
		    $parms = $new_parms;
		}
        if (empty($action)) {
            expTheme::showModule($params['module'], $view, $title, $src, false, null, $chrome);
        } else {
            expTheme::showAction($params['module'], $action, $src, $parms);
        }
    } elseif (isset($params['controller'])) {
        $view = isset($params['view']) ? $params['view'] : $params['action'];
        $action = isset($params['action']) ? $params['action'] : 'index';
        $scope = isset($params['scope']) ? $params['scope'] : 'global';
        //$chrome = isset($params['chrome']) ? '"chrome"=>true' : '';
        $source = isset($params['source']) ? $params['source'] : $smarty->getTemplateVars('__loc')->src;
        $cfg = array(
            "controller"=>$params['controller'],
            "action"=>$action,
            "view"=>$view,
            "source"=>$source,
            "scope"=>$scope
        );

        //because of the silly way we have to toggle chrome
        if (!empty($params['chrome'])) {
            $cfg['chrome'] = true;
        } else {
	        $cfg['chrome'] = false;
        }
        //eDebug($cfg);
        expTheme::module($cfg);
    }
}

?>

