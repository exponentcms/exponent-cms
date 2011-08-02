<?php
/** @define "BASE" ".." */

if (!defined('EXPONENT')) exit('');

$files = include(BASE.'framework/core/subsystems-1/lang.manifest.php');

$dh = opendir(BASE.'framework/core/subsystems-1/lang');

while (($file = readdir($dh)) !== false) {
	if (is_readable(BASE.'framework/core/subsystems-1/lang/'.$file.'/manifest.php') && $file{0} != '.') {
		$files = array_merge($files,include(BASE.'framework/core/subsystems-1/lang/'.$file.'/manifest.php'));
	}
}

return $files;

?>