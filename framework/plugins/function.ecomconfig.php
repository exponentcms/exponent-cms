<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * Smarty {ecomconfig} function plugin
 *
 * Type:     function<br>
 * Name:     ecomconfig<br>
 * Purpose:  return the ecom configuration
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return null|string
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_ecomconfig($params,&$smarty) {
    $retval = ecomconfig::getConfig($params['var']);
    if (empty($retval))
        return $params['default'];
    else {
        if (!empty($params['unescape']))
            $retval = stripcslashes($retval);
        if (!empty($params['json'])) {
            $retval = str_replace('\r\n','', $retval = json_encode($retval));
        }
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $retval);
        } else {
            return $retval;
        }
    }
}
?>