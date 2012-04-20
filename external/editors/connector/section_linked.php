<?php

##################################################
#
# Copyright (c) 2004-2011 James Hunt and the OIC Group, Inc.
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
# $Id: section_linked.php 1984 2007-11-27 22:51:14Z kessler44 $
##################################################

#  Thanks to Daniel Grabert for this patch. - 1/12/05
	define("SCRIPT_EXP_RELATIVE","external/editors/connector/");
	define("SCRIPT_FILENAME","section_linked.php");
	require_once("../../../exponent.php");

	if (empty($_REQUEST['section'])) {
		// bad request - no section found

		// go back to referring page, if available
		$referer_url = $_SERVER['HTTP_REFERER'];
		if ( $referer_url ) {
			header("Location: $referer_url");
		} else {
			echo SITE_403_HTML;
			exit();
		}
	}
?>
<!DOCTYPE HTML>
<html>
	<body>
	<script type="text/javascript">
	var f_url = window.opener.document.getElementById("f_href");
	var f_extern = window.opener.document.getElementById("f_extern");
	var f_title = window.opener.document.getElementById("f_title");
	
	// set value for url form element in opener
	f_url.value = "<?php echo $router->buildUrlByPageId($_REQUEST['section']); ?>";

	// uncheck external link box in parent window
	f_extern.checked = false;

	// set title
	f_title.value = "<?php echo 'Link to page ' .  $_REQUEST['section_name']; ?>";
	
	window.close();
	</script>
	</body>
</html>

