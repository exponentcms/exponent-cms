<?php

return array(
	'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	'question_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'user_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'ip_hash'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>64),
	'lock_expires'=>array(
		DB_FIELD_TYPE=>DB_DEF_TIMESTAMP)
);

?>