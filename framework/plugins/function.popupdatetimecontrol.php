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
/** @define "BASE" "../.." */

function smarty_function_control($params,&$smarty) {
	if (isset($params['name']) ) {
//		require_once(BASE.'framework/core/subsystems-1/forms.php');
		$control = new $params['type'];
		echo $control->controlToHTML($params['name']);
	}
}

?>
