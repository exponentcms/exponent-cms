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
 *
 * @package    Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {grouplistcontrol} function plugin
 *
 * Type:     function<br>
 * Name:     grouplistcontrol<br>
 * Purpose:  display a list control of user groups
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_grouplistcontrol($params, &$smarty) {
//    echo '<script type="text/javascript" src="' . PATH_RELATIVE . 'framework/core/subsystems/forms/controls/listbuildercontrol.js"></script>';

    global $db;
    $groups = $db->selectObjects("group", null, "name");

    $selected = isset($params['items']) ? $params['items'] : null;
    foreach ($groups as $group) {
        if (!in_array($group->id, $selected)) {
            $allgroups[$group->id] = "$group->name";
        } else {
            $selectedgroups[$group->id] = "$group->name";
        }
    }

    $control = new listbuildercontrol($selectedgroups, $allgroups, 5);
    if (!empty($params['class'])) $control->class = $params['class'];
    $name    = isset($params['name']) ? $params['name'] : "grouplist";
    $label   = isset($params['label']) ? $params['label'] : "";
//    echo $control->controlToHTML($name);
    echo $control->ToHTML($label,$name);
}

?>
