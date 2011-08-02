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

define('SCRIPT_EXP_RELATIVE','modules/containermodule/');
define('SCRIPT_FILENAME','picked_source.php');

include_once('../../exponent.php');

$src = $_GET['ss'];
$mod = $_GET['sm'];

//$locref = $db->selectObject("locationref","module='".$mod."' AND source='".$src."'");
$secref = $db->selectObject("sectionref","module='".$mod."' AND source='".$src."'");
if (!isset($secref->description)) $secref->description = '';

?>
<html>
<head>
<script type="text/javascript">
function saveSource() {
	window.opener.sourcePicked("<?php echo $_GET['ss']; ?>","<?php echo str_replace(array("\"","\r\n"),array("\\\"","\\r\\n"),$secref->description); ?>");
	window.close();
	
}
</script>
</head>
<body onload="saveSource()">
</body>
</html>