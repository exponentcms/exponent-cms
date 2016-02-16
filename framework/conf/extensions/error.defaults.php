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

if (!defined('SITE_404_TITLE')) define('SITE_404_TITLE','Page Not Found');
if (!defined('SITE_404_HTML')) define('SITE_404_HTML',exponent_unhtmlentities('The page you were looking for wasn&apos;t found.  It may have been moved or deleted.'));
if (!defined('SITE_404_FILE')) define('SITE_404_FILE','');
if (!defined('SITE_403_REAL_HTML')) define('SITE_403_REAL_HTML',exponent_unhtmlentities('<h3>Authorization Failed</h3>You are not allowed to perform this operation.'));
if (!defined('SITE_403_FILE')) define('SITE_403_FILE','');
if (!defined('SITE_500_FILE')) define('SITE_500_FILE','');
if (!defined('SESSION_TIMEOUT_HTML')) define('SESSION_TIMEOUT_HTML',exponent_unhtmlentities('<h3>Expired Login Session</h3>Your session has expired, because you were idle too long.  You will have to log back into the system to continue what you were doing.'));

?>