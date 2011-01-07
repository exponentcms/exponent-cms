<?php

##################################################
#
# Copyright (c) 2004-2009 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

$i18n = exponent_lang_loadFile('conf/extensions/comments.structure.php');

return array(
	$i18n['title'],
	array(
		'COMMENTS_REQUIRE_LOGIN'=>array(
			'title'=>$i18n['comments_require_login'],
			'description'=>$i18n['comments_require_login_desc'],
			'control'=>new checkboxcontrol()
		),
		'COMMENTS_REQUIRE_APPROVAL'=>array(
			'title'=>$i18n['comments_require_approval'],
			'description'=>$i18n['comments_require_approval_desc'],
			'control'=>new checkboxcontrol()
		),
		'COMMENTS_REQUIRE_NOTIFICATION'=>array(
			'title'=>$i18n['comments_require_notification'],
			'description'=>$i18n['comments_require_notification_desc'],
			'control'=>new checkboxcontrol()
		),
		'COMMENTS_NOTIFICATION_EMAIL'=>array(
			'title'=>$i18n['comments_require_notification_email'],
			'description'=>$i18n['comments_require_notification_email_desc'],
			'control'=>new textcontrol()
		),
	)
);

?>
