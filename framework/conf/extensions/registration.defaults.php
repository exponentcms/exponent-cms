<?php

##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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

if (!defined('SITE_ALLOW_REGISTRATION')) define('SITE_ALLOW_REGISTRATION','0');
if (!defined('USER_REGISTRATION_USE_EMAIL')) define('USER_REGISTRATION_USE_EMAIL','0');
if (!defined('USER_NO_PASSWORD_CHANGE')) define('USER_NO_PASSWORD_CHANGE','0');
if (!defined('USER_REGISTRATION_SEND_NOTIF')) define('USER_REGISTRATION_SEND_NOTIF','0');
if (!defined('USER_REGISTRATION_NOTIF_SUBJECT')) define('USER_REGISTRATION_NOTIF_SUBJECT','New User Registration From Website');
if (!defined('USER_REGISTRATION_ADMIN_EMAIL')) define('USER_REGISTRATION_ADMIN_EMAIL','');
if (!defined('USER_REGISTRATION_SEND_WELCOME')) define('USER_REGISTRATION_SEND_WELCOME','0');
if (!defined('USER_REGISTRATION_WELCOME_SUBJECT')) define('USER_REGISTRATION_WELCOME_SUBJECT','Welcome to our website!');
if (!defined('USER_REGISTRATION_WELCOME_MSG')) define('USER_REGISTRATION_WELCOME_MSG','');

if (!defined('USE_LDAP')) define('USE_LDAP','0');
if (!defined('LDAP_SERVER')) define('LDAP_SERVER','not_configured');
if (!defined('LDAP_BASE_CONTEXT')) define('LDAP_BASE_CONTEXT','not_configured');
if (!defined('LDAP_BASE_DN')) define('LDAP_BASE_DN','not_configured');
if (!defined('LDAP_BIND_USER')) define('LDAP_BIND_USER','not_configured');
if (!defined('LDAP_BIND_PASS')) define('LDAP_BIND_PASS','not_configured');

?>