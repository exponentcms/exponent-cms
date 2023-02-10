#!/usr/bin/env php
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

/**
 * ------------------------------------------------------------------------- *
 * This library is free software; you can redistribute it and/or             *
 * modify it under the terms of the GNU Lesser General Public                *
 * License as published by the Free Software Foundation; either              *
 * version 2.1 of the License, or (at your option) any later version.        *
 *                                                                           *
 * This library is distributed in the hope that it will be useful,           *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU         *
 * Lesser General Public License for more details.                           *
 *                                                                           *
 * You should have received a copy of the GNU Lesser General Public          *
 * License along with this library; if not, write to the Free Software       *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA *
 * ------------------------------------------------------------------------- *
 *
 * lang_extract.php - extracts gettext strings from ExponentCMS files
 * adapted from http://smarty-gettext.sf.net/  Sagi Bashari <sagi@boom.org.il>
 *
 * Requires the 'BING_API' constant be set to the API code for your Bing account
 *
 * Usage:
 * ./lang_extract.php <filename or directory> <file2> <..>
 *
 * If a parameter is a directory, the files within will be parsed.
 *
 */

if (!defined('DEVELOPMENT'))
	define('DEVELOPMENT','1');
define('WRITE_LANG_TEMPLATE', DEVELOPMENT);

// Initialize the exponent environment
include_once('../exponent_bootstrap.php');
if (!defined('DISPLAY_THEME')) {
	/** exdoc
	 * The directory and class name of the current active theme.  This may be different
	 * than the configured theme (DISPLAY_THEME_REAL) due to previewing.
	 */
	define('DISPLAY_THEME',DISPLAY_THEME_REAL);
}

if (!defined('THEME_ABSOLUTE')) {
	/** exdoc
	 * The absolute path to the current active theme's files.  This is similar to the BASE constant
	 */
	define('THEME_ABSOLUTE',BASE.'themes/'.DISPLAY_THEME.'/'); // This is the recommended way
}

// Initialize the language subsystem
expLang::initialize();
global $default_lang, $cur_lang;
if (empty($default_lang))
	$default_lang = include(BASE."framework/core/lang/English - US.php");

// regex for the gettext smarty modifier
$regex_gettext_mod='/(?<=["\'])((\\\\.|[^\'"])*)(?=["\']\|gettext)/';

// regex for the gettxtlist smarty modifier
$regex_gettxtlist_mod='/(?<=["\'])((\\\\.|[^\'"])*)(?=["\']\|gettxtlist)/';

// regex for the gettext smarty function
$regex_gettext_func='/(?<=gettext str=[\'"])((\\\\.|[^\'"])*)([^}]*)(?=[\'"]\})/';

// regex for the gettext gt shortcut function
$regex_gt='/(?<=gt\([\'"])((\\\\.|[^\'"])*)(?=[\'"]\))/';

// regex for the gettext glist shortcut function
$regex_glist='/(?<=glist\([\'"])((\\\\.|[^\'"])*)(?=[\'"]\))/';

// extensions of files, used when going through a directory
$extensions = array('tpl','php');

$recur = true;

$custom = false; // for themes

$total_new = 0;

// "fix" string - strip slashes, escape and convert new lines to \n
function fs($str) {
	$str = stripslashes($str);
	$str = str_replace('"', '\"', $str);
	$str = str_replace("\n", '\n', $str);
	return $str;
}

// rips gettext strings from $file and prints them in C format
function do_extract($file, $regex, $isalist=false) {
    global $total_new;

    $content = @file_get_contents($file);
   	if (empty($content))
   		return;
    preg_match_all(
           $regex,
           $content,
           $matches,
           PREG_PATTERN_ORDER
   	);
    print "$file" . " - ";
    $num_added = 0;
	for ($i = 0, $iMax = count($matches[0]); $i < $iMax; $i++) {
        str_replace('"', "\'", $matches[0][$i]);  // remove the killer double-quotes
        if ($isalist) {
            $phrases = explode(",",$matches[0][$i]);
            foreach ($phrases as $phrase) {
                expLang::writeTemplate(trim($phrase));
                $num_added++;
            }
        } else {
            expLang::writeTemplate($matches[0][$i]);
            $num_added++;
        }
   	}
    $total_new += $num_added;
    print $num_added."\n";
}

// processes file for assoc strings
function do_file($file, $fileext) {
	global $regex_gt, $regex_gettext_mod, $regex_gettxtlist_mod, $regex_gettext_func, $regex_glist;

    if ($fileext === 'tpl') {
        do_extract($file,$regex_gettext_mod);
        do_extract($file,$regex_gettxtlist_mod,true);
//        do_extract($file,$regex_gettext_func);  //FIXME these tend to hold computations and likewise break things?
    } elseif ($fileext === 'php') {
        do_extract($file,$regex_gt);
        do_extract($file,$regex_glist,true);
    }
}

// go through a directory
function do_dir($dir) {
    global $extensions, $recur;

	$d = dir($dir);

	while (false !== ($entry = $d->read())) {
		if ($entry === '.' || $entry === '..') {
			continue;
		}

		$entry = $dir.'/'.$entry;

		if (is_dir($entry)) { // if a directory, go through it
			if ($recur) do_dir($entry);
		} else { // if file, parse only if extension is matched
			$pi = pathinfo($entry);
			if (empty($pi['extension'])) $pi['extension'] = null;
			if (isset($pi['extension']) && in_array($pi['extension'], $extensions)) {
				do_file($entry,$pi['extension']);
			}
		}
	}

	$d->close();
}

for ($ac=1; $ac < $_SERVER['argc']; $ac++) {
    print "Extracting Language Phrases\n";
	if ($_SERVER['argv'][$ac] === '-r') {
		$recur = false;
	} elseif ($_SERVER['argv'][$ac] === '-t'){
		$custom = true;
    } elseif (is_dir($_SERVER['argv'][$ac])) { // go through directory
		do_dir($_SERVER['argv'][$ac]);
	} else { // do file
        $pi = pathinfo($_SERVER['argv'][$ac]);
        if (empty($pi['extension'])) $pi['extension'] = null;
		do_file($_SERVER['argv'][$ac],$pi['extension']);
	}
    print "\nCompleted Extracting ".$total_new." Total Phrases!\n\n";
}

?>
