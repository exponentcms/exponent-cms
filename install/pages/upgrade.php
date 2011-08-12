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

if (!defined('EXPONENT'))
    exit('');

$errors = array();

?>
<h2 id="subtitle"><?php
echo gt('Running the Upgrade Scripts');
?></h2>
<table cellspacing="0" cellpadding="3" rules="all" border="0" style="border:1px solid grey;" width="425">
<tr>
	<td style="background-color: lightgrey;"><b><?php
echo gt('Upgrade Performed');
?></b></td>
	<td style="background-color: lightgrey;"><b><?php
echo gt('Status');
?></b></td>
</tr>
<?php

//Run the upgrade scripts
$upgrade_dir = 'upgrades';
if (is_readable('include/upgradescript.php'))
    include_once('include/upgradescript.php');
if (is_readable($upgrade_dir)) {
    $dh = opendir($upgrade_dir);
    while (($file = readdir($dh)) !== false) {
        if (is_readable($upgrade_dir . '/' . $file) && is_file($upgrade_dir . '/' . $file) && ($file != '.' && $file != '..' && $file != '.svn' && substr($file, -4, 4) != '.swp')) {
            include_once($upgrade_dir . '/' . $file);
            $classname     = substr($file, 0, -4);
            $upgradescript = new $classname;
            if ($upgradescript->checkVersion($_POST['from_version'])) {
                echo '<tr><td>' . $upgradescript->name() . '</td>';
                echo '<td class="bodytext success">' . $upgradescript->upgrade() . '</td></tr>';
            }
        }
    }
}

echo '</table>';

if (count($errors)) {
    echo gt('Errors were encountered upgrading the site database.') . '<br /><br /><br />';
    foreach ($errors as $e)
        echo $e . '<br />';
} else {
    echo gt('Upgrade Successful!');
    ;
    echo '<br /><br /><a class="awesome large green" href="?page=final">' . gt('Complete the Upgrade') . '</a>';
}

?>
