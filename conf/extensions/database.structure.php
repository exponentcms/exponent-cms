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
	gt('Database Options'),
	array(
		'DB_ENGINE'=>array(
			'title'=>gt('Backend Software'),
			'description'=>gt('The database server software package.'),
			'control'=>new dropdowncontrol('',exponent_database_backends())
		),
		'DB_NAME'=>array(
			'title'=>gt('Database Name'),
			'description'=>gt('The name of the database to store the site tables in.'),
			'control'=>new textcontrol()
		),
		'DB_USER'=>array(
			'title'=>gt('Username'),
			'description'=>gt('The name of the user to connect to the database server as'),
			'control'=>new textcontrol()
		),
		'DB_PASS'=>array(
			'title'=>gt('Password'),
			'description'=>gt('Password of the user above.'),
			'control'=>new passwordcontrol()
		),
		'DB_HOST'=>array(
			'title'=>gt('Server Address'),
			'description'=>gt('The domain name or IP address of the database server.  If this is a local server, use "localhost"'),
			'control'=>new textcontrol()
		),
		'DB_PORT'=>array(
			'title'=>gt('Server Port'),
			'description'=>gt('The port that the database server runs on.  For MySQL, this is 3306.'),
			'control'=>new textcontrol()
		),
		'DB_TABLE_PREFIX'=>array(
			'title'=>gt('Table Prefix'),
			'description'=>gt('A prefix to prepend to all table names.'),
			'control'=>new textcontrol()
		),
		'DB_ENCODING'=>array(
			'title'=>gt('Database Connection Encoding'),
			'description'=>gt('Sets the encoding of a connection. Supported on mySQL higher 4.1.12.'),
			'control'=>new dropdowncontrol('', exponent_config_dropdownData('DB_ENCODING'))
		)
	)
);

?>