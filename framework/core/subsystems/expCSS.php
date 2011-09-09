<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expCSS class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Phillip Ball <phillip@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expCSS
 *
 * @subpackage Core-Subsystems
 * @package Framework
 */

class expCSS {

    public static function pushToHead($params) {
        global $css_primer, $css_core, $css_links, $css_theme, $css_inline;
        
        // primer css
        if (isset($params['css_primer'])){
            $primer_array = $params['css_primer'];
            foreach ($primer_array as $path) {
                //indexing the array by the filename
                $css_primer[$path] = $path;
            }
        }
        
        // files in framework/core/assets/css that is general to many views and the system overall
        if (isset($params['corecss'])){
            $core_array = explode(",",$params['corecss']);
            foreach ($core_array as $filename) {
                $existspath = BASE."framework/core/assets/css/".$filename.".css";
                $filepath = PATH_RELATIVE."framework/core/assets/css/".$filename.".css";
                if (is_file($existspath)) {
                    $css_core[$filepath] = $filepath;
                }
            }
        }
        
        // css linked in through the css plugin
        if (isset($params['link'])){ 
            $css_links[$params['link']] = $params['link'];
        };
        
        // CSS hard coded in a view
        if (!empty($params['css'])){
            $tcss = trim($params['css']);
            if (!empty($tcss)) $css_inline[$params['unique']] = $params['css'];
        }
    }    

    public static function parseCSSFiles() {
        global $css_primer, $css_core, $css_links, $css_theme, $css_inline, $head_config;
        $html = "";
        
        // gather up all .css files in themes/mytheme/css/
        self::themeCSS();
        
        unset($head_config['xhtml']);
        $css_files = array();
        foreach($head_config as $key=>$value) {
            if (!empty($value) && is_array($$key)) {
                 $css_files = array_merge($css_files, $$key);
            };
        };
        
        if (MINIFY!=1) {
            //eDebug($css_files);
            foreach ($css_files as $file) {
                $html .= "\t".'<link rel="stylesheet" type="text/css" href="'.$file.'" '.XHTML_CLOSING.'>'."\r\n";
            }
        } else {
            // if we're minifying, we'll break our URLs apart at MINIFY_URL_LENGTH characters to allow it through 
            // browser string limits
            $i = 0;
            $srt = array();
            $srt[$i] = "";
            foreach ($css_files as $file) {
                if (strlen($srt[$i])+strlen($file)<= MINIFY_URL_LENGTH) {
                    $srt[$i] .= $file.",";
                } else {
                    $i++;
                    $srt[$i] = "";
                    $srt[$i] .= $file.",";
                }
            }

            foreach ($srt as $link) {
                $link = rtrim($link,",");
                $html .= "\t".'<link rel="stylesheet" type="text/css" href="'.PATH_RELATIVE.'external/minify/min/index.php?f=' . $link . '" />'."\r\n";
            }
        }
        
        
        if (!empty($css_inline)) {
            $styles = "";
            foreach ($css_inline as $key=>$val) {
                $styles .= $val;
            }
            trim($styles);
            if (!empty($styles)) {
                $html .= "\t".'<style type="text/css" media="screen">'."\n";
                $html .= "\t".$styles."\n";
                $html .= "\t".'</style>'."\n";
            }
        }
        
        return $html;
    }
    
    
    public function themeCSS() {
        global $css_theme, $head_config;

        $cssdirs[] = BASE.'themes/'.DISPLAY_THEME.'/css/';
        if (THEME_STYLE!="") {
            $cssdirs[] = BASE.'themes/'.DISPLAY_THEME.'/css_'.THEME_STYLE.'/';
        }

        foreach($cssdirs as $key=>$cssdir){
            $variation = (THEME_STYLE!=''&&$key!=0)?"_".THEME_STYLE:"";
            
            if (is_dir($cssdir) && is_readable($cssdir)) {

                if (is_array($head_config['css_theme'])) {
                    foreach($head_config['css_theme'] as $filename){
                        $cssfile = $filename.".css";
    //                    $css_theme[reset(explode(".",end(explode("/",$filename))))."-theme"] = PATH_RELATIVE."themes/".DISPLAY_THEME_REAL."/css/".$cssfile;
                        $css_theme[reset(explode(".",end(explode("/",$filename))))."-theme"] = PATH_RELATIVE."themes/".DISPLAY_THEME."/css/".$cssfile;
                    }
                } elseif(empty($head_config['css_theme'])) {
                    # do nothing. We're not including CSS from the theme
                } else {
                    $dh = opendir($cssdir);
                    while (($cssfile = readdir($dh)) !== false) {
                        $filename = $cssdir.$cssfile;
                        if ( is_file($filename) && substr($filename,-4,4) == ".css") {
    //                        $css_theme[reset(explode(".",end(explode("/",$filename))))."-theme"] = PATH_RELATIVE."themes/".DISPLAY_THEME_REAL."/css/".$cssfile;
                            $css_theme[$key.reset(explode(".",end(explode("/",$filename))))."-theme".$variation] = PATH_RELATIVE."themes/".DISPLAY_THEME."/css".$variation."/".$cssfile;
                        }
                    }
                }
            }
        }
        // return the theme files in alphabetical order
        ksort($css_theme);
    }

}
?>
