<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright 2006 Maxim Mueller
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
/** @define "BASE" "../../.." */

/* exdoc
 * The definition of this constant lets other parts
 * of the system know that the Core Subsystem
 * has been included for use.
 * @node Subsystems:Core
 */
//define("SYS_CORE",1);

/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an extension type of module.
 * @node Subsystems:Core
 */
define("CORE_EXT_MODULE",1);
/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an extension type of theme.
 * @node Subsystems:Core
 */
define("CORE_EXT_THEME",2);
/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an extension type of subsystem.
 * @node Subsystems:Core
 */
define("CORE_EXT_SUBSYSTEM",3);
/* exdoc
 * This constant can (and should) be used by other parts of the system
 * for defining and communicating an 'extension type' to represent the
 * whole system
 * @node Subsystems:Core
 */
define("CORE_EXT_SYSTEM",4);

/* exdoc
 * Creates a location object, based off of the three arguments passed, and returns it.
 *
 * @param		$mo	The module component of the location.
 * @param		$src	The source component of the location.
 * @param		$int	The internal component of the location.
 * @node Subsystems:Core
 */
function exponent_core_initializeNavigation () {
	$sections = array();
	$sections = navigationmodule::levelTemplate(0,0);
	return $sections;
}

function exponent_core_makeLocation($mod=null,$src=null,$int=null) {
	$loc = null;
	$loc->mod = ($mod ? $mod : "");
	$loc->src = ($src ? $src : "");
	$loc->int = ($int ? $int : "");
	return $loc;
}

/* exdoc
 * Resolve dependencies for an extension, by looking at the appropriate deps.php file.
 *
 * @param string $ext_name The name of the extension.
 * @param Constant $ext_type The type of extension.  This can be one of the following values:
 *	<ul>
 *		<li>CORE_EXT_SUBSYSTEM</li>
 *		<li>CORE_EXT_THEME</li>
 *		<li>CORE_EXT_MODULE</li>
 *	<ul>
 * @node Subsystems:Core
 */
function exponent_core_resolveDependencies($ext_name,$ext_type,$path=null) {
	if ($path == null) {
		$path = BASE;
	}
	$depfile = '';
	switch ($ext_type) {
		case CORE_EXT_SUBSYSTEM:
			$depfile = $path.'subsystems/'.$ext_name.'.deps.php';
			break;
		case CORE_EXT_THEME:
			$depfile = $path.'themes/'.$ext_name.'/deps.php';
			break;
		case CORE_EXT_MODULE:
			$depfile = $path.'modules/'.$ext_name.'/deps.php';
			break;
		case CORE_EXT_SYSTEM:
			$depfile = $path.'deps.php';
			break;
	}
	
	$deps = array();
	if (is_readable($depfile)) {
		$deps = include($depfile);
		foreach ($deps as $info) {
			$deps = array_merge($deps,exponent_core_resolveDependencies($info['name'],$info['type']));
		}
	}
	
	return $deps;
}

/* exdoc
 * Return a full URL, given the desired querystring arguments as an associative array.
 *
 * This function does take into account the SEF URLs settings and the SSL urls in the site config.
 *
 * @param Array $params An associative array of the desired querystring parameters.
 * @node Subsystems:Core
 */
function exponent_core_makeLink($params,$type='',$sef_name='') {
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//   Now that we have the router class, this function is here for compatability reasons only.
	//   it will most likey be deprecated in newer releases of exponent.
	//
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $router;
	
	// this is here for compatability with the navigation module and the old way make link used prior
	// to having the router class
	$params['sef_name'] = $sef_name;  

	// now that we have the router class we'll use it to build the link and then return it.
	return $router->makeLink($params);
}

function exponent_core_makeRSSLink($params) {
	$link = (ENABLE_SSL ? NONSSL_URL : URL_BASE);
	
	//FIXME: Hardcoded controller stuff!!
	if (controllerExists($params['module'])) {
	    $link .= SCRIPT_RELATIVE . "site_rss.php" . "?";
	} else {
	    $link .= SCRIPT_RELATIVE . "rss.php" . "?";
	}
	
	foreach ($params as $key=>$value) {
        $value = chop($value);
        $key = chop($key);
        if ($value != "") $link .= urlencode($key)."=".urlencode($value)."&";
    }
    $link = substr($link,0,-1);
    return htmlspecialchars($link,ENT_QUOTES);
}

function exponent_core_makePodcastLink($params) {
	$link = (ENABLE_SSL ? NONSSL_URL : URL_BASE);
	//FIXME: Hardcoded controller stuff!!
	if (controllerExists($params['module'])) {
//	    $link .= SCRIPT_RELATIVE . "site_podcast.php" . "?";
	    $link .= SCRIPT_RELATIVE . "site_rss.php" . "?";
	} else {
//	    $link .= SCRIPT_RELATIVE . "podcast.php" . "?";
	    $link .= SCRIPT_RELATIVE . "rss.php" . "?";
	}
	foreach ($params as $key=>$value) {
        $value = chop($value);
        $key = chop($key);
        if ($value != "") $link .= urlencode($key)."=".urlencode($value)."&";
    }
    $link = substr($link,0,-1);
    return htmlspecialchars($link,ENT_QUOTES);
}
/* exdoc
 * Return a full URL, given the desired querystring arguments as an associative array.
 *
 * This function does take into account the SEF URLs settings and the SSL urls in the site config,
 * and uses the SSL url is the site is configured to use SSL.  Otherwise, it works exactly like
 * exponent_core_makeLink.
 *
 * @param Array $params An associative array of the desired querystring parameters.
 * @node Subsystems:Core
 */
function exponent_core_makeSecureLink($params) {
	global $router;

        // this is here for compatability with the navigation module and the old way make link used prior
        // to having the router class
        $params['sef_name'] = $sef_name;

        // now that we have the router class we'll use it to build the link and then return it.
        return $router->makeLink($params, false, true);
/*
	if (!ENABLE_SSL) return exponent_core_makeLink($params);
	$link = SSL_URL .  SCRIPT_RELATIVE . SCRIPT_FILENAME . "?";
	foreach ($params as $key=>$value) {
		$value = chop($value);
		$key = chop($key);
		if ($value != "") $link .= urlencode($key)."=".urlencode($value)."&";
	}
	$link = substr($link,0,-1);
	return $link;
*/
}

/* exdoc
 * Put in place to get around the strange assignment
 * semantics in PHP5 (assign by ref not value)
 * @param Object $o The object to copy.  An exact duplicate of this will be returned.
 * @node Subsystems:Core
 */
function exponent_core_copyObject($o) {
	$new = null;
	foreach (get_object_vars($o) as $var=>$val) $new->$var = $val;
	return $new;
}

/* exdoc
 * Decrement the reference counts for a given location.  This is used by the Container Module,
 * and probably won't be needed by 95% of the code in Exponent.
 *
 * @param Location $loc The location object to decrement references for.
 * @param integer $section The id of the section that the location exists in.
 * @node Subsystems:Core
 */
function exponent_core_decrementLocationReference($loc,$section) {
	global $db;
//	$oldLocRef = $db->selectObject("locationref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."'");
	$oldSecRef = $db->selectObject("sectionref", "module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
	
//	$oldLocRef->refcount -= 1;
	$oldSecRef->refcount -= 1;
	
//	$db->updateObject($oldLocRef,"locationref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."'");
	$db->updateObject($oldSecRef,"sectionref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
}

/* exdoc
 * Increment the reference counts for a given location.  This is used by the Container Module,
 * and probably won't be needed by 95% of the code in Exponent.
 *
 * @param Location $loc The location object to increment references for.
 * @param integer $section The id of the section that the location exists in.
 * @node Subsystems:Core
 */
function exponent_core_incrementLocationReference($loc,$section) {
	global $db;
//	 $newLocRef = $db->selectObject("locationref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."'");
//	 $is_new = false; // For the is_original sectionref attribute
//	 if ($newLocRef != null) {
//		 // Pulled an existing source.  Update refcount
//		 $newLocRef->refcount += 1;
//		 $db->updateObject($newLocRef,"locationref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."'");
//	 } else {
//		 $is_new = true;
//		 // New source.  Populate reference
//		 $newLocRef->module   = $loc->mod;
//		 $newLocRef->source   = $loc->src;
//		 $newLocRef->internal = $loc->int;
//		 $newLocRef->refcount = 1;
//		 $db->insertObject($newLocRef,"locationref");
//
//		 // Go ahead and assign permissions on contained module.
//		 if ($loc->mod != 'navigationmodule' && $loc->mod != 'administrationmodule') {
//			 //$perms = call_user_func(array($loc->mod,"permissions"));
//			 $mod = new $loc->mod();
//			 $perms = $mod->permissions();
//			 global $user;
//			 foreach (array_keys($perms) as $perm) {
//				 exponent_permissions_grant($user,$perm,$loc);
//			 }
//		 }
//		 exponent_permissions_triggerSingleRefresh($user);
//	 }
	
	$newSecRef = $db->selectObject("sectionref", "module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
	if ($newSecRef != null) {
		// Pulled an existing source for this section.  Update refcount
		$newSecRef->refcount += 1;
		$db->updateObject($newSecRef,"sectionref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
	} else {
		// New source for this section.  Populate reference
		$newSecRef->module   = $loc->mod;
		$newSecRef->source   = $loc->src;
		$newSecRef->internal = $loc->int;
		$newSecRef->section = $section;
		$newSecRef->refcount = 1;
//		$newSecRef->is_original = ($is_new ? 1 : 0);
		$newSecRef->is_original = 1;
		$db->insertObject($newSecRef,"sectionref");
	}
}

/* exdoc
 * Return a string of the current version number.
 *
 * @param bool $full Whether or not to return a full verison number.  If passed as true,
 *	a string in the form of '0.96.3-beta5' will be returned.  Otherwise, '0.96' would be returned.
 * @param bool $build Whether or not to return the build date in the string.
 * @node Subsystems:Core
 */
function exponent_core_version($full = false, $build = false) {
	if (!defined("EXPONENT_VERSION_MAJOR")) include_once(BASE."exponent_version.php");
	$vers = EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR;
	if ($full) {
		$vers .= ".".EXPONENT_VERSION_REVISION;
		if (EXPONENT_VERSION_TYPE != "") $vers .= "-".EXPONENT_VERSION_TYPE.EXPONENT_VERSION_ITERATION;
	}
	if ($build) {
		$vers .= " (Build Date: ".strftime("%D",EXPONENT_VERSION_BUILDDATE).")";
	}
	return $vers;
}

/* exdoc
 * This function checks a full URL against a set of
 * known protocls (like http and https) and determines
 * if the URL is valid.  Returns true if the URL is valid,
 * and false if otherwise.
 *
 * @param string $url The URL to test for validity
 * @node Subsystems:Core
 */
function exponent_core_URLisValid($url) {
	return (
		substr($url,0,7) == "http://" ||
		substr($url,0,8) == "https://" ||
		substr($url,0,7) == "mailto:" ||
		substr($url,0,6) == "ftp://"
	);
}

/* exdoc
 * Generates and returns a message stating the current maximum accepted size of
 * uploaded files.  It intelligently parses the php.ini configuration, so that settings of
 * 2K and 2048 are treated identically.
 * @node Subsystems:Core
 */
function exponent_core_maxUploadSizeMessage() {
	$size = ini_get("upload_max_filesize");
	$size_msg = "";
	$type = substr($size,-1,1);
	$shorthand_size = substr($size,0,-1);
	switch ($type) {
		case 'M':
			$size_msg = $shorthand_size . ' MB';
			break;
		case 'K':
			$size_msg = $shorthand_size . ' kB';
			break;
		case 'G': // PHP5 +
			$size_msg = $shorthand_size . ' GB';
			break;
		default:
			if ($size >= 1024*1024*1024) { // Gigs
				$size_msg = round(($size / (1024*1024*1024)),2) . " GB";
			} else if ($size >= 1024*1024) { // Megs
				$size_msg = round(($size / (1024*1024)),2) . " MB";
			} else if ($size >= 1024) { // Kilo
				$size_msg = round(($size / 1024),2) . " kB";
			} else {
				$size_msg = $size . " bytes";
			}
	}
	$i18n = exponent_lang_loadFile('subsystems/core.php');
	return sprintf($i18n['max_upload'],$size_msg);
}

/* exdoc
 * This function converts an absolute path, such as the one provided
 * by the exponent_core_resolveFilePaths() function into a relative one.
 * 
 * This is useful if the file is not to be included server-
 * but loaded client-side
 *
 * @param string $inPath The absolute file path
 * @node Subsystems:Core
 */
function exponent_core_abs2rel($inPath) {
	//TODO: Investigate the chances of BASE occurring more than once
	$outPath = str_replace(BASE, PATH_RELATIVE, $inPath);
	return $outPath;
}


//helper function
function glob2keyedArray($workArray){
	$temp = array();
	foreach($workArray as $myWorkFile){
		$temp[basename($myWorkFile)] = $myWorkFile;
	}
	return $temp;
}


/* exdoc
 * This function finds the most appropriate version of a file
 *  - if given wildcards, files -
 * and returns an array with the file's physical location's full path or,
 * if no file was found, false
 *
 * @param string $type (to be superseded) type of base resource (= directory name)
 * @param string $name (hopefully in the future type named) Resource identifier (= class name = directory name)
 * @param string $subtype type of the actual file (= file extension = (future) directory name)
 * @param string $subname name of the actual file (= filename name without extension)
 * 
 * @node Subsystems:Core
 */
function exponent_core_resolveFilePaths($type, $name, $subtype, $subname) {
	//TODO: implement caching
	//TODO: optimization - walk the tree backwards and stop on the first match
   // eDebug($type);
   // eDebug($name);
   // eDebug($subtype);
   // eDebug($subname);
	//once baseclasses are in place, simply lookup the baseclass name of an object
	if($type == "guess") {
		// new style name processing
		//$type = array_pop(preg_split("*(?=[A-Z])*", $name));

		//TODO: convert everything to the new naming model
		if(stripos($name, "module") != false){
			$type = "modules";
		} else if (stripos($name, "control") != false) {
			$type = "controls";
		} else if (stripos($name, "theme") != false) {
			$type = "themes";
		}
	}
	
	// convert types into paths
	$relpath = '';
	if ($type == "modules" || $type == 'profileextension') {
			$relpath .= "framework/modules-1/";
		} elseif($type == "controllers") {
			$relpath .= "framework/views/";
		} elseif($type == "forms") {
			if ($name != "forms/email") {
				$relpath .= "subsystems/forms/";
			} else {  //TODO  forms/email only used by calendarmodule
				$relpath .= "framework/modules-1/calendarmodule/";
			}
		} elseif($type == "themes") {
			$relpath .= "themes/";
		} elseif($type == "models") {
			$relpath .= "models/";
		} elseif($type == "controls") {
			$relpath .= "themes/";
		} elseif($type == "Control") {
			$relpath .= "themes/";
		} elseif($type == "Form") {
			$relpath .= "subsystems/forms/";
		} elseif($type == "Module") {
			$relpath .= "modules/";
		} elseif($type == "Controller" || $type=='controllers') {
			$relpath .= "framework/views/";
		} elseif($type == "Theme") {
			$relpath .= "themes/";
		}
	
	// for later use for searching in lib/common
	$typepath = $relpath;
	if ($name != "" && $name != "forms/email") {  //TODO  forms/email only used by calendarmodule
		$relpath .= $name . "/";
	}

	// for later use for searching in lib/common
	$relpath2 = '';
	if ($subtype == "css") {
			$relpath2 .= "css/";
		} elseif($subtype == "js") {
			$relpath2 .= "js/";
		} elseif($subtype == "tpl") {
			if ($type == 'controllers' || $type == 'Controller') {
				//do nothing
			} elseif ($name == "forms/email") {  //TODO  forms/email only used by calendarmodule
//				$relpath2 .= "/";
				$relpath2 .= "forms/email/";
		    } elseif ($type == 'controls' || $type == 'Control') {
		        $relpath2 .= 'editors/';
		    } elseif ($type == 'profileextension') {
				$relpath2 .= "extensions/";
			} elseif ($type == 'globalviews') {
				$relpath2 .= "framework/core/views/";
			} else {
				$relpath2 .= "views/";
			}
		} elseif($subtype == "form") {
			$relpath2 .= "views/"; 
		} elseif($subtype == "action") {
			$relpath2 .= "actions/";
			//HACK: workaround for now
			$subtype = "php";
		}
	
	$relpath2 .= $subname;
	if($subtype != "") {
		$relpath2 .= "." . $subtype;
	}

	$relpath .= $relpath2;
	
	//TODO: handle subthemes
	//TODO: now that glob is used build a syntax for it instead of calling it repeatedly
	//latter override the precursors
	$locations = array(BASE, THEME_ABSOLUTE);
	foreach($locations as $location) {
		$checkpaths[] = $location . $typepath . "common/" . $relpath2;
		if (strstr($location,DISPLAY_THEME_REAL) && strstr($relpath,"framework/modules-1")) {
   		$checkpaths[] = $location . str_replace("framework/modules-1", "modules", $relpath);
		} else {
   		$checkpaths[] = $location . $relpath;
		}
		//eDebug($relpath);
	}
	//eDebug($checkpaths);

	//TODO: handle the - currently unused - case where there is 
	//the same file in different $type categories 
	$myFiles = array();
	foreach($checkpaths as $checkpath) {
   	//eDebug($checkpath);
		$tempFiles = glob2keyedArray(glob($checkpath));
		if ($tempFiles != false) {
			$myFiles = array_merge($myFiles, $tempFiles);
		}
	}
	if(count($myFiles) != 0) {
		return array_values($myFiles);
	} else {
		//TODO: invent better error handling, maybe an error message channel ?
		//die("The file " . basename($filepath) . " could not be found in the filesystem");
		return false;
	}
		
}

/* exdoc
 * This function is a wrapper around exponent_core_resolveFilePaths()
 * and returns a list of the basenames, minus the fileextensions - if any 
 *
 * @param string $type (to be superceeded) type of base ressource (= directory name)
 * @param string $name (hopefully in the future type named) Ressource identifier (= class name = directory name)
 * @param string $subtype type of the actual file (= file extension = (future) directory name)
 * @param string $subname name of the actual file (= filename name without extension)
 * 
 * @node Subsystems:Core
 */
function exponent_core_buildNameList($type, $name, $subtype, $subname) {
	$nameList = array();
	$fileList = exponent_core_resolveFilePaths($type, $name, $subtype, $subname);
	if ($fileList != false) {
		foreach ($fileList as $file) {
			// exponent_core_resolveFilePaths() might also return directories
			if (basename($file) != "") {
				// just to make sure: do we have an extension ?
				// relying on there is only one dot in the filename
				$extension = strstr(basename($file), ".");
				$nameList[basename($file, $extension)] = basename($file, $extension);
			} else {
				// don't know where this might be needed, but...
				$nameList[] = array_pop(explode("/", $file));
			}
		}
	}
	return $nameList;
}

function exponent_core_getCurrencySymbol($currency_type) {
	switch ($currency_type) {
        	case "USD":
                	return "$";
                        break;
                case "CAD":
                case "AUD":
                        return "$";
                        break;
                case "EUR":
                        return "&euro;";
                        break;
                case "GBP":
                        return "&#163;";
                        break;
                case "JPY":
                        return "&#165;";
                break;
                default:
                        return "$";
       }
}
?>
