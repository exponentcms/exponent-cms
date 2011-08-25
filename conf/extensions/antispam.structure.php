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

return array(
	gt('Anti-Spam Measures'),
	array(
		'SITE_USE_ANTI_SPAM'=>array(
			'title'=>gt('Use CAPTCHA Test?'),
			'description'=>gt('A CAPTCHA (Computer Automated Public Turing Test to Tell Computers and Humans Apart) is a means to prevent massive account registration.  When registering a new user account, the visitor will be required to enter a series of letters and numbers appearing in an image.  This prevents scripted bots from registering a large quantity of accounts.'),
			'control'=>new checkboxcontrol()
		),
		'ANTI_SPAM_USERS_SKIP'=>array(
			'title'=>gt('Skip using Anti-Spam measures for Logged-In Users?'),
			'description'=>gt('If a user is logged-in, do not display anti-spam control.<br />'),
			'control'=>new checkboxcontrol()
		),
		'ANTI_SPAM_CONTROL'=>array(
			'title'=>gt('Choose an Anti-Spam Control'),
			'description'=>gt('Spam on forms, like comments and contact forms can be a big issue for admins. If you would like to try to combat spam on your site, choose an anti-spam control to use.<br />'),
			'control'=>new dropdowncontrol('',$as_types)
		),
		'RECAPTCHA_THEME'=>array(
			'title'=>gt('reCAPTCHA Theme.'),
			'description'=>gt('reCAPTCHA Theme.'),
			'control'=>new dropdowncontrol('',$as_themes)
		),
		'RECAPTCHA_PUB_KEY'=>array(
			'title'=>gt('reCAPTCHA Public Key.'),
			'description'=>gt('If you are using reCAPTCHA please enter the public key here.'),
			'control'=>new textcontrol()
		),
		'RECAPTCHA_PRIVATE_KEY'=>array(
			'title'=>gt('reCAPTCHA Private Key.'),
			'description'=>gt('If you are using reCAPTCHA please enter the private key here.'),
			'control'=>new textcontrol()
		),
		
	)
);

//$info = gd_info();
//if (!EXPONENT_HAS_GD) {
//	$stuff[1]['SITE_USE_ANTI_SPAM']['description'] = gt('A CAPTCHA (Computer Automated Public Turing Test to Tell Computers and Humans Apart) is a means to prevent massive account registration.  When registering a new user account, the visitor will be required to enter a series of letters and numbers appearing in an image.  This prevents scripted bots from registering a large quantity of accounts.').'<br /><br />'.
//	                                                 gt('<div class="error">The server\'s version and/or configuration of PHP does not include GD support, so you will not be able to activate or use the CAPTCHA test.</div>');
//	$stuff[1]['SITE_USE_ANTI_SPAM']['control']->disabled = true;
//}
//
//return $stuff;

?>
