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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {list_object} function plugin
 *
 * Type:     function<br>
 * Name:     list_object<br>
 * Purpose:  place an object in a unordered list
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_list_object($params,&$smarty) {
	if (isset($params['object'])) {
		echo "<ul>";
		foreach ($params['object'] as $key=>$val) {
			echo "<li><strong>$key: </strong>$val</li>";
		}
		echo "</ul>";
	} else {
		echo '<span class="error">'.gt('No Object Found').'</span><br />';
	}
}

?>

