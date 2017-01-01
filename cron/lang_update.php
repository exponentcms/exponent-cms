#!/usr/bin/env php
<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * lang_update.php - attempts to auto-update all ExponentCMS language files
 * by running the other two language scripts lang_extract.php & lang_translate.php
 */

output("Updating the Exponent Language System!\n");

$trans_only = false;
for ($ac=1; $ac < $_SERVER['argc']; $ac++) {
	if ($_SERVER['argv'][$ac] == '-t'){
        $trans_only = true;  // only do translation, NO phrase extraction
	} else { // set translation type
        if (!defined('TRANSLATE')) {
            define('TRANSLATE', $_SERVER['argv'][$ac]);
        }
	}
}
if (!defined('TRANSLATE')) {
    define('TRANSLATE', '');
}

if (!$trans_only) {
// Update the default phrase library by extracting the English phrases
    output("Extracting phrases from the root folder!\n");
    exec('php ./lang_extract.php -r ..', $output);
    output($output);
    unset ($output);
    output("Now extracting phrases from the folders!\n");
    exec('php ./lang_extract.php ../cron ../framework ../install', $output);
    output($output);
    unset ($output);
}

//Update each language file based on default language and then attempt to translate

// Initialize the exponent environment
include_once('../exponent_bootstrap.php');
if (!defined('DISPLAY_THEME')) {
	/* exdoc
	 * The directory and class name of the current active theme.  This may be different
	 * than the configured theme (DISPLAY_THEME_REAL) due to previewing.
	 */
	define('DISPLAY_THEME',DISPLAY_THEME_REAL);
}

if (!defined('THEME_ABSOLUTE')) {
	/* exdoc
	 * The absolute path to the current active theme's files.  This is similar to the BASE constant
	 */
	define('THEME_ABSOLUTE',BASE.'themes/'.DISPLAY_THEME.'/'); // This is the recommended way
}

// Initialize the language subsystem
expLang::initialize();
global $default_lang, $cur_lang;
if (empty($default_lang))
    $default_lang = include(BASE."framework/core/lang/English - US.php");
$orig_lang = LANG;
$lang_list = expLang::langList();
output("Now Translating ".count($default_lang)." Unique Phrases!\n");

//exit();

foreach ($lang_list as $key=>$value) {
    if (!empty($key) && $key!="English - US") {
        output("Now attempting to translate new ".$key." phrases\n");
        expSettings::change('LANGUAGE', $key);
        exec ('php ./lang_translate.php', $output);
        output($output);
        unset ($output);
    }
}
expSettings::change('LANGUAGE', $orig_lang);

print "\nCompleted Updating the Exponent Language System!\n";

function output($text) {
    if (!is_array($text))
        $text = array($text);
    foreach ($text as $string) {
        print $string."\n";
    }
}
?>
