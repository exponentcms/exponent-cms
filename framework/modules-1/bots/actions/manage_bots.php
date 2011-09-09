<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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
	expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
	
	// get new bots into the database
	if (is_readable(BASE.'framework/modules-1/bots/bots')) {
        $dh = opendir(BASE.'framework/modules-1/bots/bots');
        while (($file = readdir($dh)) !== false) {
			$botfile = BASE.'framework/modules-1/bots/bots/'.$file;
            if (is_file($botfile) && is_readable($botfile) && substr($file, -4) == '.php') {
                include_once($botfile);
				$botname = substr($file, 0, -4);
				$bot = $db->selectObject('bots', "name='".$botname."'");
				if (empty($bot)) {
					$botobj = new $botname();
					$bot = null;
					$bot->name = $botname;
					$bot->state = 0;
					$bot->displayname = $botobj->displayname();
					$bot->author = $botobj->author();
					$db->insertObject($bot, 'bots');
				}
            }
        }
    }
	
	$bots = $db->selectObjects('bots');
	$template = new template('bots','_bot_manager',$loc);
	$template->assign('bots', $bots);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
