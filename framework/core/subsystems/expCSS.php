<?php
/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @package    Framework
 * @subpackage Subsystems
 * @author     Phillip Ball <phillip@oicgroup.net>
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @version    Release: @package_version@
 * @link       http://www.exponent-docs.org/api/package/PackageName
 */

class expCSS {

    public function pushToHead($params) {
        global $css_primer, $css_core, $css_links, $css_theme, $css_inline;
        
        // primer css
        if (isset($params['css_primer'])){
            $primer_array = $params['css_primer'];
            foreach ($primer_array as $path) {
                //indexing the array by the filename
                $css_primer[reset(explode(".",end(explode("/",$path))))] = $path;
            }
        }
        
        // files in framework/core/assets/css that is general to many views and the system overall
        if (isset($params['corecss'])){
            $core_array = explode(",",$params['corecss']);
            foreach ($core_array as $filename) {
                $existspath = BASE."framework/core/assets/css/".$filename.".css";
                $filepath = PATH_RELATIVE."framework/core/assets/css/".$filename.".css";
                if (is_file($existspath)) {
                    $css_core[$filename] = $filepath;
                }
            }
        }
        
        // css linked in through the css plugin
        if (isset($params['link'])){ 
            $css_links[$params['unique']] = $params['link'];
        };
        
        // CSS hard coded in a view
        if (!empty($params['css'])){
            $tcss = trim($params['css']);
            if (!empty($tcss)) $css_inline[$params['unique']] = $params['css'];
        }
    }    

    public function parseCSSFiles() {
        global $css_primer, $css_core, $css_links, $css_theme, $css_inline, $head_config;
        $html = "";
        
        // gather up all .css files in themes/mytheme/css/
        expCSS::themeCSS();
        
                
        // if (is_array($css_primer)) ksort($css_primer);
        // eDebug($css_primer);
        // 
        // if (is_array($css_core)) ksort($css_core);
        // eDebug($css_core);
        // 
        // if (is_array($css_links)) ksort($css_links);
        // eDebug($css_links);
        // 
        // if (is_array($css_primer)) ksort($css_theme);
        // eDebug($css_theme);
        // 
        // if (is_array($css_inline)) ksort($css_inline);
        // eDebug($css_inline);

        unset($head_config['xhtml']);
        $css_files = array();
        foreach($head_config as $key=>$value) {
            if (!empty($value) && is_array($$key)) {
                 $css_files = array_merge($css_files, $$key);
            };
        };
        
        if (MINIFY!=1) {
            foreach ($css_files as $file) {
                $html .= "\t".'<link rel="stylesheet" type="text/css" href="'.$file.'" '.XHTML_CLOSING.'>'."\r\n";
            }
        } else {
            //2048
            $urlstr = implode(',',$css_files);
            eDebug(strlen($urlstr));
            $html = "\t".'<link rel="stylesheet" type="text/css" href="'.PATH_RELATIVE.'external/minify/min/index.php?f=' . $urlstr . '" />';
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

        $cssdir = BASE.'themes/'.DISPLAY_THEME.'/css/';

        if (is_dir($cssdir) && is_readable($cssdir)) {
            $dh = opendir($cssdir);
            while (($cssfile = readdir($dh)) !== false) {
                $filename = $cssdir.$cssfile;
                if ( is_file($filename) && substr($filename,-4,4) == ".css") {
                    $css_theme[reset(explode(".",end(explode("/",$filename))))."-theme"] = PATH_RELATIVE."themes/".DISPLAY_THEME_REAL."/css/".$cssfile;
                }
            }
        }

        //return $css_theme;
        //eDebug($css_files);
    }

}
?>
