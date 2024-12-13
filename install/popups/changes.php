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

?>
<div id="hd">
   <h1><?php echo gt('ExponentCMS Version Changelog'); ?></h1>
</div>
<div id="bd">
    <p><?php echo gt('Here is the list of changes from your current version'); ?></p>
    <table style="border:1px solid grey;" class="exp-skin-table">
        <?php
            $swversion = expVersion::swVersion();
            $dbversion = expVersion::dbVersion();
            if (!expVersion::compareVersion($dbversion,$swversion)) {
                ?>
                <tr><td colspan="2" style="background-color: orange;"><strong><?php echo gt('You already appear to be running this version'); ?></strong></td></tr>
                <tr><td colspan="2">&#160;</td></tr>
                <?php
            }
            for ($swversion->revision; $swversion->revision >= -1; $swversion->revision--) {
                if ($swversion->revision == -1) {
                    $swversion->revision = 9;
                    $swversion->minor--;
                    if ($swversion->minor == -1) break;
                }
                if (expVersion::compareVersion($dbversion,$swversion)) {
//                    include('./changes/'.$swversion->major.'.'.$swversion->minor.'.'.$swversion->revision.'.php');
                    if (file_exists('./changes/'.$swversion->major.'.'.$swversion->minor.'.'.$swversion->revision.'.txt')) {
                        $file = file('./changes/'.$swversion->major.'.'.$swversion->minor.'.'.$swversion->revision.'.txt');
                        $file = array_filter($file);  // remove empty lines
                        foreach ($file as $key=>$line) {
                            $line = trim($line);
                            if ($key == 0) {
                                ?>
                                <tr><td colspan="2" style="background-color: lightgrey;"><strong><?php echo $line; ?></strong></td></tr>
                                <?php
                            } elseif ($key == 1) {
                                ?>
                                <tr><td colspan="2" class="bodytext"><?php echo $line; ?></td></tr>
                                <?php
                            } else {
                                if (substr($line,0,1)=='*') $class = ' class="critical"'; else $class = '';
                                ?>
                                <tr<?php echo $class; ?>><td class="bodytext" style="font-weight: bold;">&#160;&#160;</td>
                                    <td class="bodytext"><?php echo $line; ?></td></tr>
                                <?php
                            }
                        }
                    }
                }
            }
        ?>
        <tr><td colspan="2" style="background-color: lightgrey;"><strong><?php echo gt('Older changes can be found in the \'CHANGELOG.md\' file'); ?></strong></td></tr>
    </table>
</div>