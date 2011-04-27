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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','login_redirect.php');

ob_start();

// Initialize the Exponent Framework
require_once('exponent.php');

// Initialize the Sessions Subsystem
if (!defined('SYS_SESSIONS')) require_once(BASE.'subsystems/sessions.php');

if (isset($_GET['redirecturl'])) {
	$redirect = urldecode($_GET['redirecturl']);
	if (substr($redirect,0,4) != 'http') {
		$redirect = URL_FULL.$redirect;
	}
	exponent_sessions_set('redirecturl',$redirect);
}

// Initialize the Theme Subsystem
if (!defined('SYS_THEME')) require_once(BASE.'subsystems/theme.php');
$SYS_FLOW_REDIRECTIONPATH = 'loginredirect'; 

if (exponent_sessions_loggedIn()) {
	$url = exponent_sessions_get('redirecturl');
	if ($url . '' == '') {
		$SYS_FLOW_REDIRECTIONPATH = 'default';
		exponent_flow_redirect();
	}
	header('Location: ' . $url);
	exit('Redirecting...');
} else if (isset($_REQUEST['module']) && isset($_REQUEST['action'])) {
	exponent_theme_runAction();
	loginmodule::show(DEFAULT_VIEW,null);
} else {
	exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_SECTIONAL);
	loginmodule::show(DEFAULT_VIEW,null);
}

$template = new standalonetemplate('loginredirect');

$template->assign('output',ob_get_contents());
ob_end_clean();
$template->output();

?>