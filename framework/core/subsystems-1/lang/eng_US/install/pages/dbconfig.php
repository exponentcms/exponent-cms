<?php

return array(
	'subtitle'=>'Database Configuration',
	
	'in_doubt'=>'If in doubt, contact your system administrator or hosting provider.',
	'more_info'=>'More Information',
	
	'server_info'=>'Server Information',
	'backend'=>'Backend',
	'backend_desc'=>'Select which database server software package your web server is running.  If the software is not listed, it is not supported by Exponent.',
	
	'address'=>'Address',
	'address_desc'=>'If your database server software runs on a different physical machine than the web server, enter the address of the database server machine.  Either an IP address (like 1.2.3.4) or an internet domain name (such as example.com) will work.<br /><br />If your database server software runs on the same machine as the web server, use the default setting, "localhost".',
	
	'port'=>'Port',
	'port_desc'=>'If you are using a database server that supports TCP or other network connection protocols, and that database software runs on a different physical machine than the web server, enter the connection port.<br /><br />If you entered "localhost" in the Address field, you should leave this as the default setting.',
	
	'database_info'=>'Database Information',
	'dbname'=>'Database Name',
	'dbname_desc'=>'This is the real name of the database, according to the database server.  Consult your system administrator or hosting provider if you are unsure and did not set the database up yourself.',
	
	'username'=>'Username',
	'username_desc'=>'All database server software supported by Exponent require some sort of authentication.  Enter the name of the user account to use for logging into the database server.',
	'username_desc2'=>'Make sure that this user has the proper database user privileges.',
	
	'password'=>'Password',
	'password_desc'=>'Enter the password for the username you entered above.  The password will <b>not</b> be obscured, because it cannot be obscured in the configuration file.  The Exponent developers urge you to use a completely new password, unlike any of your others, for security reasons.',
	
	'prefix'=>'Table Prefix',
	'prefix_desc'=>'A table prefix helps Exponent differentiate tables for this site from other tables that may already exist (or eventually be created by other scripts).  If you are using an existing database, you may want to change this.',
	'prefix_note'=>'<b>Note:</b> A table prefix may only contain numbers and letters.  Spaces and symbols (including "_") are not allowed.  An underscore will be added for you, by Exponent.',
	
	'default_content'=>'Default Example Content',
	'install'=>'Install Example Content',
	'install_desc'=>'To help you understand how Exponent works, and how everything fits together, we suggest that you install the packaged example content.  If you are new to Exponent, you are highly encouraged to do so.',

	'enable_sef'=>'Enable Friendly URLs',
	'sef'=>'Friendly URLs',
	'sef_desc'=>'Check this option to turn on friendly (SEF) URLs.',	

	'verify'=>'Verify Configuration',
	'verify_desc'=>'After you are satisfied that the information you have entered is correct, click the "Test Settings" button, below.  The Exponent Install Wizard will then perform some preliminary tests to ensure that the configuration is valid.',
	'test_settings'=>'Test Settings',

	'DB_ENCODING'=>'Database Encoding',
	'DB_ENCODING_desc'=>'Dont change that unless you know what you are doing. This setting is currently only respected on MySQL 4.1.2+'
);

?>
