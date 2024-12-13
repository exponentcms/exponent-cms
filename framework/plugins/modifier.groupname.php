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
 * Smarty {groupname} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     groupname<br>
 * Purpose:  return the group name for a group id
 *
 * @param array
 *
 * @return string
 *
 * @package Smarty-Plugins
 * @subpackage Modifier
 */
function smarty_modifier_groupname($groupid) {
	global $db;
	return $db->selectValue('group', 'name', 'id='.(int)($groupid));
}

?>
