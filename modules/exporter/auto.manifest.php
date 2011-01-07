<?php

if (!defined('EXPONENT')) exit('');

$files = include(BASE.'modules/exporter/manifest.php');

$dh = opendir(BASE.'modules/exporter/exporters');
while (($file = readdir($dh)) !== false) {
	if (is_readable(BASE.'modules/exporter/exporters/'.$file.'/manifest.php') && $file{0} != '.') {
		$files = array_merge($files,include(BASE.'modules/exporter/exporters/'.$file.'/manifest.php'));
	}
}

return $files;

?>