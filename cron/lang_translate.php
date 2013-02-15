#!/usr/bin/env php
<?php
##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * lang_translate.php - attempts to auto-translate current ExponentCMS language (file)
 * Requires the 'BING_API' constant be set to the API code for your Bing account
 */

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
expLang::loadLang();
global $default_lang, $cur_lang;
if (empty($default_lang)) $default_lang = include(BASE."framework/core/lang/English - US.php");

if (LANGUAGE=="English - US") {
    print "You can't update the current language 'English - US' which is also the default translation!\n";
    print "Create and/or Switch to another Translation using Manage Translations!\n";
    print "Or use the 'lang_extract.php' script to add new phrases to it.\n";
    exit;
} elseif (!is_readable(BASE . 'framework/core/lang/' . utf8_decode(LANGUAGE) . '.php')) {
    print "The '".utf8_decode(LANGUAGE)."' Translation doesn't seem to exist yet!\n";
    print "You must first Create and/or Switch to this Translation using Manage Translations!\n";
    exit;
}
print "Updating ".utf8_decode(LANG)." Translation\n";
print count($cur_lang)." Phrases in the ".utf8_decode(LANG)." Translation\n";

// Add new/missing phrases in current language
$num_missing = 0;
foreach ($default_lang as $key => $value) {
    if (!array_key_exists($key,$cur_lang)) $num_missing++;
}
$changes = expLang::updateCurrLangFile();
$changes = $changes?$changes:'No';
print $changes." New Phases were Added to the ".utf8_decode(LANG)." Translation\n";

// Remove Obsolete phrases from current language
$num_extra = 0;
foreach ($cur_lang as $key => $value) {
    if (!array_key_exists($key,$default_lang)) {
        unset($cur_lang[$key]);
        expLang::saveCurrLangFile();
        $num_extra++;
    }
}
$num_extra = $num_extra?$num_extra:'No';
print $num_extra." Obsolete Phases were Found and Removed from the ".utf8_decode(LANG)." Translation\n";

// Attempt a machine translation for un-translated phrases in current language
$num_untrans = 0;
foreach ($cur_lang as $key => $value) {
    if ($key == $value) $num_untrans++;
}
print $num_untrans." Phrases appear Un-Translated in the ".utf8_decode(LANG)." Translation\n";
$num_added = 0;
if (defined('LOCALE')) {
    foreach ($cur_lang as $key => $value) {
        if ($key == $value) {
            $translation = expLang::translate($value,'en',LOCALE);
            if ($translation) {
                $translation = str_replace('"', "\'", $translation);  // remove the killer double-quotes
                $cur_lang[$key] = addslashes(stripslashes(strip_tags($translation)));
                expLang::saveCurrLangFile();
                $num_added++;
            }
        }
    }
    print $num_added." New Phases were Translated in the ".utf8_decode(LANG)." Translation\n";
} else {
    print "There is no Locale Assigned for the ".utf8_decode(LANG)." to attempt a Translation\n";
    exit;
}

print count($cur_lang)." Phrases are now in the ".utf8_decode(LANG)." Translation\n";
print "\nCompleted Updating the ".utf8_decode(LANG)." Translation!\n";

?>
