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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {clear} function plugin
 *
 * Type:     function<br>
 * Name:     clear<br>
 * Purpose:  clear formatting
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_clear($params,&$smarty) {
	echo '<div style="clear:both"></div>';
}

?>

