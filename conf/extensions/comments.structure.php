<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

return array(
	gt('User Comment Policies'),
	array(
		'COMMENTS_REQUIRE_LOGIN'=>array(
			'title'=>gt('Require Login to Post Comments'),
			'description'=>gt('Checking this option will force user to create an account and be logged in before they can post comments on your site.'),
			'control'=>new checkboxcontrol()
		),
		'COMMENTS_REQUIRE_APPROVAL'=>array(
			'title'=>gt('I Want to Approve All Comments'),
			'description'=>gt('If this option is selected, comments will not be published to your site until you approve them.'),
			'control'=>new checkboxcontrol()
		),
		'COMMENTS_REQUIRE_NOTIFICATION'=>array(
			'title'=>gt('Notify Me of New Comments'),
			'description'=>gt('An email notification will be sent to the email address specificed below to notify you of new comments on your site.'),
			'control'=>new checkboxcontrol()
		),
		'COMMENTS_NOTIFICATION_EMAIL'=>array(
			'title'=>gt('Notification Email Address(es) (Enter multiple addresses by using a comma to separate them)'),
			'description'=>gt('If you have indicated you would like to be notified of new comments on your site, please enter the email address of all the people you would like to get the notificaiton email addresses.'),
			'control'=>new textcontrol()
		),
	)
);

?>
