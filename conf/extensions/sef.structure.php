<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

$ctl = new checkboxcontrol(false,true);
$ctl->disabled = 0;

$i18n = exponent_lang_loadFile('conf/extensions/sef.structure.php');

return array(
	$i18n['title'],
	array(
		'SEF_URLS'=>array(
			'title'=>$i18n['sef_urls'],
			'description'=>$i18n['sef_urls_desc'],
			'control'=>$ctl
		)
	)
);

?>
