<?php

return array(
	'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	'question_id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID),
	'answer'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>4500),
	'rank'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER),
	'vote_count'=>array(
		DB_FIELD_TYPE=>DB_DEF_INTEGER)
);

?>