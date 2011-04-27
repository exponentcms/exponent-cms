<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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

if (!defined('SITE_TITLE')) define('SITE_TITLE','My New Exponent Site');
//if (!defined('SITE_ALLOW_REGISTRATION')) define('SITE_ALLOW_REGISTRATION',1);
//if (!defined('SITE_USE_CAPTCHA')) define('SITE_USE_CAPTCHA',1);

if (!defined('SITE_404_TITLE')) define('SITE_404_TITLE','Page Not Found');
if (!defined('SITE_404_HTML')) define('SITE_404_HTML',exponent_unhtmlentities('The page you were looking for wasn&apos;t found.  It may have been moved or deleted.'));
// SITE_403_HTML will be set by sessions, to be either the timeout html, or forbidden
if (!defined('SITE_403_REAL_HTML')) define('SITE_403_REAL_HTML',exponent_unhtmlentities('<h3>Authorization Failed</h3>You are not allowed to perform this operation.'));

if (!defined('SITE_KEYWORDS')) define('SITE_KEYWORDS','');
if (!defined('SITE_DESCRIPTION')) define('SITE_DESCRIPTION','');
if (!defined('SITE_DEFAULT_SECTION')) define('SITE_DEFAULT_SECTION',1);

if (!defined('SAVE_SEARCH_QUERIES')) define('SAVE_SEARCH_QUERIES',1);
if (!defined('SITE_WYSIWYG_EDITOR')) define('SITE_WYSIWYG_EDITOR','ckeditor');

if (!defined('SESSION_TIMEOUT_ENABLE')) define('SESSION_TIMEOUT_ENABLE',1);
if (!defined('SESSION_TIMEOUT')) define('SESSION_TIMEOUT',7200);
if (!defined('SESSION_TIMEOUT_HTML')) define('SESSION_TIMEOUT_HTML',exponent_unhtmlentities('<h3>Expired Login Session</h3>Your session has expired, because you were idle too long.  You will have to log back into the system to continue what you were doing.'));

if (!defined('ENABLE_SSL')) define('ENABLE_SSL',0);
if (!defined('SSL_URL')) define('SSL_URL','https://my.domain/');
if (!defined('NONSSL_URL')) define('NONSSL_URL','http://my.domain/');

if (!defined('FILE_DEFAULT_MODE_STR')) define('FILE_DEFAULT_MODE_STR','0666');
if (!defined('FILE_DEFAULT_MODE')) define('FILE_DEFAULT_MODE',octdec(FILE_DEFAULT_MODE_STR+0));
if (!defined('DIR_DEFAULT_MODE_STR')) define('DIR_DEFAULT_MODE_STR','0770');
if (!defined('DIR_DEFAULT_MODE')) define('DIR_DEFAULT_MODE',octdec(DIR_DEFAULT_MODE_STR+0));

if (!defined('USE_LANG')) define('USE_LANG','eng_US');
if (!defined('ENABLE_WORKFLOW')) define('ENABLE_WORKFLOW',0);
if (!defined('WORKFLOW_REVISION_LIMIT')) define('WORKFLOW_REVIOSION_LIMIT',0);
if (!defined('ORGANIZATION_NAME')) define('ORGANIZATION_NAME',URL_BASE);
if (!defined('HELP_ACTIVE')) define('HELP_ACTIVE',0);
if (!defined('HELP_URL')) define('HELP_URL','http://docs.exponentcms.org/');

?>
