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

/**
 * Smarty {popupdatetimecontrol} function plugin
 *
 * Type:     function<br>
 * Name:     popupdatetimecontrol<br>
 * Purpose:  ???
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_popupdatetimecontrol($params,&$smarty) {  //FIXME this seems like a empty copy of something else
	if (isset($params['name']) ) {
		$control = new $params['type'];
		echo $control->controlToHTML($params['name']);
	}
}

?>
