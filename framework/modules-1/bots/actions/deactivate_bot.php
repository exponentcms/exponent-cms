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

// Part of the Extensions category

if (!defined('EXPONENT')) exit('');

if (expPermissions::check('extensions',expCore::makeLocation('administrationmodule'))) {
	$bot = $db->selectObject('bots', 'id='.$_REQUEST['id']);
	$bot->state = 0;
	$db->updateObject($bot, 'bots');
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
