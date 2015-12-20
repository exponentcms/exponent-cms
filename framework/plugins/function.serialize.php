<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 *
 * @package    Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {serialize} function plugin
 * -------------------------------------------------------------
 * Type:     function<br>
 * Name:     serialize<br>
 * Purpose:  Converts an assigned variable (or value) to a text representation compatable with
 *           the {assocarray} block plugin
 * Version:  1.0
 * Author:    boots
 *
 * @param value        required variable of value to be serialized
 * @param var          optional if set, smarty assigns serialed value
 *                     to $var without output
 *
 * @return string
 */
function smarty_function_serialize($params, &$smarty)
{
    extract($params);
    if (empty($value)) {
        $smarty->trigger_error("serialize: missing 'value' parameter");
    }

    $retval = _srlz($value);

    if (!empty($var)) {
        $smarty->assign($var, $retval);
    } else {
        return $retval;
    }
}

function _srlz($data)
{
    if (is_array($data)) {
        $retval = '[';
        foreach ($data as $k => $v) {
            $use_key = (!$k || !is_string($k)) ? false : true;
            if ($use_key) {
                $retval .= "$k: ";
            }
            if (is_array($v)) {
                $retval .= _srlz($v);
            } else {
                if (is_string($v)) {
                    $retval .= '"' . $v . '"';
                } else {
                    $retval .= $v;
                }
            }
            if ($use_key) {
                $retval .= "\n";
            } else {
                $retval .= ",";
            }
        }
        $retval = substr($retval, 0, strlen($retval) - 1);
        $retval .= ']' . "\n";
    } else {
        if (is_string($data)) {
            $retval = '"' . $data . '"' . "\n";
        } else {
            $retval = $data . "\n";
        }
    }
    return $retval;
}

?>