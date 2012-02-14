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
 * Smarty {attribution} function plugin
 *
 * Type:     function<br>
 * Name:     attribution<br>
 * Purpose:  create an attribution phrase
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_attribution($params,&$smarty) {
	if (isset($params['user_id'])) {
		$u = user::getUserById($params['user_id']);
	} elseif (isset($params['user'])) {
		$u = $params['user'];
	}

	if (!empty($u->id)) {
		$str = "";
		$display = isset($params['display']) ? $params['display'] : DISPLAY_ATTRIBUTION;
		switch ($display) {
			case "firstlast":
				$str = $u->firstname . " " . $u->lastname;
				break;
			case "lastfirst":
				$str = $u->lastname . ", " . $u->firstname;
				break;
			case "first":
				$str = $u->firstname;
				break;
			case "username":
			default:
				$str = $u->username;
				break;
		}
		echo $str;
	} else {
		echo gt('Anonymous User');
	}
}

?>
