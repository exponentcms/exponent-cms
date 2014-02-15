<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

if (!defined('SITE_TITLE')) define('SITE_TITLE','My New Exponent Site');
if (!defined('SITE_HEADER')) define('SITE_HEADER','My New Exponent Header');
if (!defined('ORGANIZATION_NAME')) define('ORGANIZATION_NAME',URL_BASE);
if (!defined('SITE_KEYWORDS')) define('SITE_KEYWORDS','');
if (!defined('SITE_DESCRIPTION')) define('SITE_DESCRIPTION','');
if (!defined('SITE_DEFAULT_SECTION')) define('SITE_DEFAULT_SECTION','1');

if (!defined('SITE_404_TITLE')) define('SITE_404_TITLE','Page Not Found');
if (!defined('SITE_404_HTML')) define('SITE_404_HTML',exponent_unhtmlentities('The page you were looking for wasn&apos;t found.  It may have been moved or deleted.'));
if (!defined('SITE_403_REAL_HTML')) define('SITE_403_REAL_HTML',exponent_unhtmlentities('<h3>Authorization Failed</h3>You are not allowed to perform this operation.'));

if (!defined('ADVERTISE_RSS')) define('ADVERTISE_RSS','0');

if (!defined('SITE_WYSIWYG_EDITOR')) define('SITE_WYSIWYG_EDITOR','ckeditor');

if (!defined('SESSION_TIMEOUT_ENABLE')) define('SESSION_TIMEOUT_ENABLE','1');
if (!defined('SESSION_TIMEOUT')) define('SESSION_TIMEOUT','7200');
if (!defined('SESSION_TIMEOUT_HTML')) define('SESSION_TIMEOUT_HTML',exponent_unhtmlentities('<h3>Expired Login Session</h3>Your session has expired, because you were idle too long.  You will have to log back into the system to continue what you were doing.'));

if (!defined('ENABLE_SSL')) define('ENABLE_SSL','0');
if (!defined('DISABLE_SSL_WARNING')) define('DISABLE_SSL_WARNING','0');

if (!defined('DISABLE_PRIVACY')) define('DISABLE_PRIVACY','1');

if (!defined('FILE_DEFAULT_MODE_STR')) define('FILE_DEFAULT_MODE_STR','0666');
if (!defined('DIR_DEFAULT_MODE_STR')) define('DIR_DEFAULT_MODE_STR','0770');

if (!defined('HELP_ACTIVE')) define('HELP_ACTIVE','1');
if (!defined('HELP_URL')) define('HELP_URL','http://docs.exponentcms.org/');

if (!defined('FORCE_ECOM')) define('FORCE_ECOM','0');
if (!defined('ECOM_CURRENCY')) define('ECOM_CURRENCY','USD');
if (!defined('SKIP_VERSION_CHECK')) define('SKIP_VERSION_CHECK','0');

if (!defined('LESS_COMPILER')) define('LESS_COMPILER','less.php');

?>