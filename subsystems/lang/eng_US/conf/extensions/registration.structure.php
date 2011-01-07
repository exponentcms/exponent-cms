<?php

return array(
	'title'=>'User Registration',

	'allow_registration'=>'Allow Registration?',
	'allow_registration_desc'=>'Whether or not new users should be allowed to create accounts for themselves.',

	'use_captcha'=>'Use CAPTCHA Test?',
	'use_captcha_desc'=>'A CAPTCHA (Computer Automated Public Turing Test to Tell Computers and Humans Apart) is a means to prevent massive account registration.  When registering a new user account, the visitor will be required to enter a series of letters and numbers appearing in an image.  This prevents scripted bots from registering a large quantity of accounts.',
	'no_gd_support'=>'<div class="error">The server\'s version and/or configuration of PHP does not include GD support, so you will not be able to activate or use the CAPTCHA test.</div>',

	'user_registration_send_notif'=>'Notification of New User',
	'user_registration_send_notif_desc'=>'Select this option if you want to send an email to a site administrator/webmaster when a new user registers on your website.',
	'user_registration_admin_email'=>'Notification Email Address',
	'user_registration_admin_email_desc'=>'Enter the email address you would like new user notification to go to.',

	'user_registration_send_welcome'=>'Send Welcome Email to New Users',
	'user_registration_send_welcome_desc'=>'Select this option if you want to send a welcome email to a new user when they create a new account on your website.',

	'user_registration_notif_subject'=>'Notification Subject',
	'user_registration_notif_subject_desc'=>'This is the text to be put in the subject line of a new user notification email.',

	'user_registration_welcome_subject'=>'Welcome Subject',
	'user_registration_welcome_subject_desc'=>'This is the text to be put in the subject line of a welcome email sent to new users.',

	'user_registration_welcome_msg'=>'Welcome Message',
	'user_registration_welcome_msg_desc'=>'This is the text to be put in the message body of a welcome email sent to new users.',
);

?>
