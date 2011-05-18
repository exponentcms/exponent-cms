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

exponent_sessions_unset('installer_config');
exponent_sessions_clearAllSessionData();

global $user;

if (unlink(BASE.'install/not_configured')) { 
    
    ?>
    <h2><?php echo gt('You\'re all set!') ?></h2>
<?php } else { ?>
    <h2><?php echo gt('Hmmm..') ?></h2>
    <p><?php echo gt('We weren\'t able to remove /install/not_configured. Remove this file manually to complete your installation.') ?></p>
<?php } ?>

<?php if (isset($_REQUEST['upgrade'])) { ?>
<p><?php echo gt('Log back in to start using all your fancy new enhancements!') ?></p>
<a class="awesome large green" href="<?php echo URL_FULL; ?>login.php">Log In Screen</a>
<?php } ?>
