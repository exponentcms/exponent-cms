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

if (!defined('SMTP_USE_PHP_MAIL')) define('SMTP_USE_PHP_MAIL',0);
if (!defined('SMTP_SERVER')) define('SMTP_SERVER','localhost');
if (!defined('SMTP_PORT')) define('SMTP_PORT',25);
if (!defined('SMTP_AUTHTYPE')) define('SMTP_AUTHTYPE','NONE');
if (!defined('SMTP_USERNAME')) define('SMTP_USERNAME','');
if (!defined('SMTP_PASSWORD')) define('SMTP_PASSWORD','');
if (!defined('SMTP_FROMADDRESS')) define('SMTP_FROMADDRESS','website@localhost');

?>