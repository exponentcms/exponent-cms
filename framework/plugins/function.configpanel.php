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
 * Smarty {configpanel} function plugin
 *
 * Type:     function<br>
 * Name:     configpanel<br>
 * Purpose:  display a config panel
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_configpanel($params,&$smarty) {
	$cp = new configcontrol($params['title'], $params['welcome'], $params['opts']);
	echo $cp->controlToHTML('test', 'test');
}

?>

