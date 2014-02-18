<?php
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * This is the class expCSS
 *
 * @package Subsystems
 * @subpackage Subsystems
 */

class expCSS {

    public static function pushToHead($params) {
        global $css_primer, $css_core, $css_links, $css_theme, $css_inline, $less_vars;
        
        // normalize.css is always at the top
        if (!empty($params['normalize'])){
            $css_primer[PATH_RELATIVE."external/normalize/normalize.css"] = PATH_RELATIVE."external/normalize/normalize.css";
        }

         // set up less variables
        $lless_vars = array_merge($less_vars, !empty($params['lessvars']) ? $params['lessvars'] : array());

        // primer less to compile to css
        if (!empty($params['lessprimer'])) {
            $less_array = $params['lessprimer'];
            if (!empty($less_array) && !is_array($less_array)) $less_array = array($less_array);
            foreach ($less_array as $less_path) {
                if (strlen(PATH_RELATIVE) != 1) {
                    $less_path = str_replace(PATH_RELATIVE, '', $less_path);  // strip relative path for links coming from templates
                    $path_rel = PATH_RELATIVE;
                } else {
                    $path_rel = '/';
                }
                $less_path = ltrim($less_path, '/');
                $css_path = str_replace("/less/", "/css/", $less_path);
                $css_path = substr($css_path, 0, strlen($css_path)-4)."css";
                //indexing the array by the filename
                if (self::auto_compile_less($less_path, $css_path, $lless_vars))
                    $css_primer[$path_rel.$css_path] = $path_rel.$css_path;
            }
        }

        // primer css
        if (!empty($params['css_primer'])){
            $primer_array = $params['css_primer'];
            if (!empty($primer_array) && !is_array($primer_array)) $primer_array = array($primer_array);
            foreach ($primer_array as $path) {
                //indexing the array by the filename
                $css_primer[$path] = $path;
            }
        }
        
         // less files to compile to css
        if (!empty($params['lesscss'])) {
            $less_array = $params['lesscss'];
            if (!empty($less_array) && !is_array($less_array)) $less_array = array($less_array);
            foreach ($less_array as $less_path) {
                if (strlen(PATH_RELATIVE) != 1) {
                    $less_path = str_replace(PATH_RELATIVE, '', $less_path);  // strip relative path for links coming from templates
                    $path_rel = PATH_RELATIVE;
                } else {
                    $path_rel = '/';
                }
                $less_path = ltrim($less_path, '/');
                $css_path = str_replace("/less/", "/css/", $less_path);
                $css_path = substr($css_path, 0, strlen($css_path)-4)."css";
                //indexing the array by the filename
                if (self::auto_compile_less($less_path, $css_path, $lless_vars))
                    $css_links[$path_rel.$css_path] = $path_rel.$css_path;
            }
        }

        // files in framework/core/assets/less that are general to many views and the system overall
        // add .less support to corecss
        if (!empty($params['corecss'])){
            $core_array = explode(",",$params['corecss']);
            foreach ($core_array as $filename) {
                $filename = trim($filename);
                $css_path = "framework/core/assets/css/".$filename.".css";
                $less_path = "framework/core/assets/less/".$filename.".less";
                if (file_exists(BASE.$less_path))
                    self::auto_compile_less($less_path,$css_path,$lless_vars);
                if (is_file(BASE.$css_path))
                    $css_core[PATH_RELATIVE.$css_path] = PATH_RELATIVE.$css_path;
            }
        }
        
        // css linked in through the css plugin
        if (!empty($params['link'])){
            $css_links[$params['link']] = $params['link'];
        };
        
        // CSS hard coded in a view
        if (!empty($params['css'])){
            $tcss = trim($params['css']);
            if (!empty($tcss)) $css_inline[$params['unique']] = $params['css'];
        }

        if (expJavascript::inAjaxAction()) {
		    echo "<div class=\"io-execute-response\">";
            if (isset($params['corecss'])&&!empty($css_core)){
                foreach ($css_core as $path) {
                    echo '<link rel="stylesheet" type="text/css" href="'.$path.'">';
                }
            }
            if (!empty($params['link'])){
                echo '<link rel="stylesheet" type="text/css" href="'.$params['link'].'">';
            }
		    echo "</div>";
        }
    }    

    public static function parseCSSFiles() {
        global $css_primer, $css_core, $css_links, $css_theme, $css_inline, $head_config;  // these are all used via $$key below

        $html = "";
        
        // gather up all .css files in themes/mytheme/css/
        self::themeCSS();

        // at this point these params have already been processed
        unset($head_config['xhtml']);
        unset($head_config['lessprimer']);
        unset($head_config['lesscss']);
        unset($head_config['lessvars']);
        unset($head_config['normalize']);
        unset($head_config['framework']);
        unset($head_config['viewport']);
        unset($head_config['meta']);

        // we ALWAYS need the core and primer css
        if (!isset($head_config['css_core'])) {
            $core = array('css_core' => true);
            $head_config = array_merge($core, $head_config);
        }
        if (!isset($head_config['css_primer'])) {
            $primer = array('css_primer' => true);
            $head_config = array_merge($primer, $head_config);
        }

        $css_files = array();
        foreach($head_config as $key=>$value) {
            if (!empty($value) && is_array($$key)) {
                 $css_files = array_merge($css_files, $$key);
            };
        };

        if (MINIFY==1&&MINIFY_LINKED_CSS==1) {
            // if we're minifying, we'll break our URLs apart at MINIFY_URL_LENGTH characters to allow it through
            // browser string limits
            $strlen = (ini_get("suhosin.get.max_value_length")==0) ? MINIFY_URL_LENGTH : ini_get("suhosin.get.max_value_length");
            $i = 0;
            $srt = array();
            $srt[$i] = "";
            foreach ($css_files as $file) {
                if (!empty($file) && file_exists($_SERVER['DOCUMENT_ROOT'].$file)) {
                    if (strlen($srt[$i])+strlen($file) <= $strlen && $i <= MINIFY_MAX_FILES) {
                        $srt[$i] .= $file.",";
                    } else {
                        $i++;
                        $srt[$i] = "";
                        $srt[$i] .= $file.",";
                    }
                }
            }
            if ($srt[0] == "") array_shift($srt);
            foreach ($srt as $link) {
                $link = rtrim($link,",");
                $html .= "\t".'<link rel="stylesheet" type="text/css" href="'.PATH_RELATIVE.'external/minify/min/index.php?f=' . $link . '"' . XHTML_CLOSING.'>'."\r\n";
            }
        } else {
            //eDebug($css_files);
            foreach ($css_files as $file) {
                $html .= "\t".'<link rel="stylesheet" type="text/css" href="'.$file.'" '.XHTML_CLOSING.'>'."\r\n";
            }
        }
        
        if (!empty($css_inline)) {
            $styles = "";
            $htmlcss = "";
            foreach ($css_inline as $key=>$val) {
                $styles .= $val;
            }
            trim($styles);
            if (!empty($styles)) {
                $htmlcss .= "\t".'<style type="text/css" media="screen">'."\n";
                $htmlcss .= "\t".$styles."\n";
                $htmlcss .= "\t".'</style>'."\n";
            }
            if (MINIFY==1&&MINIFY_INLINE_CSS==1) {
                include_once(BASE.'external/minify/min/lib/JSMin.php');
                $htmlcss = JSMin::minify($htmlcss);
            }
            $html .= $htmlcss;
        }

        return $html;
    }

    public static function themeCSS() {
        global $css_theme, $head_config;

        // compile any theme .less files to css
        $less_vars =!empty($head_config['lessvars']) ? $head_config['lessvars'] : array();
        $lessdirs[] = 'themes/'.DISPLAY_THEME.'/less/';
        if (THEME_STYLE!="") {
            $lessdirs[] = 'themes/'.DISPLAY_THEME.'/less_'.THEME_STYLE.'/';
        }
        foreach($lessdirs as $lessdir){
            if (is_dir($lessdir) && is_readable($lessdir)) {
                if (is_array($head_config['css_theme'])) {
                    foreach($head_config['css_theme'] as $lessfile){
                        $filename = $lessdir.$lessfile;
                        if (is_file($filename) && substr($filename,-5,5) == ".less") {
                            $css_dir = str_replace("/less/","/css/",$lessdir);
                            $css_file = substr($lessfile,0,strlen($lessfile)-4)."css";
                            self::auto_compile_less($lessdir.$lessfile,$css_dir.$css_file,$less_vars);
                        }
                    }
                } elseif (empty($head_config['css_theme'])) {
                    # do nothing. We're not including CSS from the theme
                } else {
                    $dh = opendir($lessdir);
                    while (($lessfile = readdir($dh)) !== false) {
                        $filename = $lessdir.$lessfile;
                        if (is_file($filename) && substr($filename,-5,5) == ".less") {
                            $css_dir = str_replace("/less/","/css/",$lessdir);
                            $css_file = substr($lessfile,0,strlen($lessfile)-4)."css";
                            self::auto_compile_less($lessdir.$lessfile,$css_dir.$css_file,$less_vars);
                        }
                    }
                }
            }
        }

        // collect all the theme css files (less files have been compiled already)
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
                        $css_theme[reset(explode(".",end(explode("/",$filename))))."-theme"] = PATH_RELATIVE."themes/".DISPLAY_THEME."/css/".$cssfile;
                    }
                } elseif(empty($head_config['css_theme'])) {
                    # do nothing. We're not including CSS from the theme
                } else {
                    $dh = opendir($cssdir);
                    while (($cssfile = readdir($dh)) !== false) {
                        $filename = $cssdir.$cssfile;
                        if ( is_file($filename) && substr($filename,-4,4) == ".css") {
                            $endfile = explode("/",$filename);
                            $tmpfile = explode(".",end($endfile));
                            $css_theme[$key.reset($tmpfile)."-theme".$variation] = PATH_RELATIVE."themes/".DISPLAY_THEME."/css".$variation."/".$cssfile;
                        }
                    }
                }
            }
        }
        // return the theme files in alphabetical order
        if (is_array($css_theme)) ksort($css_theme);
    }

    /**
     * Automatically compile .less files into a .css file in the /tmp/css folder
     *
     * @static
     *
     * @param string $less_pname full pathname of the .less file
     * @param string $css_fname  filename of the output css file
     * @param array  $vars       array of variables to pass to parse()
     *
     * @return bool
     */
    public static function auto_compile_less($less_pname, $css_fname, $vars=array()) {
        if (defined('LESS_COMPILER')) {
            $less_compiler = strtolower(LESS_COMPILER);
        } else {
            $less_compiler = 'lessphp';
        }
        switch ($less_compiler) {
            case 'iless':
//                break;
            case 'less.php':
//                if (is_file(BASE.$less_pname) && substr($less_pname,-5,5) == ".less") {
////                    include_once(BASE.'external/lessphp/lessc.inc.php');
//                    include_once(BASE.'external/phpless/Less.php');
//                    // load the cache
//                    $less_cname = str_replace("/","_",$less_pname);
//                    $cache_fname = BASE.'tmp/css/'.$less_cname.".cache";
//                    $cache = BASE.$less_pname;
//                    if (file_exists($cache_fname)) {
//                        $cache = unserialize(file_get_contents($cache_fname));
//                        if (!empty($cache['vars']) && $vars != $cache['vars']) {
//                            $cache = BASE.$less_pname;
//                        }
//                    }
////                    $less = new lessc;
//                    $less = new Less_Parser();
//                    $less->SetCacheDir(BASE.'tmp/css/');
//                    $less->setVariables($vars);
//                    // we need to convert $vars array to a less string
//                    $lvars = '';
//                    foreach ($vars as $key=>$param) {
//                        $lvars .= '@' . $key . ":" . $param . ";";
//                    }
//                    $less->parse($lvars);
//
//                    $new_cache = $less->cachedCompile($cache, false);
//                    if (!file_exists(BASE.$css_fname) || !is_array($cache) || $new_cache['updated'] > $cache['updated']) {
//                        if (!empty($new_cache['compiled'])) {
//                            $new_cache['vars'] = !empty($vars)?$vars:null;
//                            $css_loc = pathinfo(BASE.$css_fname);
//                            if (!is_dir($css_loc['dirname'])) mkdir($css_loc['dirname']);  // create /css output folder if it doesn't exist
//                            file_put_contents(BASE.$css_fname, $new_cache['compiled']);
//                            file_put_contents($cache_fname, serialize($new_cache));
//                        }
//                    }
//
//                    $less->parseFile($cache);
//                    $new_cache = $less->getCss();
//                    $css_loc = pathinfo(BASE.$css_fname);
//                    if (!is_dir($css_loc['dirname'])) mkdir($css_loc['dirname']);  // create /css output folder if it doesn't exist
//                    file_put_contents(BASE.$css_fname, $new_cache);
//                    return true;
//                } else {
//                    flash('notice',$less_pname. ' ' . gt('does not exist!'));
//                    return false;
//                }
//                break;
            case 'lessphp':
            default :
                if (is_file(BASE.$less_pname) && substr($less_pname,-5,5) == ".less") {
                    if (!is_file(BASE.'external/' . $less_compiler . '/lessc.inc.php')) $less_compiler = 'lessphp';
                    include_once(BASE.'external/' . $less_compiler . '/lessc.inc.php');
                    // load the cache
                    $less_cname = str_replace("/","_",$less_pname);
                    $cache_fname = BASE.'tmp/css/'.$less_cname.".cache";
                    $cache = BASE.$less_pname;
                    if (file_exists($cache_fname)) {
                        $cache = unserialize(file_get_contents($cache_fname));
                        if (!empty($cache['vars']) && $vars != $cache['vars']) {
                            $cache = BASE.$less_pname;
                        }
                    }
                    $less = new lessc;
                    $less->setVariables($vars);

                    $new_cache = $less->cachedCompile($cache, false);
                    if (!file_exists(BASE.$css_fname) || !is_array($cache) || $new_cache['updated'] > $cache['updated']) {
                        if (!empty($new_cache['compiled']) && $new_cache['compiled'] != "\n") {
                            $new_cache['vars'] = !empty($vars)?$vars:null;
                            $css_loc = pathinfo(BASE.$css_fname);
                            if (!is_dir($css_loc['dirname'])) mkdir($css_loc['dirname']);  // create /css output folder if it doesn't exist
                            file_put_contents(BASE.$css_fname, $new_cache['compiled']);
                            file_put_contents($cache_fname, serialize($new_cache));
                        }
                    }
                    return true;
                } else {
                    flash('notice',$less_pname. ' ' . gt('does not exist!'));
                    return false;
                }
        }
    }

    public static function updateCoreCss(){
        $dir = BASE . 'framework/core/assets/less';
        $files = '';
        if (is_readable($dir)) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($dir . '/' . $file) && is_file($dir . '/' . $file) && substr($file, -5, 5) == '.less') {
                   $files .= substr($file, 0, strlen($file) -5) . ',';
                }
            }
            expCSS::pushToHead(array(
                "corecss"=>$files
            ));
        }
    }

}

?>