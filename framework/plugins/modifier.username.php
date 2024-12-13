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
 * Smarty {username} modifier plugin
 * Type:     modifier<br>
 * Name:     username<br>
 * Purpose:  return the username for a user id
 *
 * @param         $userid
 * @param boolean $type if true return user name instead of record
 *
 * @return string
 *
 * @package Smarty-Plugins
 * @subpackage Modifier
 */
function smarty_modifier_username($userid,$type=null) {
	global $db;

    if ($type == 'system') {
        return user::getUserAttribution((int)($userid), DISPLAY_ATTRIBUTION);
    } elseif ($type) {
        $user = $db->selectObject('user', 'id='.(int)($userid));
        return $user->firstname . ' ' . $user->lastname;
    } else
        return $db->selectValue('user', 'username', 'id='.(int)($userid));
}

?>
