#!/usr/bin/env php
<?php
/**
 * lang_update.php - attempts to auto-update all ExponentCMS language files
 * by running the other two language scripts lang_extract.php & lang_translate.php
 */

output("Updating the Exponent Language System!\n");

// Update the default phrase library by extracting the English phrases
output("Now extracting phrases from the root folder!\n");
exec ('php ./lang_extract.php -r ..',$output);
output($output);
unset ($output);
output("Now extracting phrases from the folders!\n");
exec ('php ./lang_extract.php ../conf ../cron ../framework ../install ../themes', $output);
output($output);
unset ($output);

//Update each language file based on default language and then attempt to translate
// Initialize the exponent environment and language subsystem
include_once('../exponent_bootstrap.php');
expLang::loadLang();
global $default_lang, $cur_lang;
if (empty($default_lang)) $default_lang = include(BASE."framework/core/lang/English - US.php");
$orig_lang = LANG;
$lang_list = expLang::langList();
foreach ($lang_list as $key=>$value) {
    if ($key!="English - US") {
        output("Now attempting to translate new ".$key." phrases\n");
        expSettings::change('LANGUAGE', $key);
        exec ('php ./lang_translate.php',$output);
        output($output);
        unset ($output);
    }
}
expSettings::change('LANGUAGE', $orig_lang);

print "\nCompleted Updating the Exponent Language System!\n";

function output($text) {
    if (!is_array($text)) $text = array($text);
    foreach ($text as $string) {
        print $string."\n";
    }
}
?>
