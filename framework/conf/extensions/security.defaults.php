<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

if (!defined('SESSION_TIMEOUT_ENABLE')) define('SESSION_TIMEOUT_ENABLE','1');
if (!defined('SESSION_TIMEOUT')) define('SESSION_TIMEOUT','7200');
if (!defined('FILE_DEFAULT_MODE_STR')) define('FILE_DEFAULT_MODE_STR','0644');
if (!defined('DIR_DEFAULT_MODE_STR')) define('DIR_DEFAULT_MODE_STR','0755');
if (!defined('ENABLE_SSL')) define('ENABLE_SSL','0');
if (!defined('DISABLE_PRIVACY')) define('DISABLE_PRIVACY','1');
if (!defined('USE_XMLRPC')) define('USE_XMLRPC','0');
if (!defined('NO_XMLRPC_DESC')) define('NO_XMLRPC_DESC','0');

if (!defined('NEW_PASSWORD')) define('NEW_PASSWORD','0');
if (!defined('MIN_PWD_LEN')) define('MIN_PWD_LEN','8');
//if (!defined('MIN_LOWER')) define('MIN_LOWER','0');
if (!defined('MIN_UPPER')) define('MIN_UPPER','0');
if (!defined('MIN_DIGITS')) define('MIN_DIGITS','0');
if (!defined('MIN_SYMBOL')) define('MIN_SYMBOL','0');

?>