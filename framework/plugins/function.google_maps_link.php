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
 * Smarty {google_maps_link} function plugin
 *
 * Type:     function<br>
 * Name:     google_maps_link<br>
 * Purpose:  create a google maps link
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_google_maps_link($params,&$smarty) {
	$link = '';

	if (!empty($params['street'])) $link .= addPlus($link).urlencode(trim($params['street'])); 
	if (!empty($params['city'])) $link .= addPlus($link).urlencode(trim($params['city'])); 
	if (!empty($params['state'])) $link .= addPlus($link).urlencode(trim($params['state'])); 
	if (!empty($params['zip'])) $link .= addPlus($link).urlencode(trim($params['zip'])); 

	echo 'http://maps.google.com/maps?f=q&hl=en&geocode=&q='.$link;

}

function addPlus($link) {
	return empty($link) ? '' : '+';
}

?>
