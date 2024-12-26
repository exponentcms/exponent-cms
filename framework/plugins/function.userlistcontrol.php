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
 * Smarty {userlistcontrol} function plugin
 *
 * Type:     function<br>
 * Name:     userlistcontrol<br>
 * Purpose:  display a list control of users
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_userlistcontrol($params, &$smarty) {
    global $db;

    $users = $db->selectObjects("user", null, "username LIMIT 2500");

    $selected = isset($params['items']) ? $params['items'] : array();
    foreach ($users as $user) {
        if (!in_array($user->id, $selected)) {
            //TODO should we display username w/ first/last name in parens or first/last name?
            if (empty($user->lastname) && empty($user->firstname)) {
                $allusers[$user->id] = "($user->username)";
            } else {
                $allusers[$user->id] = "$user->lastname, $user->firstname ($user->username)";
            }
        } else {
            if (empty($user->lastname) && empty($user->firstname)) {
                $selectedusers[$user->id] = "($user->username)";
            } else {
                $selectedusers[$user->id] = "$user->lastname, $user->firstname ($user->username)";
            }
        }
    }

    $size = (isset($params['size'])) ? $params['size'] : 5;
    $control = new listbuildercontrol($selectedusers, $allusers, $size);
    if (!empty($params['class'])) $control->class = $params['class'];
    $name    = isset($params['name']) ? $params['name'] : "userlist";
    $label   = isset($params['label']) ? $params['label'] : "";
    echo $control->ToHTML($label,$name);
}

?>
