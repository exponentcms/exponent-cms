<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

/** @define "BASE" "../../../" */
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
        if (!empty($params['normalize']) && !bs3() && !bs4() && !bs5()){
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
                $css_path = self::generate_filename($css_path);
                //indexing the array by the filename
                if (!isset($css_primer[$path_rel . $css_path]))
                    if (self::auto_compile_less($less_path, $css_path, $lless_vars))
                        $css_primer[$path_rel.$css_path] = $path_rel.$css_path;
            }
        }

        // primer scss to compile to css
        if (!empty($params['scssprimer'])) {
            $scss_array = $params['scssprimer'];
            if (!empty($scss_array) && !is_array($scss_array)) $scss_array = array($scss_array);
            foreach ($scss_array as $scss_path) {
                if (strlen(PATH_RELATIVE) != 1) {
                    $scss_path = str_replace(PATH_RELATIVE, '', $scss_path);  // strip relative path for links coming from templates
                    $path_rel = PATH_RELATIVE;
                } else {
                    $path_rel = '/';
                }
                $scss_path = ltrim($scss_path, '/');
                $css_path = str_replace("/scss/", "/css/", $scss_path);
                $css_path = self::generate_filename($css_path);
                //indexing the array by the filename
                if (!isset($css_primer[$path_rel . $css_path]))
                    if (self::auto_compile_scss($scss_path, $css_path, $lless_vars))
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
                $css_path = self::generate_filename($css_path);
                //indexing the array by the filename
                if (!isset($css_links[$path_rel . $css_path]))
                    if (self::auto_compile_less($less_path, $css_path, $lless_vars))
                        $css_links[$path_rel.$css_path] = $path_rel.$css_path;
            }
        }

        // scss files to compile to css
        if (!empty($params['scsscss'])) {
            $scss_array = $params['scsscss'];
            if (!empty($scss_array) && !is_array($scss_array)) $scss_array = array($scss_array);
            foreach ($scss_array as $scss_path) {
                if (strlen(PATH_RELATIVE) != 1) {
                    $scss_path = str_replace(PATH_RELATIVE, '', $scss_path);  // strip relative path for links coming from templates
                    $path_rel = PATH_RELATIVE;
                } else {
                    $path_rel = '/';
                }
                $scss_path = ltrim($scss_path, '/');
                $css_path = str_replace("/scss/", "/css/", $scss_path);
                $css_path = self::generate_filename($css_path);
                //indexing the array by the filename
                if (!isset($css_links[$path_rel . $css_path]))
                    if (self::auto_compile_scss($scss_path, $css_path, $lless_vars))
                        $css_links[$path_rel.$css_path] = $path_rel.$css_path;
            }
        }

        // files in framework/core/assets/less that are general to many views and the system overall
        if (!empty($params['corecss'])){
            $core_array = explode(",",$params['corecss']);
            foreach ($core_array as $filename) {
                $filename = trim($filename);
                $css_path = "framework/core/assets/css/".$filename.".css";
                $less_path = "framework/core/assets/less/".$filename.".less";
                if (!isset($css_core[PATH_RELATIVE . $css_path])) {
                    if (file_exists(BASE . $less_path))
                        self::auto_compile_less($less_path, $css_path, $lless_vars);
                    if (is_file(BASE . $css_path))
                        $css_core[PATH_RELATIVE . $css_path] = PATH_RELATIVE . $css_path;
                }
            }
        }
        //note we don't use scss files for core stylesheets at this time

        // css stylesheets linked in through the css plugin
        if (!empty($params['link'])){
            $params['link'] = is_array($params['link']) ? $params['link'] : array($params['link']);
            foreach ($params['link'] as $link) {
                $css_links[$link] = $link;
            }
        };

        // css hard coded in a view
        if (!empty($params['css'])){
            $tcss = trim($params['css']);
            if (!empty($tcss)) {
                if (empty($params['unique'])) {
                    $params['unique'] = "unique-" . microtime();  // must be unique for each call
                }
                $css_inline[$params['unique']] = $params['css'];
            }
        }

        // if within an ajax call, immediately output the css
        //FIXME we ONLY output primercss, corecss, links, and inline styles in $params['css']... with less processing
        if (expJavascript::inAjaxAction()) {
            // we make several assumptions since we are only running a single action
		    echo "<div class=\"io-execute-response\">";
            if (!empty($css_primer)){
                foreach ($css_primer as $path) {
                    echo '<link rel="stylesheet" type="text/css" href="',$path,'">' . "\r\n";
                }
            }
            if (!empty($css_core)){
                foreach ($css_core as $path) {
                    echo '<link rel="stylesheet" type="text/css" href="',$path,'">' . "\r\n";
                }
            }
            if (!empty($css_links)) {
                foreach ($css_links as $link) {
                    echo '<link rel="stylesheet" type="text/css" href="',$link,'">' . "\r\n";
                }
            }
            if (!empty($params['css'])) {
                echo '<style type="text/css">';
                $htmlcss = trim($params['css']);
                if (MINIFY == 1 && MINIFY_INLINE_CSS == 1) {
                    if (MINIFY_USE_JSMIN) {
                        include_once(BASE . 'external/minify/min/lib/JSMin.php');
                        $htmlcss = JSMin::minify($htmlcss) . "\r\n";
                    } else {
                        include_once(BASE . 'external/minify/min/lib/CSSmin.php');
                        $min = new CSSmin();
                        $htmlcss = $min->run($htmlcss) . "\r\n";
                    }
                }
                echo $htmlcss;
                echo '</style>' . "\r\n";
            }
		    echo "</div>";
            return true;
        }
    }

    public static function parseCSSFiles() {
        global $css_primer, $css_core, $css_links, $css_theme, $css_inline, $head_config;  // these are all used via $$key below

        $html = "";

        // gather up all .css files in themes/mytheme/css/
        self::themeCSS();

        // at this point these params have already been processed
        unset(
            $head_config['xhtml'],
            $head_config['lessprimer'],
            $head_config['scssprimer'],
            $head_config['lesscss'],
            $head_config['scsscss'],
            $head_config['link'],
            $head_config['lessvars'],
            $head_config['normalize'],
            $head_config['framework'],
            $head_config['viewport'],
            $head_config['meta']
        );

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
//                $css_files += $$key;
            };
        };

        if (MINIFY == 1 && MINIFY_LINKED_CSS == 1) {
            // if we're minifying, we'll break our URLs apart at MINIFY_URL_LENGTH characters to allow it through
            // browser string limits
            $strlen = (ini_get("suhosin.get.max_value_length")==0) ? MINIFY_URL_LENGTH : ini_get("suhosin.get.max_value_length");
            $i = 0;
            $srt = array();
            $srt[$i] = "";
            foreach ($css_files as $file) {
                if (!empty($file) && file_exists($_SERVER['DOCUMENT_ROOT'].$file)) {
                    if ($i <= MINIFY_MAX_FILES && strlen($srt[$i])+strlen($file) <= $strlen) {
                        $srt[$i] .= $file.",";
                    } else {
                        $i++;
                        $srt[$i] = "";
                        $srt[$i] .= $file.",";
                    }
                } else {
                    $html .= "\t" . '<link rel="stylesheet" type="text/css" href="' . $file . '" ' . XHTML_CLOSING . '>' . "\r\n";
                }
            }
            if ($srt[0] == "") array_shift($srt);
            foreach ($srt as $link) {
                $link = rtrim($link,",");
                $html .= "\t" . '<link rel="stylesheet" type="text/css" href="' . PATH_RELATIVE . 'external/minify/min/index.php?f=' . $link . '"' . XHTML_CLOSING . '>' . "\r\n";
            }
        } else {
            //eDebug($css_files);
            foreach ($css_files as $file) {
                $html .= "\t" . '<link rel="stylesheet" type="text/css" href="' . $file . '" ' . XHTML_CLOSING . '>' . "\r\n";
            }
        }

        if (!empty($css_inline)) {
            $styles = "";
            $htmlcss = "";
            foreach ($css_inline as $key=>$val) {
                $styles .= $val;
            }
            $styles = trim($styles);
            if (!empty($styles)) {
                $htmlcss .= "\t" . '<style type="text/css">' . "\r\n";
                $htmlcss .= "\t" . $styles . "\r\n";
                $htmlcss .= "\t" . '</style>' . "\r\n";
            }
            if (MINIFY == 1 && MINIFY_INLINE_CSS == 1) {
                if (MINIFY_USE_JSMIN) {
                    include_once(BASE . 'external/minify/min/lib/JSMin.php');
                    $htmlcss = JSMin::minify($htmlcss) . "\r\n";
                } else {
                    include_once(BASE . 'external/minify/min/lib/CSSmin.php');
                    $min = new CSSmin();
                    $htmlcss = $min->run($htmlcss) . "\r\n";
                }
            }
            $html .= $htmlcss;
        }

        return $html;
    }

    public static function themeCSS() {
        global $css_theme, $head_config, $less_vars;

        // compiler will crash without expected variables assigned
        if (!isset($less_vars['menu_width']))
            $less_vars['menu_width'] = 769;
        if (!isset($less_vars['swatch']))
            $less_vars['swatch'] = '';
        if (!isset($less_vars['themepath']))
            $less_vars['themepath'] = '';

//        // code for testing scss compiler
//        self::auto_compile_scss('external/bootstrap5/scss/newui', 'tmp/css/newui5.css', $less_vars);  //FIXME test

        // compile any theme .less files to css
//        $less_vars =!empty($head_config['lessvars']) ? $head_config['lessvars'] : array();
        $lessdirs[] = 'themes/' . DISPLAY_THEME . '/less/';
        if (THEME_STYLE != "") {
            $lessdirs[] = 'themes/' . DISPLAY_THEME . '/less_' . THEME_STYLE . '/';
        }
        foreach($lessdirs as $lessdir){
            if (is_dir($lessdir) && is_readable($lessdir)) {
                if (is_array($head_config['css_theme'])) {
                    foreach($head_config['css_theme'] as $lessfile){
                        $filename = $lessdir . $lessfile;
                        if (substr($filename,-5,5) !== ".less") {
                            $filename .= ".less";
                        }
                        if (is_file($filename)) {
                            $css_dir = str_replace("/less/","/css/", $lessdir);
                            $css_file = self::generate_filename($lessfile);
                            self::auto_compile_less($lessdir.$lessfile,$css_dir.$css_file,$less_vars);
                        }
                    }
                } elseif (empty($head_config['css_theme'])) {
                    # do nothing. We're not including CSS from the theme
                } else {
                    $dh = opendir($lessdir);
                    while (($lessfile = readdir($dh)) !== false) {
                        $filename = $lessdir . $lessfile;
                        if (is_file($filename) && substr($filename,-5,5) === ".less" && basename($filename) !== 'variables.less') {
                            $css_dir = str_replace("/less/","/css/", $lessdir);
                            $css_file = self::generate_filename($lessfile);
                            self::auto_compile_less($lessdir . $lessfile,$css_dir . $css_file, $less_vars);
                        }
                    }
                }
            }
        }

        // compile any theme .scss files to css
        $scssdirs[] = 'themes/' . DISPLAY_THEME . '/scss/';
        if (THEME_STYLE != "") {
            $scssdirs[] = 'themes/' . DISPLAY_THEME . '/scss_' . THEME_STYLE . '/';
        }
        foreach($scssdirs as $scssdir){
            if (is_dir($scssdir) && is_readable($scssdir)) {
                if (is_array($head_config['css_theme'])) {
                    foreach($head_config['css_theme'] as $scssfile){
                        $filename = $scssdir . $scssfile;
                        if (substr($filename,-5,5) !== ".scss") {
                            $filename .= ".scss";
                        }
                        if (is_file($filename) || is_file("_" . $filename)) {
                            $css_dir = str_replace("/scss/","/css/", $scssdir);
                            $css_file = self::generate_filename($scssfile);
                            self::auto_compile_scss($scssdir . $scssfile,$css_dir . $css_file, $less_vars);
                        }
                    }
                } elseif (empty($head_config['css_theme'])) {
                    # do nothing. We're not including CSS from the theme
                } else {
                    $dh = opendir($scssdir);
                    while (($scssfile = readdir($dh)) !== false) {
                        $filename = $scssdir . $scssfile;
                        if (is_file($filename) && substr($filename,-5,5) === ".scss" && basename($filename) !== '_variables.scss') {
                            $css_dir = str_replace("/scss/","/css/",$scssdir);
                            $css_file = self::generate_filename($scssfile);
                            self::auto_compile_scss($scssdir . $scssfile,$css_dir.$css_file, $less_vars);
                        }
                    }
                }
            }
        }

        // collect all the theme css files (less and scss files have been compiled already)
        $cssdirs[] = BASE.'themes/'.DISPLAY_THEME.'/css/';
        if (THEME_STYLE != "") {
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
                        if ( is_file($filename) && substr($filename,-4,4) === ".css") {
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
     * Automatically compile .less files into a .css file cached in the /tmp/css folder
     *
     * @static
     *
     * @param string $less_pname full pathname of the .less file
     * @param string $css_fname  filename of the output css file
     * @param array  $vars       array of variables to pass to parse()
     *
     * @throws Exception
     * @return bool
     */
    public static function auto_compile_less($less_pname, $css_fname, $vars=array()) {
        if (DEVELOPMENT || !file_exists(BASE . $css_fname) || expSession::get('force_less_compile') == 1) {
            if (defined('LESS_COMPILER')) {
                $less_compiler = strtolower(LESS_COMPILER);
                if ((bs3()||bs4()|| bs5()) && $less_compiler === "lessphp")
                    $less_compiler = 'less.php'; //note lessphp will NOT compile bootstrap v3
            } else {
                $less_compiler = 'less.php';
            }
            if (substr($less_pname,-5,5) !== ".less") {
                $less_pname .= ".less";
            }
            switch ($less_compiler) {
                case 'iless':
                    if (is_file(BASE.$less_pname)) {
                        require_once(BASE.'external/iless/lib/ILess/Autoloader.php');
                        ILess\Autoloader::register();  //iLess2

                        $iless_options = array();
                        if (DEVELOPMENT && LESS_COMPILER_MAP) {
                            $less_cname = str_replace("/", "_", $less_pname);
                            if ((int)LESS_COMPILER_MAP === 1) {
                                $map_write_to = null;  // inline map
                            } else {
                                $map_write_to = BASE . 'tmp/css/' . $less_cname . ".map";
                            }
                            if (strlen(PATH_RELATIVE) != 1) {
                                $basePath = rtrim(realpath(str_replace(PATH_RELATIVE, '', BASE)), "\\/");
                            } else {
                                $basePath = rtrim(realpath(BASE), '\\/');
                            }
                            $iless_options = array(
                                'sourceMap'         => true,  // output .map file?
                                'sourceMapOptions' => array(
                                    'sourceRoot' => '/',
                                    // an optional name of the generated code that this source map is associated with.
                                    'filename' => PATH_RELATIVE . $css_fname,  // url location of .css file
                                    // url of the map
                                    'url' => PATH_RELATIVE . 'tmp/css/' . $less_cname . ".map",
                                    // absolute path to a file to write the map to
                                    'write_to' =>  $map_write_to,
                                    // output source contents?
                                    'source_contents' => false,
                                    // base path for filename normalization
                                    'base_path' => $basePath,  // base (difference between) file & url locations, removed from ALL source files in .map
                                ),
                            );
                        }
                        if (MINIFY==1 && MINIFY_LESS==1) {
                            $iless_options['compress'] = true;  // compress output file?
                        }
                        $less = new ILess\Parser(  //iLess2
                            $iless_options,  // options
                            // cache implementation
                            new ILess\Cache\FileSystemCache(array(  //iLess2
                                'cache_dir' => BASE . 'tmp/css/',
                            )
                        ));
                        $less->setVariables($vars);

                        try {
                            // create your cache key
                            $cacheKey = md5(BASE . $less_pname);
                            $importer = $less->getImporter();
                            $cache = $less->getCache();
                            $rebuild = true;
                            $cssLastModified = -1;
                            if ($cache->has($cacheKey)) {
                                $rebuild = false;
                                list($css, $importedFiles) = $cache->get($cacheKey);
                                // we need to check if the file has been modified
                                foreach ($importedFiles as $importedFileArray) {
                                    list($lastModifiedBefore, $path, $currentFileInfo) = $importedFileArray;
                                    $lastModified = $importer->getLastModified($path, $currentFileInfo);
                                    $cssLastModified = max($lastModified, $cssLastModified);
                                    if ($lastModifiedBefore != $lastModified) {
                                        $rebuild = true;
                                        // no need to continue, we will rebuild the CSS
                                        break;
                                    }
                                }
                            }
                            if ($rebuild) {
                                $less->parseFile(BASE . $less_pname);
                                $css = $less->getCSS();
                                if (file_exists(BASE . 'external/php-autoprefixer/autoload.inc.php')) {
                                    include_once(BASE . 'external/php-autoprefixer/autoload.inc.php');
                                    // save map info
                                    $map = explode("\n/*# sourceMappingURL=", $css);
                                    $css = (new Padaliyajay\PHPAutoprefixer\Autoprefixer($css))->compile(!(MINIFY == 1 && MINIFY_LESS == 1));
                                    if (!empty($map[1])) {
                                        $css .= "/*# sourceMappingURL=" . $map[1];
                                    }
                                }
                                // what files have been imported?
                                $importedFiles = array();
                                foreach ($importer->getImportedFiles() as $importedFile) {
                                    $importedFiles[] = array(
                                        $importedFile[0]->getLastModified(),
                                        $importedFile[1],
                                        $importedFile[2]
                                    );
                                    $cssLastModified = max($cssLastModified, $importedFile[0]->getLastModified());
                                }
                                $cache->set($cacheKey, array($css, $importedFiles));
                            }
                            if ($rebuild || !file_exists(BASE . $css_fname)) {
                                // write compiled css file
                                $css_loc = pathinfo(BASE . $css_fname);
                                if (!is_dir($css_loc['dirname'])) {
                                    if (mkdir(
                                        $css_loc['dirname'],
                                        octdec(DIR_DEFAULT_MODE_STR + 0)
                                    ) === false) {
                                        flash('error', gt('Less compiler') . ': ' . gt('unable to create css folder') . ': ' . $css_loc['dirname'] );
                                        return false;
                                    }
                                } // create /css output folder if it doesn't exist
                                if (file_put_contents(BASE . $css_fname, $css) === false) {
                                    flash('error', gt('Less compiler') . ': ' . gt('unable to write') . ': ' . $css_fname );
                                    return false;
                                }
                            }
                            return true;
                        } catch(Exception $e) {
                            flash('error', gt('Less compiler') . ': ' . $less_pname . ': ' . $e->getMessage());
                            if (file_exists(BASE . $css_fname))
                                return true; // old file exists so use it
                            else
                                return false;
                        }
                    } else {
                        flash('notice', $less_pname. ' ' . gt('does not exist!'));
                        return false;
                    }
                    break;
                case 'less.php':
                case 'lessphp':
                default :
                    if (is_file(BASE . $less_pname)) {
//                        if (!is_file(BASE . 'external/' . $less_compiler . '/lessc.inc.php'))
//                            $less_compiler = 'less.php';
                        include_once(BASE . 'external/' . $less_compiler . '/lessc.inc.php');
                        $less = new lessc;
                        $less->setOptions(array(
                            'cache_dir'         => BASE . 'tmp/css/',
                            'prefix'            => '_less_'
                        ));

                        // load the cache
                        $less_cname = str_replace("/", "_", $less_pname);
                        $cache_fname = BASE . 'tmp/css/' . $less_cname . ".cache";
                        $cache = BASE . $less_pname;
                        if (file_exists($cache_fname)) {
                            $cache = unserialize(file_get_contents($cache_fname));
                            if (!empty($cache['vars']) && $vars != $cache['vars'] && !expJavascript::inAjaxAction()) {
                                $cache = BASE . $less_pname;  // force a compile if the vars have changed
                            }
                        }

                        if (DEVELOPMENT && LESS_COMPILER_MAP && $less_compiler === 'less.php') {
                            if ((int)LESS_COMPILER_MAP === 1) {
                                $map_write_to = null;  // inline map
                                $map_url = null;
                                $map_filename = null;
                            } else {
                                $map_write_to = BASE . 'tmp/css/' . $less_cname . ".map";
                                $map_url = PATH_RELATIVE . 'tmp/css/' . $less_cname . ".map";
                                $map_filename = PATH_RELATIVE . $css_fname;
                            }
                            if (strlen(PATH_RELATIVE) != 1) {
                                $basePath = rtrim(realpath(str_replace(PATH_RELATIVE, '', BASE)), "\\/");
                            } else {
                                $basePath = rtrim(realpath(BASE), '\\/');
                            }
                            $less->setOptions(array(
//                                'outputSourceFiles' => true,  // include css source in .map file?
                                'sourceMap'         => true,  // output .map file?
                                'sourceMapWriteTo'  => $map_write_to,
                                'sourceMapURL'      => $map_url,
                                'sourceMapFilename' => $map_filename,  // url location of .css file
                                'sourceMapBasepath' => $basePath,  // base (difference between) file & url locations, removed from ALL source files in .map
                                'sourceRoot'        => '/',
//                                'sourceMapRootpath' => PATH_RELATIVE . $less_pname,  // tacked onto ALL source files in .map
                            ));
                        }

                        if (MINIFY==1 && MINIFY_LESS==1 && $less_compiler === 'less.php') {
                            $less->setOptions(array(
                                'compress'         => true,  // compress output file?
                            ));
                        }

                        $less->setVariables($vars);

                        try {
                            $file_updated = false;
                            $new_cache = $less->cachedCompile($cache, false);

                            if (!is_array($cache) || $new_cache['updated'] > $cache['updated']) {
                                if (!empty($new_cache['compiled']) && $new_cache['compiled'] !== "\n") {
                                    $file_updated = true;
                                    // attempt to load autoprefixer
                                    if (file_exists(BASE . 'external/php-autoprefixer/autoload.inc.php')) {
                                        include_once(BASE . 'external/php-autoprefixer/autoload.inc.php');
                                        // save map info
                                        $map = explode("\n/*# sourceMappingURL=", $new_cache['compiled']);
                                        $new_cache['compiled'] = (new Padaliyajay\PHPAutoprefixer\Autoprefixer($new_cache['compiled']))->compile(!(MINIFY == 1 && MINIFY_LESS == 1));
                                        if (!empty($map[1])) {
                                            $new_cache['compiled'] .= "/*# sourceMappingURL=" . $map[1];
                                        }
                                    }
                                    // store compiler cache file
                                    $new_cache['vars'] = !empty($vars) ? $vars : null;
                                    file_put_contents($cache_fname, serialize($new_cache));
                                }
                            }
                            if ($file_updated || (!empty($new_cache['compiled']) && $new_cache['compiled'] !== "\n" && !file_exists(BASE . $css_fname))) {
                                // write compiled css file
                                $css_loc = pathinfo(BASE . $css_fname);
                                if (!is_dir($css_loc['dirname'])) {
                                    if (mkdir(
                                        $css_loc['dirname'],
                                        octdec(DIR_DEFAULT_MODE_STR + 0)
                                    ) === false) {
                                        flash('error', gt('Less compiler') . ': ' . gt('unable to create css folder') . ': ' . $css_loc['dirname'] );
                                        return false;
                                    }
                                } // create /css output folder if it doesn't exist
                                if (file_put_contents(BASE . $css_fname, $new_cache['compiled']) === false) {
                                    flash('error', gt('Less compiler') . ': ' . gt('unable to write') . ': ' . $css_fname);
                                    return false;
                                }
                            }
                            return true;
                        } catch(Exception $e) {
                            flash('error', gt('Less compiler') . ': ' . $less_pname . ': ' . $e->getMessage());
                            if (file_exists(BASE . $css_fname))
                                return true; // old file exists so use it
                            else
                                return false;
                        }
                    } else {
                        flash('notice', $less_pname . ' ' . gt('does not exist!'));
                        return false;
                    }
            }
        } else {
            return true;  // the .css file already exists and we're not in develeopment
        }
    }

    /**
     * Automatically compile .scss files into a .css file cached in the /tmp/css folder
     *
     * @static
     *
     * @param string $scss_pname full pathname of the .scss file
     * @param string $css_fname  filename of the output css file
     * @param array  $vars       array of variables to pass to parse()
     *
     * @throws Exception
     * @return bool
     */
    public static function auto_compile_scss($scss_pname, $css_fname, $vars=array()) {
        if (DEVELOPMENT || !file_exists(BASE . $css_fname) || expSession::get('force_less_compile') == 1) {
            if (defined('SCSS_COMPILER')) {
                $scss_compiler = strtolower(SCSS_COMPILER);
            } else {
                $scss_compiler = 'scssphp';
            }
            switch ($scss_compiler) {
                case 'scssphp':
                default :
                    if (substr($scss_pname,-5,5) !== ".scss") {
                        $scss_pname .= ".scss";
                    }

                    // we need to account for leading _ with filename and missing filetype suffix
                    if (is_file(BASE . $scss_pname) || is_file(BASE . "_" . $scss_pname)) {
                        include_once(BASE . 'external/' . $scss_compiler . '/scss.inc.php');
//                        $scss = new \ScssPhp\ScssPhp\Compiler(array('cacheDir'=>BASE . 'tmp/css/','prefix'=>'_scss_','forceRefresh'=>true,'checkImportResolutions'=>true));
                        $scss = new \ScssPhp\ScssPhp\Compiler(array('cacheDir'=>BASE . 'tmp/css/','prefix'=>'_scss_'));
                        $scss_server = new \ScssPhp\Server\Server(BASE . 'tmp/css/', BASE . 'tmp/css/', $scss);

                        // load the cache
                        $scss_cname = str_replace("/", "_", $scss_pname);
                        $cache_fname = BASE . 'tmp/css/' . $scss_cname . ".cache";
                        $cache = BASE . $scss_pname;
                        if (empty($vars['swatch'])) {
                            $vars['swatch'] = '.';
                        }
                        if (file_exists($cache_fname)) {
                            $cache = unserialize(file_get_contents($cache_fname));
                            if (!empty($cache['vars']) && $vars != $cache['vars'] && !expJavascript::inAjaxAction()) {
                                $cache = BASE . $scss_pname;  // force a compile if the vars have changed
                            }
                        }

                        if (DEVELOPMENT && LESS_COMPILER_MAP && $scss_compiler === 'scssphp') {
                            $scss->setSourceMap((int)LESS_COMPILER_MAP);  // output .map file?
                            if (strlen(PATH_RELATIVE) != 1) {
                                $basePath = rtrim(realpath(str_replace(PATH_RELATIVE, '', BASE)), "\\/");
                            } else {
                                $basePath = rtrim(realpath(BASE), '\\/');
                            }
                            $scss->setSourceMapOptions(array(
//                                'outputSourceFiles' => true,  // include css source in .map file?
                                'sourceMapWriteTo'  => BASE . 'tmp/css/' . $scss_cname . ".map",
                                'sourceMapURL'      => PATH_RELATIVE . 'tmp/css/' . $scss_cname . ".map",
                                'sourceMapFilename' => PATH_RELATIVE . $css_fname,  // url location of .css file
                                'sourceMapBasepath' => $basePath,  // base (difference between) file & url locations, removed from ALL source files in .map
                                'sourceRoot'        => '/',
//                                'sourceMapRootpath' => PATH_RELATIVE . $scss_pname,  // tacked onto ALL source files in .map
                            ));
                        }

                        if (MINIFY==1 && MINIFY_LESS==1 && $scss_compiler === 'scssphp') {
                            $scss->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
                        } else {
                            $scss->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::EXPANDED);
                        }

//                        $scss->setVariables($vars);  // scssphp 1.4
                        $scss->addVariables($vars);  // scssphp 1.5+

                        try {
                            $file_updated = false;
                            $new_cache = $scss_server->cachedCompile($cache, false);

                            if (!is_array($cache) || $new_cache['updated'] > $cache['updated']) {
                                if (!empty($new_cache['compiled']) && $new_cache['compiled'] !== "\n") {
                                    // attempt to load and run the autoprefixer
                                    if (file_exists(BASE . 'external/php-autoprefixer/autoload.inc.php')) {
                                        include_once(BASE . 'external/php-autoprefixer/autoload.inc.php');
                                        // temporarily save any inline sourcemap info
                                        $map = explode("\n/*# sourceMappingURL=", $new_cache['compiled']);
                                        $new_cache['compiled'] = (new Padaliyajay\PHPAutoprefixer\Autoprefixer($new_cache['compiled']))->compile(!(MINIFY == 1 && MINIFY_LESS == 1));
                                        if (!empty($map[1])) {
                                            $new_cache['compiled'] .= "/*# sourceMappingURL=" . $map[1];
                                        }
                                    }
                                    $file_updated = true;
                                    // store compiler cache file
                                    $new_cache['vars'] = !empty($vars) ? $vars : null;
                                    file_put_contents($cache_fname, serialize($new_cache));
                                }
                            }

                            if ($file_updated || (!empty($new_cache['compiled']) && $new_cache['compiled'] !== "\n" && !file_exists(BASE . $css_fname))) {
                                // write compiled css file
                                $css_loc = pathinfo(BASE . $css_fname);
                                if (!is_dir($css_loc['dirname'])) {
                                    if (mkdir(
                                        $css_loc['dirname'],
                                        octdec(DIR_DEFAULT_MODE_STR + 0)
                                    ) === false) {
                                        flash('error', gt('SCSS compiler') . ': ' . gt('unable to create css folder') . ': ' . $css_loc['dirname'] );
                                        return false;
                                    }
                                } // create /css output folder if it doesn't exist
                                if (file_put_contents(BASE . $css_fname, $new_cache['compiled']) === false) {
                                    flash('error', gt('SCSS compiler') . ': ' . gt('unable to write') . ': ' . $css_fname);
                                    return false;
                                }
                                // save sourcemap file if needed
                                if (DEVELOPMENT && LESS_COMPILER_MAP == $scss::SOURCE_MAP_FILE && $scss_compiler === 'scssphp') {
                                    $scss_opts = $scss->getCompileOptions();
                                    file_put_contents($scss_opts['sourceMapOptions']['sourceMapWriteTo'], $new_cache['map']);
                                }
                            }
                            return true;
                        } catch(Exception $e) {
                            flash('error', gt('SCSS compiler') . ': ' . $scss_pname . ': ' . $e->getMessage());
                            if (file_exists(BASE . $css_fname))
                                return true; // old file exists so use it
                            else
                                return false;
                        }
                    } else {
                        flash('notice', $scss_pname . ' ' . gt('does not exist!'));
                        return false;
                    }
            }
        } else {
            return true;  // the .css file already exists and we're not in development
        }
    }

    /**
     * Generate an output .css filename from the input filename
     *   we assume the input filename has a .less, .scss, or no extension
     */
    public static function generate_filename($filename) {
        if (substr($filename, -5, 1) === ".") {
            $new_filename = substr($filename, 0, -4) . "css";
        } else {
            $new_filename = $filename . "css";
        }
        return $new_filename;
    }

    /**
     * Rebuild the entire set of 'core' .css files by pushing them all to the head (this one time)
     */
    public static function updateCoreCss(){
        $dir = BASE . 'framework/core/assets/less';
        $files = '';
        if (is_readable($dir)) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if (is_readable($dir . '/' . $file) && is_file($dir . '/' . $file) && substr($file, -5, 5) === '.less') {
                   $files .= substr($file, 0, -5) . ',';
                }
            }
            expCSS::pushToHead(array(
                "corecss"=>$files
            ));
        }
    }

}

?>