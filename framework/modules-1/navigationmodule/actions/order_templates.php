<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

if ($user && $user->is_acting_admin == 1) {
	$db->switchValues('section_template','rank',$_GET['a'],$_GET['b'],'parent='.intval($_GET['parent']));
	
	expSession::clearAllUsersSessionCache('navigationmodule');
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>