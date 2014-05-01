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
 * Smarty {get_user} function plugin
 *
 * Type:     function<br>
 * Name:     get_user<br>
 * Purpose:  get user name
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_get_user($params,&$smarty) {
    if (isset($params['user'])) {
        global $db;
        $thisuser = $db->selectObject('user', 'id='.intval($params['user']));
    } elseif (expSession::loggedIn()) {
		global $user;
        $thisuser = $user;
	}
    if (isset($params['assign'])) {
        $smarty->assign($params['assign'],$thisuser);
    } else {
        echo $thisuser->username;
    }
}

?>
