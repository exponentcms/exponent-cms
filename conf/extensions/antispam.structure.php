<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

// TYPES OF ANTISPAM CONTROLS... CURRENTLY ONLY ReCAPTCHA
$as_types = array(
    '0'=>'-- Please Select an Anti-Spam Control --',
    "recaptcha"=>'reCAPTCHA'
);
//THEMES FOR RECAPTCHA
$as_themes = array(
    "red"=>'DEFAULT RED',
	"white"=>'White',
	"blackglass"=>'Black Glass',
	"clean"=>'Clean (very generic)',
	//"custom"=>'Custom' --> THIS MAY BE COOL TO ADD LATER...
);

$i18n = exponent_lang_loadFile('conf/extensions/antispam.structure.php');

return array(
	$i18n['title'],
	array(
		'ANTI_SPAM_CONTROL'=>array(
			'title'=>$i18n['antispam_control'],
			'description'=>$i18n['antispam_control_desc'],
			'control'=>new dropdowncontrol('',$as_types)
		),
		'RECAPTCHA_THEME'=>array(
			'title'=>$i18n['recaptcha_theme'],
			'description'=>$i18n['recaptcha_theme'],
			'control'=>new dropdowncontrol('',$as_themes)
		),
		'RECAPTCHA_PUB_KEY'=>array(
			'title'=>$i18n['recaptcha_pub_key'],
			'description'=>$i18n['recaptcha_pub_key_desc'],
			'control'=>new textcontrol()
		),
		'RECAPTCHA_PRIVATE_KEY'=>array(
			'title'=>$i18n['recaptcha_private_key'],
			'description'=>$i18n['recaptcha_private_key_desc'],
			'control'=>new textcontrol()
		),
		
	)
);

?>
