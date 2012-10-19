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

?>
<div id="hd">
   <h1><?php echo gt('ExponentCMS Version Changelog'); ?></h1>
</div>
<div id="bd">
    <p><?php echo gt('Here is the list of changes from your current version'); ?></p>
    <table cellspacing="0" cellpadding="3" rules="all" border="0" style="border:1px solid grey;" width="100%" class="exp-skin-table">
        <?php
            $swversion = expVersion::swVersion();
            $dbversion = expVersion::dbVersion();
            for ($swversion->revision; $swversion->revision >= 0; $swversion->revision--) {
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
                                <tr><td colspan="2" class="bodytext" valign="top"><?php echo $line; ?></td></tr>
                                <?php
                            } else {
                                if (substr($line,0,1)=='*') $class = ' class="critical"'; else $class = '';
                                ?>
                                <tr<?php echo $class; ?>><td class="bodytext" style="font-weight: bold;" valign="top">&#160;&#160;</td>
                                    <td class="bodytext" valign="top"><?php echo $line; ?></td></tr>
                                <?php
                            }
                        }
                    }
                }
            }
        ?>
        <tr><td colspan="2" style="background-color: lightgrey;"><strong><?php echo gt('Older changes can be found in the \'CHANGELOG\' file'); ?></strong></td></tr>
    </table>
</div>