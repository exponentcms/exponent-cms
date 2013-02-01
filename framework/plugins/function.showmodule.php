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
 * Smarty {showmodule} function plugin
 *
 * Type:     function<br>
 * Name:     showmodule<br>
 * Purpose:  Display a module.<br>
 *
 * @param array $params
 * @param mixed $smarty
 */
function smarty_function_showmodule($params,&$smarty) {
    $module = !empty($params['module']) ? $params['module'] : null;
    $action = !empty($params['action']) ? $params['action'] : null;
    $view = !empty($params['view']) ? $params['view'] : null;
    $source = !empty($params['source']) ? $params['source'] : null;
    $chrome = !empty($params['chrome']) ? $params['chrome'] : null;
    $scope = !empty($params['scope']) ? $params['scope'] : null;
    $moduletitle = !empty($params['moduletitle']) ? $params['moduletitle'] : null;
    $parms = !empty($params['params']) ? $params['params'] : null;
    expTheme::module(array("module"=>$module,"action"=>$action,"view"=>$view,"source"=>$source,"chrome"=>$chrome,"moduletitle"=>$moduletitle,"scope"=>$scope,"params"=>$parms));
}

?>

