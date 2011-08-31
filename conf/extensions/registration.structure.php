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

return array(
	gt('User Registration'),
	array(
		'SITE_ALLOW_REGISTRATION'=>array(
			'title'=>gt('Allow Registration?'),
			'description'=>gt('Whether or not new users should be allowed to create accounts for themselves.'),
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_USE_EMAIL'=>array(
			'title'=>gt('Use Email Address as username'),
			'description'=>gt('Selecting this will prompt the user for their email address instead of a username.'),
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_SEND_NOTIF'=>array(
			'title'=>gt('Notification of New User'),
			'description'=>gt('Select this option if you want to send an email to a site administrator/webmaster when a new user registers on your website.'),
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_NOTIF_SUBJECT'=>array(
			'title'=>gt('Notification Subject'),
			'description'=>gt('This is the text to be put in the subject line of a new user notification email.'),
			'control'=>new textcontrol('',50)
		),
		'USER_REGISTRATION_ADMIN_EMAIL'=>array(
			'title'=>gt('Notification Email Address'),
			'description'=>gt('Enter the email address you would like new user notification to go to.'),
			'control'=>new textcontrol('',50)
		),
		'USER_REGISTRATION_SEND_WELCOME'=>array(
			'title'=>gt('Send Welcome Email to New Users'),
			'description'=>gt('Select this option if you want to send a welcome email to a new user when they create a new account on your website.'),
			'control'=>new checkboxcontrol()
		),
		'USER_REGISTRATION_WELCOME_SUBJECT'=>array(
			'title'=>gt('Welcome Subject'),
			'description'=>gt('This is the text to be put in the subject line of a welcome email sent to new users.'),
			'control'=>new textcontrol('',50)
		),
		'USER_REGISTRATION_WELCOME_MSG'=>array(
			'title'=>gt('Welcome Message'),
			'description'=>gt('This is the text to be put in the message body of a welcome email sent to new users.'),
			'control'=>new htmleditorcontrol('',15,50)
		),
	)
);

?>
