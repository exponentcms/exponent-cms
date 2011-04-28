<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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

$i18n = exponent_lang_loadFile('conf/extensions/maint.structure.php');

return array(
	$i18n['title'],
	array(
		'MAINTENANCE_MODE'=>array(
			'title'=>$i18n['maint_mode'],
			'description'=>$i18n['maint_mode_desc'],
			'control'=>new checkboxcontrol(false,true)
		),
		'MAINTENANCE_MSG_HTML'=>array(
			'title'=>$i18n['maint_msg'],
			'description'=>$i18n['maint_msg_desc'],
			'control'=>new htmleditorcontrol()
		),
		'DEVELOPMENT'=>array(
			'title'=>exponent_lang_getText('Enable Error Reporting'),
                        'description'=>exponent_lang_getText('This option enables error reporting. This is useful for developement, but should be turned off for a live site.'),
                        'control'=>new checkboxcontrol(false,true)
		),
	)
);

?>
