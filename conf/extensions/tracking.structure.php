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

$ctl = new checkboxcontrol(false,true);
$ctl->disabled = 0;

$i18n = exponent_lang_loadFile('conf/extensions/tracking.structure.php');

return array(
	$i18n['title'],
	array(
		'ENABLE_TRACKING'=>array(
			'title'=>$i18n['enable_tracking'],
			'description'=>$i18n['tracking_desc'],
			'control'=>$ctl
		),
        'TRACKING_COOKIE_EXPIRES'=>array(
            'title'=>$i18n['cookie_expires'],
            'description'=>$i18n['cookie_expires_desc'],
            'control'=>new textcontrol()
        ),
        'TRACKING_ARCHIVE_DELAY'=>array(
            'title'=>$i18n['tracking_archive'],
            'description'=>$i18n['tracking_archive_desc'],
            'control'=>new textcontrol()
        ),
        'TRACKING_ARCHIVE_TIME'=>array(
            'title'=>$i18n['tracking_save'],
            'description'=>$i18n['tracking_save_desc'],
            'control'=>new textcontrol()
        )
	)
);

?>
