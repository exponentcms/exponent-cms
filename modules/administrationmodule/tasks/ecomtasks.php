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

if (!defined('EXPONENT')) exit('');

// make sure ecom is installed before we display this.
if (!controllerExists('store')) return array();

$stuff = array(
	'Configure Store'=>array(
		'groupdiscounts'=>array(
			'title'=>'Group Discounts',
			'module'=>'store',
			'action'=>'groupdiscounts',
			'icon'=>ICON_RELATIVE."userperms.png"),
		'icon'=>ICON_RELATIVE."admin/ecom.png"
	),
);
return $stuff;

?>
