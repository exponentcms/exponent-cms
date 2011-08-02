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

if (!defined('EXPONENT')) exit('');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title><?php echo SITE_TITLE; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" <?php echo XHTML_CLOSING; ?>>
		<meta name="Generator" value="Exponent Content Management System" <?php echo XHTML_CLOSING; ?>>
		<link rel="stylesheet" title="default" href="<?php echo THEME_RELATIVE; ?>style.css" <?php echo XHTML_CLOSING; ?>>
		<script type="text/javascript" src="<?php echo PATH_RELATIVE; ?>exponent.js.php"></script>
	</head>

	<body onload="exponentJSinitialize()">
	<?php
	define("PREVIEW_READONLY",1);

	$module = $_GET['module'];
	$view = $_GET['view'];
	$mod = new $module();
	$title = $_GET['title'];

	$source = (isset($_GET['source']) ? $_GET['source'] : "@example");
	$loc = exponent_core_makeLocation($module,$source,"");
	$mod->show($view,$loc,$title);
	?>
	<script type="text/javascript">
	var elems = document.getElementsByTagName("a");
	for (var i = 0; i < elems.length; i++) {
		elems[i].setAttribute("onclick","return false;");
	}

	elems = document.getElementsByTagName("input");
	for (var i = 0; i < elems.length; i++) {
		if (elems[i].type == "submit") elems[i].setAttribute("disabled","disabled");
	}

	elems = document.getElementsByTagName("button");
	for (var i = 0; i < elems.length; i++) {
		elems[i].setAttribute("disabled","disabled");
	}
	</script>
	</body>
</html>
