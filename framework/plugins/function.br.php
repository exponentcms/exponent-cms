<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * Smarty {br} function plugin
 *
 * Type:     function<br>
 * Name:     br<br>
 * Purpose:  create an appropriate break depending on xhtml setting
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_br($params,&$smarty) {
	if (XHTML==1){
		echo "<br />";
	} else {
		echo "<br>";
	}
}

?>

