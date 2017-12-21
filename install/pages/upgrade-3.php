<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
<h2><?php echo gt('Upgrading database tables'); ?></h2>

<?php
global $db;

$tables = expDatabase::install_dbtables();
ksort($tables);

?>

<table cellpadding="2" cellspacing="0" width="100%" border="0" class="exp-skin-table">
    <thead>
    <tr>
        <th>
            <?php echo gt('Table Name') ?>
        </th>
        <th>
            <?php echo gt('Status') ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $row = "even";
    $line = 0;
    foreach ($tables as $table => $statusnum) {
        if ($statusnum != DATABASE_TABLE_EXISTED) {
            ?>

            <tr class="<?php echo $row ?>">
                <td>
                    <?php echo gt($table) ?>
                </td>
                <td>
                    <?php if ($statusnum == DATABASE_TABLE_INSTALLED) { ?>
                        <div style="color: green; font-weight: bold">
                            <?php echo gt('Added') ?>
                        </div>
                    <?php } elseif ($statusnum == DATABASE_TABLE_FAILED) { ?>
                        <div style="color: red; font-weight: bold">
                            <?php echo gt('Failed') ?>
                        </div>
                    <?php } elseif ($statusnum == DATABASE_TABLE_ALTERED) { ?>
                        <div style="color: green; font-weight: bold">
                            <?php echo gt('Altered Existing') ?>
                        </div>
                    <?php } elseif ($statusnum == TABLE_ALTER_FAILED) { ?>
                        <div style="color: red; font-weight: bold">
                            <?php echo gt('Failed Altering') ?>
                        </div>
                    <?php } ?>
                </td>
            </tr>
            <?php
            $row = $row == "even" ? "odd" : "even";
            $line++;
        }
        ?>
    <?php
    }
    ?>
    </tbody>
</table>
<?php
if ($line == 0) {
    echo "<p class=\"success\">" . gt("No Database Tables Were Changed!") . "</p>";
}

$emptydb = 'upgrade-4';
$emptydbstr = gt('Continue Upgrade');
// check to see if we are really upgrading a database
if ($db->tableIsEmpty('user') || $db->tableIsEmpty('modstate') || $db->tableIsEmpty('section')) {
    echo '<div style="color: red; font-weight: bold">';
    echo 'No Database Entries Were Found!';
    echo '<br /></div>';
    $emptydb = 'install-2';
    $emptydbstr = gt('Switch to Installer');

    // create the not_configured file since we're in the installer
//    if (!@file_exists(BASE . 'install/not_configured')) {
//        $nc_file = fopen(BASE . 'install/not_configured', "w");
//        fclose($nc_file);
//    }
}
?>
<a class="awesome large green" href="?page=<?php echo $emptydb ?>"><?php echo $emptydbstr ?></a>