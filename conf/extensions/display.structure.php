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

$languages = expLang::langList();
ksort($langs);

/*
echo "<xmp>";
print_r($languages);
print_r($themes);
echo "</xmp>";
*/
return array(
	gt('Display Settings'),
	array(
//		'DISPLAY_LANGUAGE'=>array(
		'LANGUAGE'=>array(
			'title'=>gt('Language'),
			'description'=>gt('The language to use.'),
			'control'=>new dropdowncontrol(null,$languages)
		),
		'SLINGBAR_TOP'=>array(
			'title'=>gt('Slingbar at Top'),
			'description'=>gt('Should the slingbar display at the top of the page? Unchecking will place the slingbar at the bottom of the page.'),
			'control'=>new checkboxcontrol(false,true)
		),
		'DISPLAY_THEME_REAL'=>array(
			'title'=>gt('Theme'),
			'description'=>gt('The current theme layout'),
			'control'=>new dropdowncontrol(null,$themes)
		),
		'DISPLAY_ATTRIBUTION'=>array(
			'title'=>gt('Attribution'),
			'description'=>gt('How credit is given to authors for their posts.'),
			'control'=>new dropdowncontrol(null,array('firstlast'=>'John Doe','lastfirst'=>'Doe, John','first'=>'John','username'=>'jdoe'))
		),
		'DISPLAY_DATETIME_FORMAT'=>array(
			'title'=>gt('Date and Time Format'),
			'description'=>gt('Default system-wide date format, displaying both date and time.'),
			'control'=>new dropdowncontrol(null,expSettings::dropdownData('datetime_format'))
		),
		'DISPLAY_DATE_FORMAT'=>array(
			'title'=>gt('Date Format'),
			'description'=>gt('Default system-wide date format, displaying date only.'),
			'control'=>new dropdowncontrol(null,expSettings::dropdownData('date_format'))
		),
		'DISPLAY_TIME_FORMAT'=>array(
			'title'=>gt('Time Format'),
			'description'=>gt('Default system-wide date format, displaying time only.'),
			'control'=>new dropdowncontrol(null,expSettings::dropdownData('time_format'))
		),
		'DISPLAY_START_OF_WEEK'=>array(
			'title'=>gt('Start of Week'),
			'description'=>gt('Default day to start the week.'),
			'control'=>new dropdowncontrol(null,expSettings::dropdownData('start_of_week'))
		),
		'DISPLAY_DEFAULT_TIMEZONE'=>array(
			'title'=>gt('Default timezone for this site.'),
			'description'=>gt('Select the default timezone for this site.  CAUTION:  This may break calendars and other features that use date functions if you change this after entering data.  Must be in a format shown here:  <a href="http://www.php.net/manual/en/timezones.php" target="_blank">http://www.php.net/manual/en/timezones.php</a>'),
			'control'=>new textcontrol()
		)
	)
);

?>
