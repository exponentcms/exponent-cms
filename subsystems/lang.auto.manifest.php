<?php
/** @define "BASE" ".." */

if (!defined('EXPONENT')) exit('');

$files = include(BASE.'subsystems/lang.manifest.php');

$dh = opendir(BASE.'subsystems/lang');

while (($file = readdir($dh)) !== false) {
	if (is_readable(BASE.'subsystems/lang/'.$file.'/manifest.php') && $file{0} != '.') {
		$files = array_merge($files,include(BASE.'subsystems/lang/'.$file.'/manifest.php'));
	}
}

return $files;

?>