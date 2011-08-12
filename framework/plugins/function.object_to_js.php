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
/** @define "BASE" "../.." */

function smarty_function_object_to_js($params,&$smarty) {
	require_once(BASE."framework/core/subsystems-1/javascript.php");
	echo "var ".$params['name']." = new Array();\n";
	if (isset($params['objects']) && count($params['objects']) > 0) {
		
		//Write Out DataClass. This is generated from the data object.
		echo exponent_javascript_class($params['objects'][0],"class_".$params['name']);
		
		//This will load up the data...
		foreach ($params['objects'] as $object) {
			echo $params['name'].".push(".exponent_javascript_object($object,"class_".$params['name']).");\n";
			//Stuff in a unique id for reference.
			echo $params['name']."[".$params['name'].".length-1].__ID = ".$params['name'].".length-1;\n";		
		}
	}
	
	return "";
}

?>