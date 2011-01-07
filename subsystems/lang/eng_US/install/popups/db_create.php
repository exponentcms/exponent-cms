<?php

return array(
	'title'=>'Creating a New Database',
	'header'=>'Exponent supports both the MySQL database server and the PostGreSQL database server as backends.',
	
	'mysql'=>'MySQL',
	'postgres'=>'PostGreSQL',
	
	'instructions'=>'Fill out the form below and click "Go" to generate SQL statements for each supported database server.',
	
	'database'=>'Database',
	'username'=>'Username',
	'password'=>'Password',
	
	'for_mysql'=>'For MySQL...',
	
	'mysql_title'=>'MySQL Database Creation',
	'mysql_instructions'=>'If you have access to the database server, and have sufficient privileges to create databases, you can use the following SQL statements to setup the database for Exponent.  Note that you will have to fill in the form above before using these.',
	
	'postgres_title'=>'PostGreSQL Database Creation',
	'postgres_instructions'=>'Because PostGreSQL does not maintain its own set of users like MySQL (and instead relies on system users) you will have to refer to the <a href="http://www.postgresql.org/">online documentation</a> for information on creating new databases and assigning user permissions.',
	
	'create_db'=>'Create the Database',
	'create_privs'=>'Grant Database Rights',
	
	'fill_out'=>'fill in the above form and click "Go" to generate SQL',
);

?>