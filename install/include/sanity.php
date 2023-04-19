<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

define('SANITY_FINE',				0);  // no SANITY_WARNING/SANITY_ERROR
define('SANITY_NOT_R',				2);  // SANITY_ERROR
define('SANITY_NOT_RW',				4);  // SANITY_ERROR
define('SANITY_NOT_E',				8);  // SANITY_ERROR

define('SANITY_READONLY',			1);
define('SANITY_READWRITE',			2);
define('SANITY_CREATEFILE',			4); // Read write, without the need for the file to exist prior

define('SANITY_WARNING',			1);
define('SANITY_ERROR',				2);

/**
 * Check file/folder for requested permissions
 * @param $file     string file/folder name
 * @param $as_file  bool is this a file or a folder
 * @param $flags    int type of check to perform
 * @return int      error
 */
function sanity_checkFile($file, $as_file, $flags) {
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

/**
 * Recursively check folder and all files/folders within for requested permissions
 * @param $dir  string folder name
 * @param $flag int type of check
 * @return int  error
 */
function sanity_checkDirectory($dir, $flag) {
	$status = sanity_checkFile(BASE.$dir,false,$flag);
	if ($status != SANITY_FINE) {
		return $status;
	}

	if (is_readable(BASE . $dir)) {
		$dh = opendir(BASE . $dir);
		while (($file = readdir($dh)) !== false) {
			if ($file[0] !== '.' && $file !== 'CVS') {
				if (is_file(BASE . $dir . '/' . $file)) {
					$status = sanity_checkFile(BASE . $dir . '/' . $file,true, $flag);
					if ($status != SANITY_FINE) {
						return $status;
					}
				} else {
					$status = sanity_checkDirectory($dir . '/' . $file, $flag);
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
		'framework/conf/config.php'=>sanity_checkFile(BASE.'framework/conf/config.php',true,SANITY_CREATEFILE),
		'framework/conf/profiles/'=>sanity_checkFile(BASE.'framework/conf/profiles',false,SANITY_READWRITE),
        'files/'=>sanity_checkFile('files',false,SANITY_READWRITE),  // we're only concerned about the folder itself and NOT the contents
        'files/uploads/'=>sanity_checkDirectory('files/uploads',SANITY_READWRITE),
        'files/avatars/'=>sanity_checkDirectory('files/avatars',SANITY_READWRITE),
		'install/'=>sanity_checkFile(BASE.'install',false,SANITY_READWRITE),
		'overrides.php'=>sanity_checkFile(BASE.'overrides.php',true,SANITY_CREATEFILE),
		'tmp/'=>sanity_checkDirectory('tmp',SANITY_READWRITE),
		'tmp/cache'=>sanity_checkDirectory('tmp/cache',SANITY_READWRITE),
        'tmp/css'=>sanity_checkDirectory('tmp/css',SANITY_READWRITE),
		'tmp/elfinder'=>sanity_checkDirectory('tmp/css',SANITY_READWRITE),
        'tmp/extensionuploads'=>sanity_checkFile(BASE.'tmp/extensionuploads',true,SANITY_READWRITE),
        'tmp/img_cache'=>sanity_checkDirectory('tmp/img_cache',SANITY_READWRITE),
		'tmp/minify'=>sanity_checkDirectory('tmp/minify',SANITY_READWRITE),
        'tmp/pixidou'=>sanity_checkDirectory('tmp/pixidou',SANITY_READWRITE),
		'tmp/rsscache'=>sanity_checkDirectory('tmp/rsscache',SANITY_READWRITE),
        'tmp/views_c'=>sanity_checkDirectory('tmp/views_c',SANITY_READWRITE),
	);

	return $status;  // any error or warning is fatal
}

function sanity_checkServer() {
	$status = array(
		gt('Database Backend')=>_sanity_checkDB(),
		gt('GD Graphics Library 2.0+')=>_sanity_checkGD(),
		'PHP 7.4.0+'=>_sanity_checkPHPVersion(),
		gt('ZLib Support')=>_sanity_checkZlib(),
        gt('Zip Support')=>_sanity_checkZip(),
        gt('cURL Library Support')=>_sanity_checkcURL(),
		gt('XML (Expat) Library Support')=>_sanity_checkXML(),
//		gt('Safe Mode Not Enabled')=>_sanity_CheckSafeMode(),
		gt('Open BaseDir Not Enabled')=>_sanity_checkOpenBaseDir(),
		gt('FileInfo Support')=>_sanity_checkFileinfo(),
		gt('File Upload Support')=>_sanity_checkUploadSize(),
	);
	$fiup = _sanity_checkTemp(ini_get('upload_tmp_dir'));
	$tmpf = _sanity_checkTemp(BASE.'tmp');
	if ($fiup[0] === SANITY_ERROR) {  // upload_tmp_dir failed, let's try tmp
		if ($tmpf[0] === SANITY_ERROR) {
			$status = array_merge($status, array(gt('File Uploads Enabled')=>$fiup));
		} else {
			$status = array_merge($status, array(gt('File Uploads Enabled')=>array(SANITY_WARNING, gt('Failed'))));
		}
	} else {
		$status = array_merge($status, array(gt('File Uploads Enabled')=>$fiup));
	}
	$status = array_merge($status, array(gt('Temporary File Creation')=>$tmpf));
	return $status;
}

function _sanity_checkDB() {
	if (count(expDatabase::backends(1)) > 0) {
		return array(SANITY_FINE,gt('Supported'));
	} else {
		return array(SANITY_ERROR,gt('No Databases Supported'));
	}
}

function _sanity_checkGD() {
	if (!EXPONENT_HAS_GD) {
		return array(SANITY_WARNING,gt('No GD Support'));
	}
	$info = gd_info();
	if ($info['GD Version'] === 'Not Supported') {
		return array(SANITY_WARNING,gt('No GD Support, PHP gd extension needed'));
	} else if (strpos($info['GD Version'],'2.') === false) {
		return array(SANITY_WARNING,sprintf(gt('Older Version Installed').' (%s)',$info['GD Version']));
	}
	return array(SANITY_FINE,$info['GD Version']);
}

function _sanity_checkPHPVersion() {
	if (version_compare(phpversion(),'7.4.0','>=')) {
		return array(SANITY_FINE,phpversion());
	} else {
		return array(SANITY_ERROR,gt('This version of ExponentCMS requires PHP 7.4.0 or higher. You are running PHP').' '.phpversion().'<br>('.gt('not supported'.')'));
	}
}

function _sanity_checkZlib() {
	if (function_exists('gzdeflate')) {
		return array(SANITY_FINE,gt('Passed'));
	} else {
		return array(SANITY_ERROR,gt('Failed, PHP zlib not enabled'));
	}
}

function _sanity_checkZip() {
	if (extension_loaded('zip')) {
		return array(SANITY_FINE,gt('Passed'));
	} else {
        return array(SANITY_WARNING,gt('No Zip file support for installing Extensions, PHP zip extension needed'));
	}
}

function _sanity_checkcURL() {
	if (function_exists('curl_init')) {
		return array(SANITY_FINE,gt('Passed'));
	} else {
		return array(SANITY_ERROR,gt('Failed, PHP curl extension needed'));
	}
}

function _sanity_checkFileinfo() {
	if (function_exists('finfo_open')) {
		return array(SANITY_FINE,gt('Passed'));
	} else {
        return array(SANITY_WARNING,gt('No FileInfo Support, PHP finfo extension needed'));
	}
}

function _sanity_checkUploadSize() {
	if ((int)(ini_get('upload_max_filesize'))==(int)(ini_get('post_max_size'))) {
		return array(SANITY_FINE,gt('Upload size limit').': '.expCore::maxUploadSize());
	} else {
        return array(SANITY_WARNING,gt('php.ini \'"post_max_size\' and \'upload_max_filesize\' don\'t match').': '.ini_get('upload_max_filesize'));
	}
}

function _sanity_checkXML() {
	if (function_exists('xml_parser_create')) {
		return array(SANITY_FINE,gt('Passed'));
	} else {
		return array(SANITY_WARNING,gt('Failed, PHP XML not enabled'));
	}
}

//function _sanity_checkSafeMode() {
//	if (ini_get('safe_mode') == 1) {
//		return array(SANITY_WARNING,gt('Failed'));
//	} else {
//		return array(SANITY_FINE,gt('Passed'));
//	}
//}

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

?>

