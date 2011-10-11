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
/** @define "BASE" "." */

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','popup.php');

ob_start();

// Initialize the Exponent Framework
require_once('exponent.php');

$loc = expCore::makeLocation(
	(isset($_GET['module'])?$_GET['module']:''),
	(isset($_GET['src'])?$_GET['src']:''),
	(isset($_GET['int'])?$_GET['int']:'')
);

$SYS_FLOW_REDIRECTIONPATH='popup';

if (expTheme::inAction()) {
	expTheme::runAction();
} else if (isset($_GET['module']) && isset($_GET['view'])) {
	expHistory::flowSet(SYS_FLOW_PUBLIC,SYS_FLOW_SECTIONAL);

	$mod = new $_GET['module']();
	$mod->show($_GET['view'],$loc,(isset($_GET['title'])?$_GET['title']:''));
}

$str = ob_get_contents();
ob_end_clean();

$template = new standalonetemplate('popup_'.(isset($_GET['template'])?$_GET['template']:'general'));
$template->assign('output',$str);
$template->output();

?>