<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

function smarty_function_filepicker($params,&$smarty) {
	global $db;
	$files = file::findFilesForItem($params['item_type'], $params['item_id']);
	echo '<script type="text/javascript" src="'.URL_FULL.'modules/cermi/js/funcs.js" /></script>';
	echo '<a href="javascript:void(0);" onclick="window.open(\''.URL_FULL."modules/cermi/actions/picker.php?item_type=".$params['item_type']."', 'filepicker', 'width=800,height=600,resizeable=no,scrollbars=no,status=no,menubar=no')\">Add Files</a>";
	echo '<div id="files-previews"></div>';
	echo '<span id="'.$params['item_type'].'">';
	echo '</span>';
}

?>
