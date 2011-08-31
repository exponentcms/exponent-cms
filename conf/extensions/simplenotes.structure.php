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

return array(
	gt('Simple Admin notes'),
	array(
		'SIMPLENOTE_REQUIRE_LOGIN'=>array(
			'title'=>gt('Require Login to Add Notes'),
			'description'=>gt('Checking this option will force user to create an account and be logged in before they can addn notes. Theoreticall this will rarely be an issue as simple notes are generall added to edit forms (products for example) and thus the user would need to be logged in anyway.'),
			'control'=>new checkboxcontrol()
		),
		'SIMPLENOTE_REQUIRE_APPROVAL'=>array(
			'title'=>gt('I Want to Approve All Notes'),
			'description'=>gt('If this option is selected, notes will not be displayed until you approve them.'),
			'control'=>new checkboxcontrol()
		),
		'SIMPLENOTE_REQUIRE_NOTIFICATION'=>array(
			'title'=>gt('Notify Me of New Notes'),
			'description'=>gt('An email notification will be sent to the email address specificed below to notify you of new notes.'),
			'control'=>new checkboxcontrol()
		),
		'SIMPLENOTE_NOTIFICATION_EMAIL'=>array(
			'title'=>gt('Notification Email Address(es) (Enter multiple addresses by using a comma to separate them)'),
			'description'=>gt('If you have indicated you would like to be notified of new notes, please enter the email address of all the people you would like to get the notificaiton email addresses.'),
			'control'=>new textcontrol()
		),
	)
);

?>
