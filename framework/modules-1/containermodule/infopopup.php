<?php
exit();
##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# Exponent is distributed in the hope that it
# will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR
# PURPOSE.  See the GNU General Public License
# for more details.
#
# You should have received a copy of the GNU
# General Public License along with Exponent; if
# not, write to:
#
# Free Software Foundation, Inc.,
# 59 Temple Place,
# Suite 330,
# Boston, MA 02111-1307  USA
#
# $Id: infopopup.php,v 1.1.1.1 2005/07/14 18:34:04 cvs Exp $
##################################################

define('SCRIPT_EXP_RELATIVE',"framework/modules-1/containermodule/");
define('SCRIPT_FILENAME',"infopopup.php");

include("../../../exponent.php");

$template = new template("containermodule","_popup_info");
$secref = null;

if (isset($_GET['id'])) {
	$container = $db->selectObject("container","id=".$_GET['id']);
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
	$secref = $db->selectObject("sectionref","module='".$_GET['mod']."' AND source='".$_GET['src']."'");
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