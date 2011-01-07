<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

$i18n = exponent_lang_loadFile('conf/extensions/registration.structure.php');

$stuff = array(
	$i18n['title'],
	array(
		'SITE_ALLOW_REGISTRATION'=>array(
			'title'=>$i18n['allow_registration'],
			'description'=>$i18n['allow_registration_desc'],
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_USE_EMAIL'=>array(
			'title'=>exponent_lang_getText('Use Email Address as username'),
			'description'=>exponent_lang_getText('Selecting this will prompt the user for their email address instead of a username.'),
			'control'=>new checkboxcontrol()
		),
		'SITE_USE_CAPTCHA'=>array(
			'title'=>$i18n['use_captcha'],
			'description'=>$i18n['use_captcha_desc'],
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_SEND_NOTIF'=>array(
			'title'=>$i18n['user_registration_send_notif'],
			'description'=>$i18n['user_registration_send_notif_desc'],
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_NOTIF_SUBJECT'=>array(
			'title'=>$i18n['user_registration_notif_subject'],
			'description'=>$i18n['user_registration_notif_subject_desc'],
			'control'=>new textcontrol('',50)
		),
		'USER_REGISTRATION_ADMIN_EMAIL'=>array(
			'title'=>$i18n['user_registration_admin_email'],
			'description'=>$i18n['user_registration_admin_email_desc'],
			'control'=>new textcontrol('',50)
		),
		'USER_REGISTRATION_SEND_WELCOME'=>array(
			'title'=>$i18n['user_registration_send_welcome'],
			'description'=>$i18n['user_registration_send_welcome_desc'],
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_WELCOME_SUBJECT'=>array(
			'title'=>$i18n['user_registration_welcome_subject'],
			'description'=>$i18n['user_registration_welcome_subject_desc'],
			'control'=>new textcontrol('',50)
		),
		'USER_REGISTRATION_WELCOME_MSG'=>array(
			'title'=>$i18n['user_registration_welcome_msg'],
			'description'=>$i18n['user_registration_welcome_msg_desc'],
			'control'=>new htmleditorcontrol('',15,50)
		),
	)
);

$info = gd_info();
if (!EXPONENT_HAS_GD) {
	$stuff[1]['SITE_USE_CAPTCHA']['description'] = $i18n['use_captcha_desc'].'<br /><br />'.$i18n['no_gd_support'];
	$stuff[1]['SITE_USE_CAPTCHA']['control']->disabled = true;
}

return $stuff;

?>
