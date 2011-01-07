<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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
define('SCRIPT_FILENAME','popup.php');

ob_start();

// Initialize the Exponent Framework
require_once('exponent.php');

// Initialize the Theme Subsystem
if (!defined('SYS_THEME')) require_once(BASE.'subsystems/theme.php');

$loc = exponent_core_makeLocation(
	(isset($_GET['module'])?$_GET['module']:''),
	(isset($_GET['src'])?$_GET['src']:''),
	(isset($_GET['int'])?$_GET['int']:'')
);

$SYS_FLOW_REDIRECTIONPATH='popup';

if (exponent_theme_inAction()) {
	exponent_theme_runAction();
} else if (isset($_GET['module']) && isset($_GET['view'])) {
	exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_SECTIONAL);

	$mod = new $_GET['module']();
	$mod->show($_GET['view'],$loc,(isset($_GET['title'])?$_GET['title']:''));
}

$str = ob_get_contents();
ob_end_clean();

$template = new standalonetemplate('popup_'.(isset($_GET['template'])?$_GET['template']:'general'));
$template->assign('output',$str);
$template->output();

?>