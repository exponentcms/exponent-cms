<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expLang class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Phillip Ball <phillip@oicgroup.net>
 * @version 2.0.0
 */
/** @define "BASE" "../../.." */

/**
 * This is the class expLang
 *
 * @subpackage Core-Subsystems
 * @package Framework
 */
class expLang {
    
    public static function loadLang() {
	    if (!defined('LANGUAGE')) define('LANGUAGE', 'English - US');
		if (!defined('LANG')) {  // LANG is needed by YUI
			if ((is_readable(BASE . 'framework/core/lang/' . LANGUAGE . '.php')) && (LANGUAGE != 'English - US')) {
				define('LANG', LANGUAGE); // Lang file exists.
			} else {
				define('LANG', 'English - US'); // Fallback to 'English - US' if language file not present.
			}
		}

	    if (file_exists(BASE . 'framework/core/lang/' . LANGUAGE.'.info.php')) {
			$info = include(BASE . 'framework/core/lang/' . LANGUAGE.'.info.php');
			setlocale(LC_ALL, $info['locale']);
			//DEPRECATED: we no longer use views for i18n
			define('DEFAULT_VIEW', $info['default_view']);
			// For anything related to character sets:
			define('LANG_CHARSET', $info['charset']);
	    } else {
		    //DEPRECATED: we no longer use views for i18n
		    define('DEFAULT_VIEW', 'Default');
		    // For anything related to character sets:
		    define('LANG_CHARSET', 'UTF-8');
	    }

	    global $cur_lang, $default_lang, $target_lang_file;
        $default_lang = include(BASE."framework/core/lang/English - US.php");
	    //TODO the $default_lang_file should probably be the 'target' language?
        $target_lang_file = BASE."framework/core/lang/English - US.php";
        $cur_lang = include(BASE."framework/core/lang/".LANGUAGE.".php");
    }
    
	public static function gettext($str) {
	    if (!defined('LANGUAGE')) return $str;

	    global $cur_lang;
		if (DEVELOPMENT) self::writeTemplate($str);
	    $str = LANGUAGE!="English - US" && array_key_exists(addslashes($str),$cur_lang) ? stripslashes($cur_lang[addslashes($str)]) : $str;
		return $str;
	}
	
	public function writeTemplate($str) {
	    global $default_lang, $target_lang_file;
	    //!array_key_exists($str,$default_lang)
		//TODO Probably should be able to build a language file even if you are using a non-English language
		//TODO E.g., be able to dump all the new english stuff in the other language which isn't defined yet
        if ((defined("WRITE_LANG_TEMPLATE") && WRITE_LANG_TEMPLATE!=0) && LANGUAGE=="English - US") {
            $fp = fopen($target_lang_file, 'w+') or die("I could not open $target_lang_file.");
            $default_lang[addslashes($str)] = addslashes($str);
            ksort($default_lang);
            fwrite($fp,"<?php\n");
            fwrite($fp,"return array(\n");
            foreach($default_lang as $key => $value){
                fwrite($fp,"\t\"".$key."\"=>\"".$value."\",\n");
            }
            fwrite($fp,");\n");
            fwrite($fp,"?>\n");
            fclose($fp);
        }
	}

	public static function langList() {
		$dir = BASE.'framework/core/lang';
		$langs = array();
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($f = readdir($dh)) !== false) {
				if (substr($f,-4,4) == '.php' && substr($f,-9,9) != '.info.php') {
					if (file_exists($dir.'/'.substr($f,0,-4).'.info.php')) {
						$info = include($dir.'/'.substr($f,0,-4).'.info.php');
						$langs[substr($f,0,-4)] = $info['name'] . ' -- ' . $info['author'];
					} else {
						$langs[substr($f,0,-4)] = substr($f,0,-4);
					}
				}
			}
		}
		return $langs;
	}
}

?>
