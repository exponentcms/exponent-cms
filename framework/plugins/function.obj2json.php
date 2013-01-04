<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * Smarty {obj2json} function plugin
 *
 * Type:     function<br>
 * Name:     obj2json<br>
 * Purpose:  convert a php object to javascript via json
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_obj2json($params,&$smarty) {
	echo json_encode($params['obj']);
}

?>

