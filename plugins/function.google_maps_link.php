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
