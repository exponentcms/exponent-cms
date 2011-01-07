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

function smarty_function_getfilename($params,&$smarty) {
	global $db;
	$file = $db->selectObject("file","id=".$params['id']);
	
	$filename = PATH_RELATIVE.$file->directory."/".$file->filename;
	if (isset($params['assign'])) $smarty->assign($params['assign'],$filename);
	else echo $filename;
}

?>