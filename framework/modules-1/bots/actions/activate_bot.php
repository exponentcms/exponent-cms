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

// Part of the Extensions category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('extensions',exponent_core_makeLocation('administrationmodule'))) {
	// turn on the bot
	$bot = $db->selectObject('bots', 'id='.$_REQUEST['id']);
	$bot->state = 1;
	$db->updateObject($bot, 'bots');

	// start the bot
	$convo  = "GET ".PATH_RELATIVE."index.php?module=bots&action=run_bot&id=".$_REQUEST['id']."&ajax_action=1 HTTP/1.1\r\n";
    $convo .= "Host: " . HOSTNAME . "\r\n";
    $convo .= "User-Agent:  ExponentCMS/".EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR.".".EXPONENT_VERSION_REVISION."  Build/".EXPONENT_VERSION_ITERATION." PHP/".phpversion()."\r\n";
    $convo .= "Connection: Close\r\n\r\n";
    try {
	    $theSpawn = fsockopen(HOSTNAME, 80);
        try {
        	fwrite ($theSpawn, $convo);
            sleep(1);
            fclose($theSpawn);
        } catch (Exception $error) {
            eLog("Error writing to socket: <br />",'','',1);
            eLog($error->getMessage(),'','',1);
        }
    } catch (Exception $error) {
        eLog("Error opening socket: <br />",'','',1);
        eLog($error->getMessage(),'','',1);
    }

	// go back to the manager
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>
