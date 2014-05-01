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
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {showaction} function plugin
 *
 * Type:     function<br>
 * Name:     showaction<br>
 * Purpose:  Display an action.<br>
 *
 * @param array $params
 * @param mixed $smarty
 */
function smarty_function_showaction($params,&$smarty) {
    expTheme::showAction($params['module'], $params['action'], $params['source'], $params['params']);
}

?>

