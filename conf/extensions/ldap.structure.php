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

$i18n = exponent_lang_loadFile('conf/extensions/ldap.structure.php');

return array(
	$i18n['title'],
	array(
		'USE_LDAP'=>array(
			'title'=>$i18n['use_ldap'],
			'description'=>$i18n['use_ldap_desc'],
			'control'=>new checkboxcontrol()
		),
		'LDAP_SERVER'=>array(
			'title'=>$i18n['ldap_server'],
			'description'=>$i18n['ldap_server_desc'],
			'control'=>new textcontrol()
		),
		'LDAP_BASE_DN'=>array(
			'title'=>$i18n['ldap_base_dn'],
			'description'=>$i18n['ldap_base_dn_desc'],
			'control'=>new textcontrol()
		),
		'LDAP_BIND_USER'=>array(
			'title'=>$i18n['ldap_bind_user'],
			'description'=>$i18n['ldap_bind_user_desc'],
			'control'=>new textcontrol()
		),
		'LDAP_BIND_PASS'=>array(
			'title'=>$i18n['ldap_bind_pass'],
			'description'=>$i18n['ldap_bind_pass_desc'],
			'control'=>new passwordcontrol()
		)
	)
);

?>
