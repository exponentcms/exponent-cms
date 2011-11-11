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

//expSession::un_set('installer_config');
expSession::clearAllSessionData();

global $user;

// We have to force the language name into the config.php file
$lang = str_replace("'", "", trim($_REQUEST['lang']));
expSettings::change('LANGUAGE',$lang);

if (isset($_REQUEST['upgrade'])) { 
// upgrades hit this
//    if (unlink(BASE.'install/not_configured')) {
    $leaveinstaller = (unlink(BASE.'install/not_configured')||!file_exists(BASE.'install/not_configured'));
    if ($leaveinstaller) {
        echo '<h2>' . gt('You\'re all set!') ."</h2>";
        echo '<p>' . gt('Take me to your leader') ."</p>";
    } else {
        echo '<h2>' . gt('Hmmmm....') ."</h2>";
        echo '<p>' . gt('We weren\'t able to remove /install/not_configured. Remove this file manually to complete your upgrade.') ."</p>";
    }
?>
    <p><?php echo gt('Log back in to start using all your fancy new enhancements!') ?></p>
    <a class="awesome large green" href="<?php echo URL_FULL; ?>login.php"><?php echo gt("Log In Screen"); ?></a>
<?php

} else {
    if (isset($_POST['username'])) {
        user::login($_POST['username'],$_POST['password']);
        $leaveinstaller = (unlink(BASE.'install/not_configured')||!file_exists(BASE.'install/not_configured'));
        if ($leaveinstaller) { 
            if ($user->id!=0) {
                switch ($_POST['next']) {
                    case 'migration':
                        if (SEF_URLS) {
                    	    header('Location: '.URL_FULL."migration/configure/");
                        } else {
                    	    header('Location: '.URL_FULL."index.php?controller=migration&action=configure");
                        }
                        break;
                    case 'configsite':
                        if (SEF_URLS) {
                    	    header('Location: '.URL_FULL."administration/configure_site/");
                        } else {
                    	    header('Location: '.URL_FULL."index.php?controller=administration&action=configure_site");
                        }
                        break;
                    default:
                    	    header('Location: '.URL_FULL);
                        break;
                }
            } else {
                echo '<h2>' . gt('Hmmmm....') ."</h2>";
	            echo '<p>' . gt('Either we weren\'t able to log in or') ."</p>";
                echo '<p>' . gt('We weren\'t able to remove /install/not_configured. Remove this file manually to complete your installation.') ."</p>";
            }
        }
    }

    echo '<h2>' . gt('You\'re all set!') ."</h2>";
    echo '<p>' . gt('Log In and start managing your site') ."</p>";

?>
	<form action="index.php?page=final" method="POST">
		<input type="hidden" name="lang" value="<?php echo LANGUAGE; ?>" />
		<div class="text-control control ">
			<label class="label"><?php echo gt("Username").':'; ?></label><input type="text" class="text " size="25" value="" name="username">
		</div>
		<div class="password-control control ">
			<label class="label"><?php echo gt("Password").':'; ?></label><input type="password" class="password " size="25" value="" name="password">
		</div>
        <div class="formcontrol radiogroup">
            <span class="label"><?php echo gt('And'); ?>:</span>
            <div class="formcontrol radiobutton">
                <input type="radio" id="radiocontrol-1" class="radiobutton" value="migration" name="next">
				<label for="radiocontrol-1"><?php echo gt("I want to begin transferring an existing Exponent v0.9x site"); ?></label>
            </div>
            <div class="formcontrol radiobutton">
                <input type="radio" id="radiocontrol-2" class="radiobutton" value="configsite" name="next">
				<label for="radiocontrol-2"><?php echo gt("I want to start configuring my new site"); ?></label>
            </div>
            <div class="formcontrol radiobutton">
                <input type="radio" id="radiocontrol-3" class="radiobutton" value="homepage" name="next">
				<label for="radiocontrol-3"><?php echo gt("Just take me to my home page"); ?></label>
            </div>
        </div>
		<div class="control buttongroup">
			<button class="awesome large green"><?php echo gt("Log In"); ?></button>
		</div>
	</form>
    
<?php
}
?>
