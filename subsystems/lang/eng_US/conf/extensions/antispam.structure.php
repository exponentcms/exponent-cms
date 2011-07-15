<?php

return array(
	'title'=>'Anti-Spam Measures',
	
	'use_captcha'=>'Use CAPTCHA Test?',
	'use_captcha_desc'=>'A CAPTCHA (Computer Automated Public Turing Test to Tell Computers and Humans Apart) is a means to prevent massive account registration.  When registering a new user account, the visitor will be required to enter a series of letters and numbers appearing in an image.  This prevents scripted bots from registering a large quantity of accounts.',
	'no_gd_support'=>'<div class="error">The server\'s version and/or configuration of PHP does not include GD support, so you will not be able to activate or use the CAPTCHA test.</div>',
	'antispam_users_skip'=>'Skip using Anti-Spam measures for Logged-In Users?',
	'antispam_users_skip_desc'=>'If a user is logged-in, do not display anti-spam control.<br />',

	'antispam_control'=>'Choose an Anti-Spam Control',
	'antispam_control_desc'=>'Spam on forms, like comments and contact forms can be a big issue for admins. If you would like to try to combat spam on your site, choose an anti-spam control to use.<br />',
    'recaptcha_theme'=>'reCAPTCHA Theme.',
    'recaptcha_pub_key'=>'reCAPTCHA Public Key.',
    'recaptcha_pub_key_desc'=>'If you are using reCAPTCHA please enter the public key here.',
    'recaptcha_private_key'=>'reCAPTCHA Private Key.',
    'recaptcha_private_key_desc'=>'If you are using reCAPTCHA please enter the private key here.',
);

?>
