<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

define('SANITY_FINE',				0);
define('SANITY_NOT_R',				2);
define('SANITY_NOT_RW',				4);
define('SANITY_NOT_E',				8);

define('SANITY_READONLY',			1);
define('SANITY_READWRITE',			2);
define('SANITY_CREATEFILE',			4); // Read write, without the need for the file to exist prior

define('SANITY_WARNING',			1);
define('SANITY_ERROR',				2);

function sanity_checkFile($file,$as_file,$flags) {
	$__oldumask = umask(0);
	if (!file_exists($file)) {
		if ($flags == SANITY_CREATEFILE) {
			return sanity_checkFile(dirname($file),false,SANITY_READWRITE);
		} else {
			if ($as_file) {
				@touch($file);
			} else {
				@mkdir($file,0777);
			}
		}
	}
	if (!file_exists($file)) {
		umask($__oldumask);
		return SANITY_NOT_E;
	} else if ($flags == SANITY_CREATEFILE) {
		$flags = SANITY_READWRITE;
	}
	$not_r = false;
	// File exists.  Check the flags for what to check for
	if ($flags == SANITY_READONLY || $flags == SANITY_READWRITE) {
		if (!is_readable($file)) {
			@chmod($file,0777);
		}
		if (!is_readable($file)) {
			if ($flags == SANITY_READONLY) {
				umask($__oldumask);
				return SANITY_NOT_R;
			}
			// Otherwise, we need to set NOT_R
			$not_r = true;
		}
	}
	if ($flags == SANITY_READWRITE) {
		if (!expUtil::isReallyWritable($file)) {
			@chmod($file,0777);
		}
		if (!expUtil::isReallyWritable($file)) {
			umask($__oldumask);
			return SANITY_NOT_RW;
		} else if ($not_r) {
			umask($__oldumask);
			return SANITY_NOT_R;
		}
	}
	return SANITY_FINE;
}

function sanity_checkDirectory($dir,$flag) {
	$status = sanity_checkFile(BASE.$dir,0,$flag);
	if ($status != SANITY_FINE) {
		return $status;
	}
	
	if (is_readable(BASE.$dir)) {
		$dh = opendir(BASE.$dir);
		while (($file = readdir($dh)) !== false) {
			if ($file{0} != '.' && $file != 'CVS') {
				if (is_file(BASE.$dir.'/'.$file)) {
					$status = sanity_checkFile(BASE.$dir.'/'.$file,1,$flag);
					if ($status != SANITY_FINE) {
						return $status;
					}
				} else {
					$status = sanity_checkDirectory($dir.'/'.$file,$flag);
					if ($status != SANITY_FINE) {
						return $status;
					}
				}
			}
		}
	}
	return $status;
}

function sanity_checkFiles() {
	$status = array(
		'conf/config.php'=>sanity_checkFile(BASE.'conf/config.php',1,SANITY_CREATEFILE),
		'files/'=>sanity_checkDirectory('files',SANITY_READWRITE),
		'install/'=>sanity_checkFile(BASE.'install',0,SANITY_READWRITE),
		'framework/modules/'=>sanity_checkDirectory('framework/modules',SANITY_READWRITE),
		//'framework/datatypes/'=>sanity_checkDirectory('framework/datatypes',SANITY_READWRITE),
		//'conf/profiles/'=>sanity_checkFile(BASE.'conf/profiles',0,SANITY_READWRITE),
		//'overrides.php'=>sanity_checkFile(BASE.'overrides.php',1,SANITY_READWRITE),
		'framework/modules-1/'=>sanity_checkFile(BASE.'framework/modules-1/',0,SANITY_READONLY),
		'tmp/'=>sanity_checkDirectory('tmp',SANITY_READWRITE),
		'tmp/extensionuploads/'=>sanity_checkFile(BASE.'tmp/extensionuploads',0,SANITY_READWRITE),
		'tmp/views_c'=>sanity_checkDirectory('tmp/views_c',SANITY_READWRITE),
		'tmp/cache'=>sanity_checkDirectory('tmp/cache',SANITY_READWRITE),
		'tmp/minify'=>sanity_checkDirectory('tmp/minify',SANITY_READWRITE),
		'tmp/css'=>sanity_checkDirectory('tmp/css',SANITY_READWRITE),
//		'tmp/pods'=>sanity_checkDirectory('tmp/pods',SANITY_READWRITE),
		'tmp/rsscache'=>sanity_checkDirectory('tmp/rsscache',SANITY_READWRITE),
//		'tmp/mail'=>sanity_checkDirectory('tmp/mail',SANITY_READWRITE),
		'tmp/img_cache'=>sanity_checkDirectory('tmp/img_cache',SANITY_READWRITE),
//		'framework/modules/pixidou/images'=>sanity_checkDirectory('framework/modules/pixidou/images',SANITY_READWRITE)
		'tmp/pixidou'=>sanity_checkDirectory('tmp/pixidou',SANITY_READWRITE)
	);
	
	return $status;
}

function sanity_checkServer() {
	$status = array(
		gt('Database Backend')=>_sanity_checkDB(),
		gt('GD Graphics Library 2.0+')=>_sanity_checkGD(),
		gt('PHP 5.2.1+')=>_sanity_checkPHPVersion(),
		gt('ZLib Support')=>_sanity_checkZlib(),
		gt('XML (Expat) Library Support')=>_sanity_checkXML(),
		gt('Safe Mode Not Enabled')=>_sanity_CheckSafeMode(),
		gt('Open BaseDir Not Enabled')=>_sanity_checkOpenBaseDir(),
		gt('File Uploads Enabled')=>_sanity_checkTemp(ini_get('upload_tmp_dir')),
		gt('Temporary File Creation')=>_sanity_checkTemp(BASE.'tmp'),
	);
	return $status;
}

function _sanity_checkGD() {
	$info = gd_info();
	if ($info['GD Version'] == 'Not Supported') {
		return array(SANITY_WARNING,gt('No GD Support'));
	} else if (strpos($info['GD Version'],'2.0') === false) {
		return array(SANITY_WARNING,sprintf(gt('Older Version Installed (%s)'),$info['GD Version']));
	}
	return array(SANITY_FINE,$info['GD Version']);
}

function _sanity_checkPHPVersion() {
	if (version_compare(phpversion(),'5.2.1','>=')) {
		return array(SANITY_FINE,phpversion());
	} else {
		return array(SANITY_ERROR,'This version of ExponentCMS requires PHP 5.2.1 or higher. You are running PHP '.phpversion().'<br>'.gt('(not supported)'));
	}
}

function _sanity_checkZlib() {
	if (function_exists('gzdeflate')) {
		return array(SANITY_FINE,gt('Passed'));
	} else {
		return array(SANITY_ERROR,gt('Failed'));
	}
}

function _sanity_checkSafeMode() {
	if (ini_get('safe_mode') == 1) {
		return array(SANITY_WARNING,gt('Failed'));
	} else {
		return array(SANITY_FINE,gt('Passed'));
	}
}

function _sanity_checkXML() {
	if (function_exists('xml_parser_create')) {
		return array(SANITY_FINE,gt('Passed'));
	} else {
		return array(SANITY_WARNING,gt('Failed'));
	}
}

function _sanity_checkOpenBaseDir() {
	$path = ini_get('open_basedir');
	if ($path == '') {
		return array(SANITY_FINE,gt('Passed'));
	} else {
		return array(SANITY_WARNING,gt('Failed'));
	}
}

function _sanity_checkTemp($dir) {
	$file = tempnam($dir,'temp');
	if (is_readable($file) && expUtil::isReallyWritable($file)) {
		unlink($file);
		return array(SANITY_FINE,gt('Passed'));
	} else {
		return array(SANITY_ERROR,gt('Failed'));
	}
}

function _sanity_checkDB() {
//	require_once(BASE.'framework/core/subsystems-1/database.php');
//	$have_good = false;
	
	if (count(expDatabase::backends(1)) > 0) {
		return array(SANITY_FINE,gt('Supported'));
	} else {
		return array(SANITY_ERROR,gt('No Databases Supported'));
	}
}

?>

