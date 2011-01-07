<?php

return array(
	'id'=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	'question'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>255),
	'location_data'=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>250),
	'is_active'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'open_results'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
	'open_voting'=>array(
		DB_FIELD_TYPE=>DB_DEF_BOOLEAN)
);

?>