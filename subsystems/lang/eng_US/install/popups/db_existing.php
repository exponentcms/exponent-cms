<?php

return array(
	'title'=>'Using an Existing Database',
	'p1'=>'A pre-existing database can be used to store the content of your website, however a few issues must be dealt with.',
	'p2'=>'Exponent needs its own set of tables within a pre-existing database in order to function properly.  This can be accomplished by specifying a new table prefix.',
	'p3'=>'The table prefix is used to make each table\'s name in the database unique.  It is prepended to the name of each table.  This means that two Exponent sites can use the database "db" if one has a table prefix of "exponent" and the other uses "cms".',
	'p4'=>'Exponent will prepend your table prefix with an underscore.  This improves database readability, and helps with troubleshooting.',
);

?>