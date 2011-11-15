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
        global $cur_lang, $default_lang, $default_lang_file, $target_lang_file;

	    if (!defined('LANGUAGE')) define('LANGUAGE', 'English - US');
		if (!defined('LANG')) {  // LANG is needed by YUI
			if ((is_readable(BASE . 'framework/core/lang/' . utf8_decode(LANGUAGE) . '.php'))) {
				define('LANG', LANGUAGE); // Lang file exists.
			} else {
				define('LANG', 'English - US'); // Fallback to 'English - US' if language file not present.
			}
		}

	    if (is_readable(BASE . 'framework/core/lang/' . utf8_decode(LANG).'.info.php')) {
			$info = include(BASE . 'framework/core/lang/' . utf8_decode(LANG).'.info.php');
            define('LOCALE', $info['locale']);
			setlocale(LC_ALL, $info['locale']);
            // For anything related to character sets:
            define('LANG_CHARSET', $info['charset']);
			//DEPRECATED: we no longer use views for i18n
			define('DEFAULT_VIEW', $info['default_view']);
	    } else {
            // For anything related to character sets:
            define('LANG_CHARSET', 'UTF-8');
		    //DEPRECATED: we no longer use views for i18n
		    define('DEFAULT_VIEW', 'Default');
	    }

        if (DEVELOPMENT) $default_lang = include(BASE."framework/core/lang/English - US.php");
        $default_lang_file = BASE."framework/core/lang/English - US.php";
        $cur_lang = include(BASE."framework/core/lang/".utf8_decode(LANG).".php");
        $target_lang_file = BASE."framework/core/lang/".utf8_decode(LANG).".php";
    }
    
	public static function gettext($str) {
        global $cur_lang;

	    if (!defined('LANG')) return $str;
        str_replace('"', "\'", $str);  // remove the killer double-quotes
		if (DEVELOPMENT) self::writeTemplate($str);
	    $str = LANG!="English - US" && array_key_exists(addslashes($str),$cur_lang) ? stripslashes($cur_lang[addslashes($str)]) : $str;
		return $str;
	}
	
	public static function writeTemplate($str) {
	    global $default_lang, $default_lang_file;

        if (defined('WRITE_LANG_TEMPLATE') && WRITE_LANG_TEMPLATE && !array_key_exists(addslashes(strip_tags($str)),$default_lang)) {
            $str = stripslashes(strip_tags($str));
            $fp = fopen($default_lang_file, 'w+') or die("I could not open $default_lang_file.");
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

    public static function updateCurrLangFile() {
        global $cur_lang, $default_lang, $target_lang_file;

        if (empty($default_lang)) $default_lang = include(BASE."framework/core/lang/English - US.php");
        $num_added = 0;
        if ((is_readable($target_lang_file))) {
            $fp = fopen($target_lang_file, 'w+') or die("I could not open $target_lang_file.");
            foreach ($default_lang as $key=>$value) {
                if (!array_key_exists($key,$cur_lang)) {
                    $cur_lang[$key] = $value;
                    $num_added++;
                }
            }
            ksort($cur_lang);
            fwrite($fp,"<?php\n");
            fwrite($fp,"return array(\n");
            foreach($cur_lang as $key => $value){
               fwrite($fp,"\t\"".$key."\"=>\"".$value."\",\n");
            }
            fwrite($fp,");\n");
            fwrite($fp,"?>\n");
            fclose($fp);
        }
        return $num_added;
   	}

    public static function saveCurrLangFile() {
        global $cur_lang, $target_lang_file;

        if ((is_readable($target_lang_file))) {
            $fp = fopen($target_lang_file, 'w+') or die("I could not open $target_lang_file.");
            ksort($cur_lang);
            fwrite($fp,"<?php\n");
            fwrite($fp,"return array(\n");
            foreach($cur_lang as $key => $value){
               fwrite($fp,"\t\"".$key."\"=>\"".$value."\",\n");
            }
            fwrite($fp,");\n");
            fwrite($fp,"?>\n");
            fclose($fp);
        }
   	}

    public static function createNewLangFile($newlang) {
        global $cur_lang, $default_lang_file, $target_lang_file;

        $error = false;
        $result = array();
        if (!empty($newlang)) {
            $newlangfile = BASE."framework/core/lang/".utf8_decode($newlang).".php";
            if (((!file_exists($newlangfile)) && ($newlangfile != $default_lang_file && $newlangfile != $target_lang_file))) {
                $fp = fopen($newlangfile, 'w+') or die("I could not open $newlangfile.");
                ksort($cur_lang);
                fwrite($fp,"<?php\n");
                fwrite($fp,"return array(\n");
                foreach($cur_lang as $key => $value){
                   fwrite($fp,"\t\"".$key."\"=>\"".$value."\",\n");
                }
                fwrite($fp,");\n");
                fwrite($fp,"?>\n");
                fclose($fp);
                $result['message'] = $newlang." ".gt('Language Created!');
            } else {
                $error = true;
                $result['message'] = $newlang." ".gt('Language Already Exists!');
            }
        } else {
            $error = true;
            $result['message'] = gt('New Language').' "'.$newlang.'" '.gt('Cannot be Created').'!';
        }
        $result['type'] = $error ? 'error' : 'message';
        return $result;
   	}

    public static function createNewLangInfoFile($newlang,$newauthor,$newcharset,$newlocale) {
        if (!empty($newlang)) {
            if (empty($newcharset)) $newcharset = 'UTF-8';
            $newlanginfofile = BASE."framework/core/lang/".utf8_decode($newlang).".info.php";
            if (((!file_exists($newlanginfofile)))) {
                $fp = fopen($newlanginfofile, 'w+') or die("I could not open $newlanginfofile.");
                fwrite($fp,"<?php\n");
                fwrite($fp,"return array(\n");
                fwrite($fp,"\t\"name\"=>\"".$newlang."\",\n");
                fwrite($fp,"\t\"author\"=>\"".$newauthor."\",\n");
                fwrite($fp,"\t\"charset\"=>\"".$newcharset."\",\n");
                fwrite($fp,"\t\"locale\"=>\"".$newlocale."\",\n");
                fwrite($fp,"\t\"default_view\"=>\"Default\",\n");
                fwrite($fp,");\n");
                fwrite($fp,"?>\n");
                fclose($fp);
            }
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
   						$langs[substr(utf8_encode($f),0,-4)] = $info['name'] . ' -- ' . $info['author'];
   					} else {
   						$langs[substr(utf8_encode($f),0,-4)] = substr($f,0,-4);
   					}
   				}
   			}
   		}
   		return $langs;
   	}

    function loadData($url, $ref = false) {
    	$chImg = curl_init($url);
    	curl_setopt($chImg, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($chImg, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0) Gecko/20100101 Firefox/4.0");
//        curl_setopt($chImg, CURLOPT_POST, true);
    	if ($ref) {
    		curl_setopt($chImg, CURLOPT_REFERER, $ref);
    	}
    	$curl_scraped_data = curl_exec($chImg);
//        curl_setopt($chImg, CURLOPT_POST, false);
    	curl_close($chImg);
    	return $curl_scraped_data;
    }

    public static function translate($text, $from = 'en', $to = 'fr') {
        include_once(BASE.'external/BingTranslate.class.php');
        $gt = new BingTranslateWrapper(BING_API);
        return $gt->translate($text, $from, $to);

        //old method
    	$data = self::loadData('http://api.bing.net/json.aspx?AppId=' . BING_API . '&Sources=Translation&Version=2.2&Translation.SourceLanguage=' . $from . '&Translation.TargetLanguage=' . $to . '&Query=' . urlencode($text));
    	$translated = json_decode($data);
    	if (sizeof($translated) > 0) {
    		if (isset($translated->SearchResponse->Translation->Results[0]->TranslatedTerm)) {
    			return $translated->SearchResponse->Translation->Results[0]->TranslatedTerm;
    		} else {
    			return false;
    		}
    	} else {
    		return false;
    	}
    }

    public static function getLangs() {
        include_once(BASE.'external/BingTranslate.class.php');
        $gt = new BingTranslateWrapper(BING_API);
        return $gt->LanguagesSupported();
    }

}

?>
