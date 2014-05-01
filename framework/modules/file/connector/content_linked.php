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
 * Glue to open the module selection window for linking content from within the site url/link browser
 */

define("SCRIPT_EXP_RELATIVE","framework/modules/file/connector/");
define("SCRIPT_FILENAME","content_linked.php");

require_once("../../../../exponent.php");
global $router;
?>
<!DOCTYPE HTML>
<html>
	<head>
        <script type="text/javascript">
            function chosen() {
                var f_url = window.opener.document.getElementById("f_href");
                f_url.value = "<?php echo $router->buildUrlByPageId(expSession::get("last_section")); ?>#mod_<?php echo $_GET['cid']; ?>";
                var f_extern = window.opener.document.getElementById("f_extern");
                f_extern.checked = false;
                var f_text = window.opener.document.getElementById("f_text");
                f_text.innerHTML = "<?php echo $router->buildUrlByPageId(expSession::get("last_section")); ?>#mod_<?php echo $_GET['cid']; ?>";
                window.close();
            };
  		</script>
	</head>
    <body onload='chosen();'>
	</body>
</html>