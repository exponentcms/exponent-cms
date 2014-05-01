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
 * Smarty {gettext} function plugin
 *
 * Type:     function<br>
 * Name:     gettext<br>
 * Purpose:  Replace with the chosen language for this text
 *
 * @param $params
 * @param $smarty
 */
function smarty_function_gettext($params,&$smarty) {
    echo expLang::gettext($params['str']);
}

?>

