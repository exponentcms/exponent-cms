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

if (!defined('MAINTENANCE_MODE')) define('MAINTENANCE_MODE','0');
if (!defined('MAINTENANCE_MSG_HTML')) define('MAINTENANCE_MSG_HTML',exponent_unhtmlentities('This site is currently down for maintenance.'));
if (!defined('MAINTENANCE_USE_RETURN_TIME')) define('MAINTENANCE_USE_RETURN_TIME','0');
if (!defined('MAINTENANCE_RETURN_TEXT')) define('MAINTENANCE_RETURN_TEXT','The web site will return in');
if (!defined('MAINTENANCE_RETURN_TIME')) define('MAINTENANCE_RETURN_TIME','0');

if (!defined('DEVELOPMENT')) define('DEVELOPMENT','0');
if (!defined('LOGGER')) define('LOGGER','0');
if (!defined('DEBUG_HISTORY')) define('DEBUG_HISTORY','0');
if (!defined('AJAX_ERROR_REPORTING')) define('AJAX_ERROR_REPORTING','0');

?>