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
 * Smarty {last_url_of} function plugin
 *
 * Type:     function<br>
 * Name:     last_url_of<br>
 * Purpose:  return the last url of type passed
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_last_url_of($params,&$smarty) {
	global $history;
	echo $history->lastUrl($params['type']);
}

?>

