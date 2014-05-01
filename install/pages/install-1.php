<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

include_once('include/sanity.php');

$status = sanity_checkFiles();
// Run sanity checks
$errcount = count($status);
$warncount = 0; // No warnings with permissions

// create the not_configured file since we're in the installer
if (!@file_exists(BASE.'install/not_configured')) {
	$nc_file = fopen(BASE.'install/not_configured', "w");
	fclose($nc_file);
}

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
foreach ($status as $file=>$stat) {
	echo '<tr class="'.$row.'"><td>'.$file.'</td><td';
	if ($stat != SANITY_FINE) echo ' class="bodytext failed">';
	else echo ' class="bodytext success">';
	switch ($stat) {
		case SANITY_NOT_E:
			echo gt('File Not Found');
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
	$row = ($row=="even") ? "odd" : "even";
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
foreach ($status as $test=>$stat) {
	echo '<tr class="'.$row.'"><td>'.$test.'</td>';
	echo '<td align="center" width="45%" ';
	if ($stat[0] == SANITY_FINE) {
		$warncount--;
		$errcount--;
		echo 'class="bodytext success">';
	} else if ($stat[0] == SANITY_ERROR) {
		$warncount--;
		echo 'class="bodytext failed">';
	} else {
		$errcount--;
		echo 'class="bodytext warning">';
	}
	echo $stat[1].'</td></tr>';
	$row = ($row=="even") ? "odd" : "even";
}

?>
</tbody>
</table>

<?php

$write_file = 0;

if ($errcount > 0) {
	// Had errors.  Force halt and fix.
	echo gt('The Exponent Install Wizard found some major problems with the server environment, which you must fix before you can continue.');
	
	if (ini_get('safe_mode') == true) {
		echo '<br /><br /><div style="font-weight: bold; color: red;">'.gt('SAFE MODE IS ENABLED.  You may encounter many strange errors unless you give the web server user ownership of ALL Exponent files.  On UNIX, this can be done with a "chown -R" command').'</div>';
	}
	?>
	<br /><br />
    <a class="awesome large red" href="index.php?page=install-1"><?php echo gt('Re-run Environment Checks'); ?></a>
	<?php
} else if ($warncount > 0) {
	?><p><?php
	echo gt('The Exponent Install Wizard found some minor problems with the server environment, but you should be able to continue.');
	?></p><?php
	
	if (ini_get('safe_mode') == true) {
    	?><p class="important_message"><?php
    	echo gt('SAFE MODE IS ENABLED. You may encounter many strange errors unless you give the web server user ownership of ALL Exponent files. On UNIX, this can be done with a "chown -R" command');
    	?></p><?php
	}
	
	$write_file = 1;
} else {
	// No errors, and no warnings.  Let them through.
	?><p><?php
	echo gt('The Exponent Install Wizard found no problems with the server environment.');
	?></p><?php
	
	$write_file = 1;
}

if ($errcount == 0) {
?>
    <a class="awesome large green" href="index.php?page=install-2"><?php echo gt('Configure Your Database'); ?></a>
<?php
}

if ($write_file) {
#	// The following checks work on Apache and IIS.  Any other success / failure stories are welcome.
#	if (strtolower(substr(php_sapi_name(),0,3)) == 'cgi') {
#		//In CGI mode SCRIPT_NAME is not correct, so we will try PATH_INFO first...
#		// We need to strip off the last two things, filename and the install dirname.
#		$components = implode('/',array_splice(split('/',$_SERVER['PATH_INFO']),0,-2)).'/';
#	} else {
#		// If we aren't in either cgi or cgi-fast, then we are compiled in and should use SCRIPT_NAME
#		// We need to strip off the last two things, filename and the install dirname.
#		$components = implode('/',array_splice(split('/',$_SERVER['SCRIPT_NAME']),0,-2)).'/';
#	}
	
	if (isset($_SERVER['SCRIPT_NAME'])) {
        $scriptname = explode('/',$_SERVER['SCRIPT_NAME']);
	    $components = implode('/',array_splice($scriptname,0,-2)).'/';
    } elseif (isset($_SERVER['PATH_INFO'])) {
        $pathinfo = explode('/',$_SERVER['PATH_INFO']);
        $components = implode('/',array_splice($pathinfo,0,-2)).'/';
    } else {
        $components = '/';
    }
    
	$path_relative = PATH_RELATIVE;
	
	if ($components != $path_relative) {
		$path_relative = $components;
		$fh = fopen(BASE.'overrides.php','w');
		fwrite($fh,"<?php\r\n\r\ndefine('PATH_RELATIVE','$path_relative');\r\n\r\n?>\r\n");
		fclose($fh);
	}

}

?>
