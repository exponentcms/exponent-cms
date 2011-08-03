<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');

$i18n = exponent_lang_loadFile('modules/loginmodule/actions/login.php');

//if (!defined('SYS_USERS')) require_once(BASE.'framework/core/subsystems-1/users.php');
require_once(BASE.'framework/core/subsystems-1/users.php');
$user = exponent_users_login($_POST['username'],$_POST['password']);

if (!isset($_SESSION[SYS_SESSION_KEY]['user'])) {
	flash('error', $i18n['login_error']);	
	if (expSession::is_set('redirecturl_error')) {
		$url = expSession::get('redirecturl_error');
		expSession::un_set('redirecturl_error');
		header("Location: ".$url);
	} else {
		exponent_flow_redirect();
	}
} else {
    global $user;
    flash ('message', 'Welcome back '.$_POST['username']);
	foreach ($user->groups as $g) {
	    if (!empty($g->redirect)) {
	        $url = URL_FULL.$g->redirect;
	        break;
	    }
	}
	if (isset($url)) {
		header("Location: ".$url);
	} else {
		exponent_flow_redirect();
	}
}

?>
