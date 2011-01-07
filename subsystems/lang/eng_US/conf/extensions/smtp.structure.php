<?php

return array(
	'title'=>'SMTP Server Settings',
	
	'php_mail'=>'Use PHP mail() Function?',
	'php_mail_desc'=>'If the Exponent implementation of raw SMTP does not work for you, either because of server issues or hosting configurations, check this option to use the standard mail() function provided by PHP.  NOTE: If you do so, you will not have to modify any other SMTP settings, as they will be ignored.',
	
	'server'=>'SMTP Server',
	'server_desc'=>'The IP address or host/domain name of the server to connect to for sending email through SMTP.',
	
	'port'=>'Port',
	'port_desc'=>'The port that the SMTP server is listening to for SMTP connections.  If you do not know what this is, leave it as the default of 25.',
	
	'auth'=>'Authentication Method',
	'auth_desc'=>'Here, you can specify what type of authentication your SMTP server requires (if any).  Please consult your mailserver administrator for this information.',
	'auth_none'=>'No Authentication',
	'auth_plain'=>'PLAIN',
	'auth_login'=>'LOGIN',
	
	'username'=>'SMTP Username',
	'username_desc'=>'The username to use when connecting to an SMTP server that requires some form of authentication',
	
	'password'=>'SMTP Password',
	'password_desc'=>'The password to use when connecting to an SMTP server that requires some form of authentication',
	
	'from_address'=>'From Address',
	'from_address_desc'=>'The from address to use when talking to the SMTP server.  This is important for people using ISP SMTP servers, which may restrict access to certain email addresses.',
);

?>