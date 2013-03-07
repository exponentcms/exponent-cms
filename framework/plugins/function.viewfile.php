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
 * Smarty {viewfile} function plugin
 *
 * Type:     function<br>
 * Name:     viewfile<br>
 * Purpose:  get and assign a view file
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_viewfile($params,&$smarty) {
    $controller = expModules::getController($params['module']);
	$view = get_template_for_action($controller,$params['view']);
	$smarty->assign($params['var'],$view->viewfile);
}
