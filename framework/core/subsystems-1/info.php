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
/** @define "BASE" "../.." */

//define('SYS_LANG',1);

/*
define('SYS_LANG_MODULE',	1);
define('SYS_LANG_VIEW',		2);
define('SYS_LANG_ACTION',	3);
*/

function exponent_lang_list() {
	$dir = BASE.'framework/core/subsystems-1/lang';
	$langs = array();
	if (is_readable($dir)) {
		$dh = opendir($dir);
		while (($f = readdir($dh)) !== false) {
			if (substr($f,-4,4) == '.php') {
				$info = include($dir.'/'.$f);
				$langs[substr($f,0,-4)] = $info['name'] . ' -- ' . $info['author'];
			}
		}
	}
	return $langs;
}

function exponent_lang_initialize() {
	if (!defined('LANG')) {
		if ((is_readable(BASE . 'framework/core/subsystems-1/lang/' . USE_LANG . '.php')) && (USE_LANG != 'en')) {
			define('LANG', USE_LANG); // Lang file exists.
		} else {
			define('LANG', 'eng_US'); // Fallback to 'eng_US' if language file not present.
		}
	}

	$info = include(BASE . 'framework/core/subsystems-1/lang/' . LANG.'.php');
	setlocale(LC_ALL, $info['locale']);
	//DEPRECATED: we no longer use views for i18n
	define('DEFAULT_VIEW', $info['default_view']);
	// For anything related to character sets:
	define('LANG_CHARSET', $info['charset']);
}

function exponent_lang_loadLangs() {
	$ret = array();
	if (is_readable(BASE.'framework/core/subsystems-1/lang')) {		
		while (($lang_file = readfile(BASE . 'framework/core/subsystems-1/lang/*.php')) !== false) {
			if (is_readable($lang_file)) {
				$ret = include($lang_file);
			}
		}
	}	
	return $ret;
}

/*
 * Load a set of language keys.
 *
 * @param string $filename The name of the file that should be internationalized.  This should
 * not start with a forward slash and well be taken relative to framework/core/subsystems-1/lang/
 *
 * @return Array The language set found, or an empty array if no set file was found.
 */
 //TODO: change api to use a global location object, which tells us module(/other types) and view, then we can do overriding cleanly
function exponent_lang_loadFile($filename) {


	//so much for having a private function :(
	//we should convert REALLY convert our API to be OO
	if (!function_exists("loadStrings")) {
		//pass-by-reference to shave off a copy operation
		function loadStrings(&$tr_array, $filepath) {
			//TODO: use GPR to allow for local overrides/extensions
			//remove $lang_dir
			//$filepath = array_pop(exponent_core_resolveFilePaths());
			if (is_readable($filepath)) {
				$tr_array = array_merge($tr_array, include($filepath));
			}
		}
	}
	

	//initialize the array to be returned
	$_TR = array();


	//set the language directory
	$lang_dir = BASE . 'framework/core/subsystems-1/lang/' . LANG;
	
	// check if the requested language file is installed
	// in that specific language
	// (an incomplete translation)
	if (!file_exists($lang_dir . "/" . $filename)) {

		// If we get to this point,
		// the preferred language file does not exist.  Try english.
		$lang_dir = BASE . 'framework/core/subsystems-1/lang/eng_US';
	}


	//load the most common strings
	loadStrings($_TR, $lang_dir . "/modules/modules.php");


	//load module specific strings
	$path_components = explode("/", $filename);
	//as the typical path will be something like modules/somemodule/views/someview.php it must be 1
	$module = array();
	if (count($path_components) > 1) {
		$module = $path_components[1];
	}
	
	loadStrings($_TR, $lang_dir . "/modules/" . $module . "/" . $module . ".php");
	

	//load the view specific strings
	loadStrings($_TR, $lang_dir . "/" . $filename);

	return $_TR;
}


/*
 * Return a single key from a language set.
 *
 * @param string $filename The name of the file that should be internationalized.  This should
 * not start with a forward slash and well be taken relative to framework/core/subsystems-1/lang/
 * @param string $key The name of the language key to return.
 *
 * @return Array The language set found, or an empty array if no set file was found.
 */
function exponent_lang_loadKey($filename, $key) {
	// First we load the full set.
	$keys = exponent_lang_loadFile($filename);

	// return either the looked-up value
	// or if non-existent
	// the key itself, so there is a visual indicator
	// of a missing translation
	if($keys[$key] != null) {
		$return_value = $keys[$key];
	} else {
		$return_value = $key;
	}
		
	return $return_value;
}

/*
 * Return a short language code from a long one, many external programs use the short ones
 * its a dumb, straight table lookup function, no fancy regexp rules.
 * It should rather be replaced by introducing a short lang code to the language descriptor files
 * and replacing the site wide CONSTANTS by global objects, which then in return
 * could have a multitude of subobjects and properties, such as long and short codes
 * 
 * @param string $long_code something like "eng_US"
 *
 * @return string the short version of the lang code
 */
function exponent_lang_convertLangCode($long_code, $target = "iso639-1") {
	//TODO: auto-guess the incoming type of lang code from the input format
	//TODO: breakout the data into a xml file in framework/core/subsystems-1/lang/

	//assume that we are getting an iso639-2_Country code for now(standard for eXp's i18n)
	switch ($long_code) {
		case "deu_DE":
			$iso639_1 = "de";
			$iso639_2 = "deu";
			break;
		case "eng_US":
			$iso639_1 = "en";
			$iso639_2 = "eng";
		break;
		default:
			$iso639_1 = "en";
			$iso639_2 = "eng";
	}
	
	//resist the temptation to do eval()
	switch ($target) {
		case "iso639-1":
			$converted_code = $iso639_1;
			break;
		case "iso639-2":
			$converted_code = $iso639_2;
			break;
	}

	return $converted_code;
}

function exponent_lang_getText($text) {
	return $text;
}
?>
