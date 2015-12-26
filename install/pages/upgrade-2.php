<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

include('include/sanity.php');

$status = sanity_checkFiles();
// Run sanity checks
$errcount = count($status);
$warncount = 0; // No warnings with permissions

if ($status['framework/conf/config.php'] == SANITY_NOT_RW) {
    $config_not_w = true;
    $errcount--;
    $warncount++;
} else {
    $config_not_w = false;
}

// create the not_configured file since we're in the installer
//if (!@file_exists(BASE . 'install/not_configured')) {
//    $nc_file = fopen(BASE . 'install/not_configured', "w");
//    fclose($nc_file);
//}

?>
    <h1><?php echo gt('System Requirements Check'); ?></h1>
<table cellspacing="0" cellpadding="0" rules="all" border="0" width="100%" class="exp-skin-table">
    <thead>
    <tr>
        <th colspan="2">
            <?php echo gt('File and Directory Permission Tests'); ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $row = "even";
    foreach ($status as $file => $stat) {
        echo '<tr class="' . $row . '"><td>' . $file . '</td><td';
        if ($file == 'framework/conf/config.php' && $stat == SANITY_NOT_RW) {
            echo ' class="bodytext warning">';
        } elseif ($stat != SANITY_FINE) {
            echo ' class="bodytext failed">';
        } else {
            echo ' class="bodytext success">';
        }
        switch ($stat) {
            case SANITY_NOT_E:
                echo gt('Not Found');
                break;
            case SANITY_NOT_R:
                echo gt('Not Readable');
                break;
            case SANITY_NOT_RW:
                echo gt('Not Readable / Writable');
                break;
            case SANITY_FINE:
                $errcount--;
                echo gt('Okay');
                break;
            default:
                echo '????';
                break;
        }
        echo '</td></tr>';
        $row = ($row == "even") ? "odd" : "even";
    }
    ?>
    </tbody>
    <table cellspacing="0" cellpadding="0" rules="all" border="0" width="100%" class="exp-skin-table">
        <thead>
        <tr>
            <th colspan="2">
                <?php echo gt('Other Tests'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php

        $status = sanity_checkServer();
        $errcount += count($status);
        $warncount += count($status);
        $row = "even";
        foreach ($status as $test => $stat) {
            echo '<tr class="' . $row . '"><td>' . $test . '</td>';
            echo '<td align="center" width="45%" ';
            if ($stat[0] == SANITY_FINE) {
                $warncount--;
                $errcount--;
                echo 'class="bodytext success">';
            } else {
                if ($stat[0] == SANITY_ERROR) {
                    $warncount--;
                    echo 'class="bodytext failed">';
                } else {
                    $errcount--;
                    echo 'class="bodytext warning">';
                }
            }
            echo $stat[1] . '</td></tr>';
            $row = ($row == "even") ? "odd" : "even";
        }

        ?>
        </tbody>
    </table>

<?php

if ($errcount > 0) {
    // Had errors.  Force halt and fix.
    echo gt(
        'The Exponent Upgrade Wizard found some major problems with the server environment, which you must fix before you can continue.'
    );

    if (ini_get('safe_mode') == true) {
        echo '<br /><br /><div style="font-weight: bold; color: red;">' . gt(
                'SAFE MODE IS ENABLED.  You may encounter many strange errors unless you give the web server user ownership of ALL Exponent files.  On UNIX, this can be done with a "chown -R" command'
            ) . '</div>';
    }
    ?>
    <br/><br/>
    <a class="awesome large red" href="index.php?page=upgrade-2"><?php echo gt('Re-run Environment Checks'); ?></a>
<?php
} else {
    if ($warncount > 0) {
        $errcount = 0;  // cancel out config.php write error
        ?><p><?php
        echo gt(
            'The Exponent Install Wizard found some minor problems with the server environment, but you should be able to continue.'
        );
        ?></p><?php

        if (ini_get('safe_mode') == true) {
            ?><p class="important_message"><?php
            echo gt(
                'SAFE MODE IS ENABLED. You may encounter many strange errors unless you give the web server user ownership of ALL Exponent files. On UNIX, this can be done with a "chown -R" command'
            );
            ?></p><?php
        }
        if ($config_not_w) {
            ?><p class="important_message"><?php
            echo gt(
                'The file /framework/conf/config.php is NOT Writeable. You will be unable to change Site Configuration settings.'
            );
            ?></p><?php
        }
    } else {
        // No errors, and no warnings.  Let them through.
        ?><p><?php
        echo gt('The Exponent Upgrade Wizard found no problems with the server environment.');
        ?>
        </p>
        <p>
            <?php
            echo gt(
                    "Next, we'll"
                ) . ' <a href="http://docs.exponentcms.org/docs/current/update-tables" target="_blank">' .
                gt('Install Tables') . '</a>, ' . gt(
                    "and then run through any upgrade scripts needed to bring your code and database up to date."
                );
            ?>
        </p>
        <?php
    }
}

if ($errcount == 0) {
    ?>
    <a class="awesome large green" href="index.php?page=upgrade-3"><?php echo gt(
            'Continue to Update the Database Tables'
        ); ?></a>
<?php
}

?>