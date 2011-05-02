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

function smarty_function_getfileicon($params,&$smarty) {
	global $db;
	
	if (!isset($params['file'])) {
	    //$file = $db->selectObject("file","id=".$params['id']);
	    $file = new expFile($params['id']);
	} else {
		$file = $params['file'];
	}
	
	$mimetype = $db->selectObject("mimetype","mimetype='".$file->mimetype."'");
	if ($mimetype->icon != "") {
		echo '<img src="'.MIMEICON_RELATIVE .$mimetype->icon.'"/>';
	} else {
	    if (empty($file->is_image)) {
	        echo '<img src="'.MIMEICON_RELATIVE .'unknown.png"/>';
	    } else {
	        echo '<img src="'.MIMEICON_RELATIVE .'image.png"/>';
	    }
	}
}

?>
