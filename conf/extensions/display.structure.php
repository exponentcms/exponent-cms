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

$themes = array();
if (is_readable(BASE.'themes')) {
	$theme_dh = opendir(BASE.'themes');
	while (($theme_file = readdir($theme_dh)) !== false) {
		if (is_readable(BASE.'themes/'.$theme_file.'/class.php')) {
			// Need to avoid the duplicate theme problem.
			if (!class_exists($theme_file)) {
				include_once(BASE.'themes/'.$theme_file.'/class.php');
			}
			
			if (class_exists($theme_file)) {
				// Need to avoid instantiating non-existent classes.
				$t = new $theme_file();
				$themes[$theme_file] = $t->name();
			}
		}
	}
}
uasort($themes,'strnatcmp');

/*
$languages = array();
if (is_readable(BASE.'subsystems/lang')) {
	$lang_dir = opendir(BASE.'subsystems/lang');
	while (($lang_file = readdir($lang_dir)) !== false) {
		$lang_file = realpath(BASE.'subsystems/lang/'.$lang_file);
			if (is_readable($lang_file) && is_file($lang_file)) {
				$thislang = include($lang_file);
				$languages[$thislang['locale']] = $thislang['name'];				
			}
	}
}

echo "<xmp>";
print_r($languages);
print_r($themes);
echo "</xmp>";
*/
$i18n = exponent_lang_loadFile('conf/extensions/display.structure.php');

return array(
	$i18n['title'],
	array(
		/*'DISPLAY_LANGUAGE'=>array(
			'title'=>$i18n['language'],
			'description'=>$i18n['language_desc'],
			'control'=>new dropdowncontrol(null,$languages)
		),
		*/		
		'SLINGBAR_TOP'=>array(
			'title'=>$i18n['slingbar_top'],
			'description'=>$i18n['slingbar_top_desc'],
			'control'=>new checkboxcontrol(false,true)
		),
		'DISPLAY_THEME_REAL'=>array(
			'title'=>$i18n['theme_real'],
			'description'=>$i18n['theme_real_desc'],
			'control'=>new dropdowncontrol(null,$themes)
		),
		'DISPLAY_ATTRIBUTION'=>array(
			'title'=>$i18n['attribution'],
			'description'=>$i18n['attribution_desc'],
			'control'=>new dropdowncontrol(null,array('firstlast'=>'John Doe','lastfirst'=>'Doe, John','first'=>'John','username'=>'jdoe'))
		),
		'DISPLAY_DATETIME_FORMAT'=>array(
			'title'=>$i18n['datetime_format'],
			'description'=>$i18n['datetime_format_desc'],
			'control'=>new dropdowncontrol(null,exponent_config_dropdownData('datetime_format'))
		),
		'DISPLAY_DATE_FORMAT'=>array(
			'title'=>$i18n['date_format'],
			'description'=>$i18n['date_format_desc'],
			'control'=>new dropdowncontrol(null,exponent_config_dropdownData('date_format'))
		),
		'DISPLAY_TIME_FORMAT'=>array(
			'title'=>$i18n['time_format'],
			'description'=>$i18n['time_format_desc'],
			'control'=>new dropdowncontrol(null,exponent_config_dropdownData('time_format'))
		),
		'DISPLAY_START_OF_WEEK'=>array(
			'title'=>$i18n['start_of_week'],
			'description'=>$i18n['start_of_week_desc'],
			'control'=>new dropdowncontrol(null,exponent_config_dropdownData('start_of_week'))
		),
		'DISPLAY_DEFAULT_TIMEZONE'=>array(
			'title'=>$i18n['default_timezone'],
			'description'=>$i18n['default_timezone_desc'],
			'control'=>new textcontrol()
		)
	)
);

?>
