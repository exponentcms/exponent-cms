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

$i18n = exponent_lang_loadFile('conf/extensions/smtp.structure.php');

return array(
	$i18n['title'],
	array(
		'SMTP_USE_PHP_MAIL'=>array(
			'title'=>$i18n['php_mail'],
			'description'=>$i18n['php_mail_desc'],
			'control'=>new checkboxcontrol()
		),
		'SMTP_SERVER'=>array(
			'title'=>$i18n['server'],
			'description'=>$i18n['server_desc'],
			'control'=>new textcontrol()
		),
		'SMTP_PORT'=>array(
			'title'=>$i18n['port'],
			'description'=>$i18n['port_desc'],
			'control'=>new textcontrol()
		),
		'SMTP_AUTHTYPE'=>array(
			'title'=>$i18n['auth'],
			'description'=>$i18n['auth_desc'],
			'control'=>new dropdowncontrol('',array('NONE'=>$i18n['auth_none'],'LOGIN'=>$i18n['auth_login'],'PLAIN'=>$i18n['auth_plain']))
		),
		'SMTP_USERNAME'=>array(
			'title'=>$i18n['username'],
			'description'=>$i18n['username_desc'],
			'control'=>new textcontrol()
		),
		'SMTP_PASSWORD'=>array(
			'title'=>$i18n['password'],
			'description'=>$i18n['password'],
			'control'=>new passwordcontrol()
		),
		'SMTP_FROMADDRESS'=>array(
			'title'=>$i18n['from_address'],
			'description'=>$i18n['from_address_desc'],
			'control'=>new textcontrol()
		),
	)
);

?>