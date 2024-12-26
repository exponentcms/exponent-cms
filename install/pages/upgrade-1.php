<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

if (!defined('EXPONENT')) {
    exit('');
}

?>
<h2><?php echo gt('Simple Site Upgrade'); ?></h2>
<h3><?php echo gt('Upgrading to') . ' v' . expVersion::getVersion(true, true) . ' ' . gt(
            'from'
        ) . ' v' . expVersion::getDBVersion(true, true); ?></h3>
<br>
<p>
    <?php
    // build core css files from .less after clearing the less compiler cache
    expFile::removeFilesInDirectory(BASE . 'tmp/css');
    expCSS::updateCoreCss();

    $upgradedb = 'upgrade-2';
    $upgradedbstr = gt('Continue to Check System Requirements');
    if ($db->tableExists('textitem')) {
        echo '<div style="color: red; font-weight: bold">';
        echo gt("This is a 0.9x database.") . ' ' . gt(
                "Create a new database, then MIGRATE from the 0.9x database after installation."
            );
        echo '</div>';
        $upgradedb = 'install-2';
        $upgradedbstr = gt('Switching to Installer');

        // create the not_configured file since we're in the installer
//        if (!@file_exists(BASE . 'install/not_configured')) {
//            $nc_file = fopen(BASE . 'install/not_configured', "w");
//            fclose($nc_file);
//        }
    } else {
    echo gt(
        "Since your website has a configuration file already in place, we're going to perform a couple simple tasks to ensure you're up and running in no time."
    );
    ?>
</p>
    <p>
        <?php
        echo gt("We recommend having a recent database backup before performing an upgrade.") . '  ';
        echo gt("If you do not have a recent backup, then first");
        echo ' <a href="' . expCore::makeLink(array('controller' => 'file', 'action' => 'export_eql')) . '">' . gt(
                'Backup Database'
            ) . '</a> ';
        echo gt("and then run the upgrade process again.");
        ?>
    </p>
<?php } ?>
<a class="awesome large green" href="?page=<?php echo $upgradedb ?>"><?php echo $upgradedbstr ?></a>