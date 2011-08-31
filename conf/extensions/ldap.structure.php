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
	gt('LDAP Authentication Options'),
	array(
		'USE_LDAP'=>array(
			'title'=>gt('Turn on LDAP Authentication'),
			'description'=>gt('Checking this option will cause Exponent to try to authenticate to the ldap server listed below.'),
			'control'=>new checkboxcontrol()
		),
		'LDAP_SERVER'=>array(
			'title'=>gt('LDAP Server'),
			'description'=>gt('Enter the hostname or IP of the LDAP server.'),
			'control'=>new textcontrol()
		),
		'LDAP_BASE_DN'=>array(
			'title'=>gt('Base DN'),
			'description'=>gt('Enter the Base context for this LDAP connection.'),
			'control'=>new textcontrol()
		),
		'LDAP_BIND_USER'=>array(
			'title'=>gt('LDAP Bind User'),
			'description'=>gt('The username or context for the binding to the LDAP Server to perform administration tasks(This currently doesn\'t do anything.)'),
			'control'=>new textcontrol()
		),
		'LDAP_BIND_PASS'=>array(
			'title'=>gt('LDAP Password'),
			'description'=>gt('Enter the password for the username/context listed above.'),
			'control'=>new passwordcontrol()
		)
	)
);

?>
