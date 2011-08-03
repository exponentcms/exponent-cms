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

/* exdoc
 * The definition of this constant lets other parts
 * of the system know that the Info Subsystem
 * has been included for use.
 * @node Subsystems:Info
 */
//define("SYS_INFO",1);

/* exdoc
 * Looks through the subsystems/ directory for a *.info.php for
 * a given subsystem, and returns the metadata stored in that file.
 * Returns an array of meta tag/values for the subsystem.  Returns
 * null if no information file was found.
 *
 * @param string $subsystem The name of the subsystem to retrieve information about.
 * @node Subsystems:Info
 */
function exponent_info_subsystemInfo($subsys) {
	if (!is_readable(BASE."subsystems/$subsystem.info.php")) return null;
	return include(BASE."subsystems/$subsystem.info.php");
}

/* exdoc
 * Looks through the subsystems/ directory for all subsystems currently installed,
 * and retrieves their information. Returns an array of all subsystems, with meta
 * tags/values for each.
 * @node Subsystems:Info
 */
function exponent_info_subsystems() {
	$info = array();
	
	$dir = BASE."subsystems";
	if (is_readable($dir)) {
		$dh = opendir($dir);
		while (($file = readdir($dh)) !== false) {
			if (is_readable("$dir/$file") && substr($file,-9,9) == ".info.php") {
				$info[substr($file,0,-9)] = include("$dir/$file");
			}
		}
	}
	return $info;
}

/* exdoc
 * Looks for a manifest file, which contains a list of all files
 * claimed by the given extension.  This list also contains the
 * cached file checksums, for verification purpses.  Returns an
 * array or a string.  If no manifest file is found, or the specified
 * extension was not found, a string error is returned. otherwise an
 * array of files information is returned.
 *
 * MD5 checksums are used to verify file integrity.
 *
 * @param integer $type The type of extension.
 * @param string $name The name of the extension
 * @node Subsystems:Info
 */
function exponent_info_files($type,$name) {
	$dir = '';
	$file = 'manifest.php';
	$autofile = 'manifest.auto.php';
	switch ($type) {
		case CORE_EXT_MODULE:
			$dir = BASE.'modules/'.$name;
			break;
		case CORE_EXT_THEME:
			$dir = BASE.'themes/'.$name;
			break;
		case CORE_EXT_SUBSYSTEM:
			$dir = BASE."subsystems";
			$file = $name.'.manifest.php';
			$autofile = $name.'.auto.manifest.php';
			break;
		default:
			echo 'Bad type: '.$type;
	}
	
	if (is_readable($dir.'/'.$autofile)) {
		return include($dir.'/'.$autofile);
	} else if (is_readable($dir.'/'.$file)) {
		return include($dir.'/'.$file);
	} else if (!is_readable($dir)) {
		return 'No such extensions ('.$name.')';
	} else {
		return 'Manifest file not found.';
	}
}

/* exdoc
 * Generates an MD5 file checksum of each file in the passed array,
 * and returns a new array of the checksums.  Returns the checksums
 * for the passed files.  Each checksum is indexed by the file it belongs to.
 *
 * @param array $files An array of file names to generate checksums for
 * @node Subsystems:Info
 */
function exponent_info_fileChecksums($files) {
	$newfiles = array();
	foreach (array_keys($files) as $file) {
        if (file_exists($file)) 
        {
		    if (is_int($files[$file])) 
                $newfiles[$file] = "";
		    else 
                $newfiles[$file] = md5_file(BASE.$file);
        }
    }
	return $newfiles;
}

/* exdoc
 * Highlight a file and show line numbering.  Slightly bastardized by James, for Exponent
 *
 * @param        string  $data       The string to add line numbers to
 * @param        bool    $funclink   Automatically link functions to the manual
 * @param        bool    $return     return or echo the data
 * @author       Aidan Lister <aidan@php.net>
 * @node Subsystems:Info
 */
function exponent_info_highlightPHP($data, $return = true)
{
    // Init
	ob_start();
	highlight_string($data); // for better compat with PHP < 4 . 20
	$contents = ob_get_contents();
	ob_end_clean();
	
    $data = explode ('<br />', $contents);
    $start = '<span style="color: black;">';
    $end   = '</span>';
    $i = 1;
    $text = '';

    // Loop
    foreach ($data as $line) {
		$spacer = str_replace(" ","&nbsp;",str_pad("",5-strlen($i."")));
    	$text .= $start . $i . $spacer . $end . str_replace("\n", '', $line) . "<br />\n";
	    ++$i;
    }
	
    // Return mode
    if ($return === false) {
        echo $text;
    } else {
        return $text;
    }
}

?>
