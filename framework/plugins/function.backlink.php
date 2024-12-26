<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * Smarty {backlink} function plugin
 *
 * Type:     function<br>
 * Name:     backlink<br>
 * Purpose:  create a back link
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_backlink($params,&$smarty) {
//	global $history;
//	$d=$params['distance']?$params['distance']+1:2;
//	echo makelink($history->history[$history->history['lasts']['type']][count($history->history[$history->history['lasts']['type']])-$d]['params']);
	$d=$params['distance']?$params['distance']:1;
	echo makeLink(expHistory::getBack($d));
}

?>

