<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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
//$num_version = EXPONENT_VERSION_MAJOR.'.'.EXPONENT_VERSION_MINOR.'.'.EXPONENT_VERSION_REVISION;
$num_version = expVersion::getVersion();
global $db;
$db_version = $db->selectObject('version','1');

?>
<h2><?php echo gt('Upgrade Scripts'); ?></h2>
<p>
<?php 
echo gt("Exponent will perform the following upgrades").':';

//display the upgrade scripts
$upgrade_dir = 'upgrades';
$i = 0;
if (is_readable('include/upgradescript.php')) include_once('include/upgradescript.php');
if (is_readable($upgrade_dir)) {
    $dh = opendir($upgrade_dir);
    echo '<ol>';
    while (($file = readdir($dh)) !== false) {
        if (is_readable($upgrade_dir . '/' . $file) && is_file($upgrade_dir . '/' . $file) && ($file != '.' && $file != '..' && $file != '.svn' && substr($file, -4, 4) != '.swp')) {
            include_once($upgrade_dir . '/' . $file);
            $classname     = substr($file, 0, -4);
            /**
             * Stores the upgradescript object
             * @var \upgradescript $upgradescript
             * @name $upgradescript
             */
            $upgradescript = new $classname;
//            if ($upgradescript->checkVersion($num_version) && $upgradescript->needed($num_version)) {
            if ($upgradescript->checkVersion($db_version) && $upgradescript->needed()) {
                echo '<li><h3>' . $upgradescript->name() . '</h3>';
                if (isset($_REQUEST['run'])) {
                    echo '<p class="success">' . $upgradescript->upgrade() . '</p></li>';
                } else {
                    echo '<p>' . $upgradescript->description() . '</p></li>';
                }
                $i++;
            }
        }
    }
    if ($i==0) {
        echo '<li>
        <h3>None</h3>
        <p>'.gt('You\'re good to go. Click next to finish up.').'</p>
        </li>';
    }
    echo '</ol>';
}

?>
</p>

<?php if (isset($_REQUEST['run']) || $i==0) { ?>
    <a class="awesome large green" href="?page=final&amp;upgrade=1"><?php echo gt("Finish Upgrade"); ?></a>
<?php } else { ?>
    <a class="awesome large green" href="?page=upgrade-3&amp;run=1"><?php echo gt("Run Upgrades"); ?></a>
<?php } ?>