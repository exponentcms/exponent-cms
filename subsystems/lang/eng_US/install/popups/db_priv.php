<?php

return array(
	'title'=>'Database User Privileges',
	'header'=>'When Exponent connects to the database, it needs to be able to run the following types of queries:',
	
	'create'=>'CREATE TABLE',
	'create_desc'=>'These queries create new table structures inside the database.  Exponent needs this when you install it for the first time.  CREATE TABLE queries are also run after new modules are uploaded to the site.',
	'alter'=>'ALTER TABLE',
	'alter_desc'=>'If you upgrade any module in Exponent, these queries will be run to change table structures in the database.',
	'drop'=>'DROP TABLE',
	'drop_desc'=>'These queries are executed on the database whenever an administrator trims it to remove tables that are no longer used.',
	'select'=>'SELECT',
	'select_desc'=>'Queries of this type are very important to the basic operation of Exponent.  All data stored in the database is read back through the use of SELECT queries.',
	'insert'=>'INSERT',
	'insert_desc'=>'Whenever new content is added to the site, new permissions are assigned, users and/or groups are created and configuration data is saved, Exponent runs INSERT queries.',
	'update'=>'UPDATE',
	'update_desc'=>'When content or configurations are updated, Exponent modifies the data in its tables by issuing UPDATE queries.',
	'delete'=>'DELETE',
	'delete_desc'=>'These queries remove content and configuration from the tables in the site database.  They are also executed whenever users and groups are removed, and permissions are revoked.',
	
);

?>