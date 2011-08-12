<?php
/** @define "BASE" "../.." */

if (!defined('EXPONENT')) exit('');

$files = include(BASE . 'modules-1/importer/manifest.php');

$dh = opendir(BASE.'modules/importer/importers');
while (($file = readdir($dh)) !== false) {
	if (is_readable(BASE.'modules/importer/importers/'.$file.'/manifest.php') && $file{0} != '.') {
		$files = array_merge($files,include(BASE.'modules/importer/importers/'.$file.'/manifest.php'));
	}
}

return $files;

?>