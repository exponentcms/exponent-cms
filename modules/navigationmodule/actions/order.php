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

if (exponent_permissions_check('manage',exponent_core_makeLocation('navigationmodule','',intval($_GET['parent'])))) {
	$db->switchValues('section','rank',intval($_GET['a']),intval($_GET['b']),'parent=' . intval($_GET['parent']));
	exponent_sessions_clearAllUsersSessionCache('navigationmodule');
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>
