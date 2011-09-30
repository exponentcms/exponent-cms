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

return array(
	gt('SMTP Server Settings'),
	array(
		'SMTP_FROMADDRESS'=>array(
			'title'=>gt('From Address'),
			'description'=>gt('The from address to use when talking to the SMTP server.  This is important for people using ISP SMTP servers, which may restrict access to certain email addresses.'),
			'control'=>new textcontrol()
		),
		'SMTP_USE_PHP_MAIL'=>array(
			'title'=>gt('Use PHP mail() Function?'),
			'description'=>gt('If the Exponent implementation of raw SMTP does not work for you, either because of server issues or hosting configurations, check this option to use the standard mail() function provided by PHP.  NOTE: If you do so, you will not have to modify any other SMTP settings, as they will be ignored.'),
			'control'=>new checkboxcontrol()
		),
		'SMTP_SERVER'=>array(
			'title'=>gt('SMTP Server'),
			'description'=>gt('The IP address or host/domain name of the server to connect to for sending email through SMTP.'),
			'control'=>new textcontrol()
		),
		'SMTP_PORT'=>array(
			'title'=>gt('Port'),
			'description'=>gt('The port that the SMTP server is listening to for SMTP connections.  If you do not know what this is, leave it as the default of 25.'),
			'control'=>new textcontrol()
		),
		'SMTP_PROTOCOL'=>array(
			'title'=>gt('Type of Encrypted Connection'),
			'description'=>gt('Here, you can specify what type of encryption your SMTP server connection requires (if any).  Please consult your mailserver administrator for this information.'),
			'control'=>new dropdowncontrol('',array(''=>gt('No Authentication'),'ssl'=>gt('SSL'),'tls'=>gt('TLS')))
		),
		'SMTP_USERNAME'=>array(
			'title'=>gt('SMTP Username'),
			'description'=>gt('The username to use when connecting to an SMTP server that requires some form of authentication'),
			'control'=>new textcontrol()
		),
		'SMTP_PASSWORD'=>array(
			'title'=>gt('SMTP Password'),
			'description'=>gt('The password to use when connecting to an SMTP server that requires some form of authentication'),
			'control'=>new passwordcontrol()
		),
		'SMTP_DEBUGGING'=>array(
			'title'=>gt('Turn SMTP Debugging On?'),
			'description'=>gt('Turns on additional debugging output for all email system use.'),
			'control'=>new checkboxcontrol()
		),
	)
);

?>