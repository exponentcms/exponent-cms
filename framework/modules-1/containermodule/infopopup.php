<?php
exit();
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

define('SCRIPT_EXP_RELATIVE',"framework/modules-1/containermodule/");
define('SCRIPT_FILENAME',"infopopup.php");

include("../../../exponent.php");

$template = new template("containermodule","_popup_info");
$secref = null;

if (isset($_GET['id'])) {
	$container = $db->selectObject("container","id=".intval($_GET['id']));
	if ($container) {
		$iloc = unserialize($container->internal);
		$secref = $db->selectObject("sectionref","module='".$iloc->mod."' AND source='".$iloc->src."'");
	
		$template->assign("is_orphan",0);
		$template->assign("container",$container);
	} else {
		echo SITE_404_HTML;
		exit();
	}
} else {
	$secref = $db->selectObject("sectionref","module='".expString::sanitize($_GET['mod'])."' AND source='".expString::sanitize($_GET['src'])."'");
	$template->assign("is_orphan",1);
}

if ($secref) {
	if (class_exists($secref->module)) $template->assign("name",call_user_func(array($secref->module,"name")));
	else $template->assign("name","");
	
	$template->assign("info",$secref->description);
} else {
	$template->assign("name","");
	$template->assign("info","");
}

$template->output();

?>