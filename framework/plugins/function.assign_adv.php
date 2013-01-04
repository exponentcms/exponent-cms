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
 * Smarty {assign_adv} function plugin
 *
 * Type:     function<br>
 * Name:     assign_adv<br>
 * Purpose:  Advanced assign variable to template
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_assign_adv($params, &$smarty)
{
    extract($params);

    if (empty($var)) {
        $smarty->trigger_error(gt("assign_adv: missing 'var' parameter"));
        return;
    }

    if (!in_array('value', array_keys($params))) {
        $smarty->trigger_error(gt("assign_adv: missing 'value' parameter"));
        return;
    }
    $value = isset($params['value']) ? $params['value'] : null;
    if (preg_match('/^\s*array\s*\(\s*(.*)\s*\)\s*$/s',$value,$match)){
        eval('$value=array('.str_replace("\n", "", $match[1]).');');
    }
    else if (preg_match('/^\s*range\s*\(\s*(.*)\s*\)\s*$/s',$value,$match)){
        eval('$value=range('.str_replace("\n", "", $match[1]).');');
    }

    $smarty->assign($var, $value);
}
?>