<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * Smarty {navtojson} function plugin
 *
 * Type:     function<br>
 * Name:     navtojson<br>
 * Purpose:  caonvert navigation structure to javascript via json
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_navtojson($params,&$smarty) {
    echo navigationmodule::navtojson();
}
?>