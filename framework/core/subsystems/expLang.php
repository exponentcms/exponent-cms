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
/**
 * This is the class expLang
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */

class expLang {
    
    function loadLang() {
        if (!defined('LANGUAGE')) return false;
	    
	    global $cur_lang, $default_lang, $defualt_lang_file;
	    $defualt_lang_file = BASE."framework/core/lang/English - US.php";
        $default_lang = include(BASE."framework/core/lang/English - US.php");
        $cur_lang = include(BASE."framework/core/lang/".LANGUAGE.".php");
    }
    
	public function gettext($str) {	
	    if (!defined('LANGUAGE')) return $str;

	    global $cur_lang;
	    expLang::writeTemplate($str);
	    $str = LANGUAGE!="English - US" && array_key_exists($str,$cur_lang) ? $cur_lang[$str] : $str;
		return $str;
	}
	
	public function writeTemplate($str) {
	    global $default_lang, $defualt_lang_file;
	    //!array_key_exists($str,$default_lang)
        if (DEVELOPMENT && (defined("WRITE_LANG_TEMPLATE") && WRITE_LANG_TEMPLATE!=0) && LANGUAGE=="English - US") {
            $fp = fopen($defualt_lang_file, 'w+') or die("I could not open $filename.");
            $default_lang[$str] = $str;
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
}

?>
