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


$i18n = exponent_lang_loadFile('conf/extensions/minify.structure.php');

return array(
	$i18n['title'],
	array(
		'MINIFY_MAXAGE'=>array(
			'title'=>$i18n['minify_maxage'],
			'description'=>$i18n['minify_maxage_desc'],
			'control'=>new textcontrol()
		),
		'MINIFY_MAX_FILES'=>array(
			'title'=>$i18n['minify_max_files'],
			'description'=>$i18n['minify_max_files_desc'],
			'control'=>new textcontrol()
		),
		'MINIFY_URL_LENGTH'=>array(
			'title'=>$i18n['minify_url_length'],
			'description'=>$i18n['minify_url_length_desc'],
			'control'=>new textcontrol()
		),
		'MINIFY_ERROR_LOGGER'=>array(
			'title'=>$i18n['minify_error_logger'],
			'description'=>$i18n['minify_error_logger_desc'],
			'control'=>new checkboxcontrol()
		)
		
	)
);

?>