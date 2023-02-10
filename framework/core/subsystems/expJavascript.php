<?php
##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

/** @define "BASE" "../../.." */
/**
 * This is the class expJavascript
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
class expJavascript {

	public static function inAjaxAction() {
        if (!empty($_REQUEST['apikey'])) {
            return true;
        }
		return empty($_REQUEST['ajax_action']) ? false : true;
	}

	public static function requiresJSON() {
        if (!empty($_REQUEST['apikey']) || !empty($_REQUEST['jsonp'])) {
            return 'jsonp';
        }
        if (!empty($_REQUEST['json']) && !empty($_REQUEST['controller']) && $_REQUEST['controller'] === 'file' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'picker') {
            return false;  // elFinder coming back from Pixlr editor
        }
		return !empty($_REQUEST['json']) ? true : false;
	}

	public static function parseJSFiles() {
        global $expJS, $yui3js, $jqueryjs, $bootstrapjs, $head_config, $framework;

        $scripts = '';
        // remove duplicate scripts since it's inefficient and crashes minify
        $newexpJS = array();
        $usedJS = array();
        foreach($expJS as $eJS) {
            if (!in_array($eJS['fullpath'], $usedJS)) {
                $usedJS[] = $eJS['fullpath'];
                $newexpJS[$eJS['name']] = $eJS;
            }
        }
        $expJS = $newexpJS;
        ob_start();
  		include(BASE.'exponent.js.php');
        $exponent_js = ob_get_clean();
        if (MINIFY == 1 && MINIFY_INLINE_JS == 1) {
            include_once(BASE . 'external/minify/min/lib/JSMin.php');
            $exponent_js = JSMin::minify($exponent_js);
        } else {
            $scripts .= "<!-- EXPONENT namespace setup -->" . "\r\n";
        }
        $scripts .= '<script type="text/javascript" charset="utf-8">//<![CDATA[' . "\r\n" . $exponent_js . "\r\n" . '//]]></script>';

        if (MINIFY == 1 && MINIFY_LINKED_JS == 1) {
            // if we're minifying, we'll break our URLs apart at MINIFY_URL_LENGTH characters to allow it through
            // browser string limits
            $strlen = (ini_get("suhosin.get.max_value_length")==0) ? MINIFY_URL_LENGTH : ini_get("suhosin.get.max_value_length");
            $i = 0;
            $srt = array();
            $srt[$i] = '';
            if (!empty($yui3js)) {
                if (USE_CDN) {
                    $scripts .= '<script type="text/javascript" src="' . YUI3_RELATIVE . 'yui/yui-min.js"></script>' . "\r\n";
                } else {
                    $srt[$i] = YUI3_RELATIVE . 'yui/yui-min.js,';
                }
            }
            if (!empty($jqueryjs) || $framework === 'jquery' || bs()) {
//                if (strlen($srt[$i])+strlen(JQUERY_SCRIPT)<= $strlen && $i <= MINIFY_MAX_FILES) {
//                    $srt[$i] .= JQUERY_SCRIPT . ",";
//                } else {
//                    $i++;
//                    $srt[$i] = JQUERY_SCRIPT . ",";
//                }
                $browser = expUtil::browser_info();
                if (OLD_BROWSER_SUPPORT) {
                    if (isset($browser['firefox']) && $browser['firefox'] < 5.0) {
                        $scripts .= '
    <script type="text/javascript" src="' . JQUERY_RELATIVE . 'js/jquery-' . '1.11.3' . '.min.js' . '"></script>' . "\r\n";
                    } else {
                        $scripts .= '
    <!--[if lt IE 9]>
        <script type="text/javascript" src="' . JQUERY_SCRIPT . '"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
        <script type="text/javascript" src="' . JQUERY2_SCRIPT . '"></script>
    <!--<![endif]-->' . "\r\n";
                    }
                } else {
                    $scripts .= '<script type="text/javascript" src="' . JQUERY3_SCRIPT . '"></script>' . "\r\n";
                    if (LOAD_MIGRATE3) {
                        $scripts .= '<script type="text/javascript" src="' . JQUERY3_MIGRATE_SCRIPT . '"></script>' . "\r\n";
                    }
                }

                if (!empty($bootstrapjs)) {
                    if (USE_CDN) {
                        if (bs2()) {
                            $scripts .= BS2_SCRIPT . "\r\n";
                        } elseif (bs3()) {
                            $scripts .= BS3_SCRIPT . "\r\n";
                        } elseif (bs4()) {
                            $scripts .= BS4_SCRIPT . "\r\n";
                        } else {
                            $scripts .= BS5_SCRIPT . "\r\n";
                        }
                    } else {
                        if (bs2()) {
                            $bootstrappath = 'external/bootstrap/js/bootstrap-';
                        } elseif (bs3()) {
                            $bootstrappath = 'external/bootstrap3/js/';
                        } elseif (bs4()) {
                            $bootstrappath = 'external/bootstrap4/js/dist/';
                            $srt[$i] .= PATH_RELATIVE . $bootstrappath . 'popper.js,';
                            $srt[$i] .= PATH_RELATIVE . $bootstrappath . 'util.js,';
                        } else {
                            $bootstrappath = 'external/bootstrap5/js/dist/';
//                            $scripts .= "\t" . '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>' . "\r\n";
//                            $srt[$i] .= PATH_RELATIVE . $bootstrappath . 'dom/data.js,';
//                            $srt[$i] .= PATH_RELATIVE . $bootstrappath . 'dom/event-handler.js,';
//                            $srt[$i] .= PATH_RELATIVE . $bootstrappath . 'dom/manipulator.js,';
//                            $srt[$i] .= PATH_RELATIVE . $bootstrappath . 'dom/selector-engine.js,';
//                            $srt[$i] .= PATH_RELATIVE . $bootstrappath . 'base-component.js,';
                            $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . '../../dist/js/bootstrap.bundle.min.js"></script>' . "\r\n";
//                            $scripts .= BS5_SCRIPT . "\r\n";
                        }
                        if (!bs5()) {
                            foreach ($bootstrapjs as $mod) {
                                if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js')) {
                                    if (strlen($srt[$i]) + strlen(PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js') <= $strlen && $i <= MINIFY_MAX_FILES) {
                                        $srt[$i] .= PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js' . ",";
                                    } else {
                                        $i++;
                                        $srt[$i] = PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js' . ",";
                                    }
                                } elseif (file_exists(BASE . $bootstrappath . $mod . '.js')) {
                                    if (strlen($srt[$i]) + strlen(PATH_RELATIVE . $bootstrappath . $mod . '.js') <= $strlen && $i <= MINIFY_MAX_FILES) {
                                        $srt[$i] .= PATH_RELATIVE . $bootstrappath . $mod . '.js' . ",";
                                    } else {
                                        $i++;
                                        $srt[$i] = PATH_RELATIVE . $bootstrappath . $mod . '.js' . ",";
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($jqueryjs)) foreach ($jqueryjs as $mod) {
                    if ($mod === 'migrate' && !OLD_BROWSER_SUPPORT) {
                        if (DEVELOPMENT)
                            flash('warning', 'jQuery Migrate v1 load prevented while using jQuery v3');
                    } elseif ($mod === 'jqueryui') {
                        if (strlen($srt[$i]) + strlen(JQUERYUI_SCRIPT) <= $strlen && $i <= MINIFY_MAX_FILES) {
                            $srt[$i] .= JQUERYUI_SCRIPT . ",";
                        } else {
                            $i++;
                            $srt[$i] = JQUERYUI_SCRIPT . ",";
                        }
                        expCSS::pushToHead(array(
                            'css_primer'=>JQUERYUI_CSS
                        ));
                    } elseif ($mod === 'jquery.dataTables') {
                        $dt_js = JQUERY_RELATIVE . 'addons/js/jquery.dataTables.js';
                        if (bs5()) {
                            expCSS::pushToHead(array(
                       		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/dataTables.bootstrap5.css',
                       		    )
                       		);
                            $dt_js .= ',' . JQUERY_RELATIVE . 'addons/js/jquery/dataTables.bootstrap5.js';
                        } elseif (bs4()) {
                            expCSS::pushToHead(array(
                       		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/dataTables.bootstrap4.css',
                       		    )
                       		);
                            $dt_js .= ',' . JQUERY_RELATIVE . 'addons/js/jquery/dataTables.bootstrap4.js';
                        } elseif (bs3()) {
                            expCSS::pushToHead(array(
                       		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/dataTables.bootstrap.css',
                       		    )
                       		);
                            $dt_js .= ',' . JQUERY_RELATIVE . 'addons/js/jquery/dataTables.bootstrap.js';
                        } else {
                            expCSS::pushToHead(array(
                       		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/jquery.dataTables.css',
                       		    )
                       		);
                        }
                        if (strlen($srt[$i]) + strlen($dt_js) <= $strlen && $i <= MINIFY_MAX_FILES) {
                            $srt[$i] .= $dt_js . ",";
                        } else {
                            $i++;
                            $srt[$i] = $dt_js . ",";
                        }
                    } else {
                        if ($mod === 'jstree') {
                            $scripts .= "\t" . '<script type="text/javascript" src="' . JQUERY_RELATIVE . 'addons/js/' . $mod . '.js"></script>' . "\r\n";
                        } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js')) {
                            if (strlen($srt[$i]) + strlen(PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js') <= $strlen && $i <= MINIFY_MAX_FILES) {
                                $srt[$i] .= PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js' . ",";
                            } else {
                                $i++;
                                $srt[$i] = PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js' . ",";
                            }
                            if ((bs4() || bs5()) && file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.scss')) {
                                expCSS::pushToHead(array(
//                           		    "unique"=>$mod,
                                        "scssprimer" => PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.scss',
                                    )
                                );
                            } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.less')) {
                                expCSS::pushToHead(array(
//                           		    "unique"=>$mod,
                           		    "lessprimer"=>PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.less',
                           		    )
                           		);
                            } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.css')) {
                                expCSS::pushToHead(array(
//                           		    "unique"=>$mod,
                           		    "css_primer"=>PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.css',
                           		    )
                           		);
                            }
                        } elseif (file_exists(JQUERY_PATH . 'addons/js/' . $mod . '.js')) {
                            if (strlen($srt[$i])+strlen(JQUERY_RELATIVE . 'addons/js/' . $mod . '.js') <= $strlen && $i <= MINIFY_MAX_FILES) {
                                $srt[$i] .= JQUERY_RELATIVE . 'addons/js/' . $mod . '.js' . ",";
                            } else {
                                $i++;
                                $srt[$i] = JQUERY_RELATIVE . 'addons/js/'.$mod.'.js' . ",";
                            }
                            if ((bs4() || bs5()) && file_exists(JQUERY_PATH . 'addons/scss/' . $mod . '.scss')) {
                                expCSS::pushToHead(array(
                                        "scssprimer" => JQUERY_RELATIVE . 'addons/scss/' . $mod . '.scss',
                                    )
                                );
                            } elseif (file_exists(JQUERY_PATH . 'addons/less/' . $mod . '.less')) {
                                expCSS::pushToHead(array(
                           		    "lessprimer"=>JQUERY_RELATIVE . 'addons/less/' . $mod . '.less',
                           		    )
                           		);
                            } elseif (file_exists(JQUERY_PATH . 'addons/css/' . $mod . '.css')) {
                                expCSS::pushToHead(array(
                           		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/' . $mod . '.css',
                           		    )
                           		);
                            }
                        }
                    }
                }
            }

            foreach ($expJS as $file) {
                if (strpos($file['fullpath'], 'http') === 0 || strpos($file['fullpath'], '//') === 0)
                    $proto = true;
                else
                    $proto = false;
                if (!empty($file['fullpath']) && ($proto || file_exists($_SERVER['DOCUMENT_ROOT'] . $file['fullpath']))) {
                    if ($file['name'] === 'ckeditor' || $file['name'] === 'tinymce' || $proto) {
                        $scripts .= "\t".'<script type="text/javascript" src="'.$file['fullpath'] . '"></script>' . "\r\n";
                        continue;
                    }
                    if (strlen($srt[$i]) + strlen($file['fullpath']) <= $strlen && $i <= MINIFY_MAX_FILES) {
                        $srt[$i] .= $file['fullpath'] . ",";
                    } else {
                        $i++;
                        $srt[$i] = $file['fullpath'] . ",";
                    }
                }
            }

            foreach ($srt as $link) {
                $link = rtrim($link,",");
                $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . 'external/minify/min/index.php?f=' . $link . '&debug"></script>' . "\r\n";
            }
        } else {
            if (!empty($jqueryjs) || !empty($bootstrapjs) || $framework === 'jquery' || bs(true)) {
                $scripts .= "\r\n\t" . "<!-- jQuery -->";
                $browser = expUtil::browser_info();
                if (OLD_BROWSER_SUPPORT) {
                    if (isset($browser['firefox']) && $browser['firefox'] < 5.0) {
                        $scripts .= '
    <script type="text/javascript" src="' . JQUERY_RELATIVE . 'js/jquery-' . '1.11.3' . '.min.js' . '"></script>' . "\r\n";
                    } else {
                        $scripts .= '
    <!--[if lt IE 9]>
        <script type="text/javascript" src="' . JQUERY_SCRIPT . '"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
        <script type="text/javascript" src="' . JQUERY2_SCRIPT . '"></script>
    <!--<![endif]-->' . "\r\n";
                    }
                } else {
                    $scripts .= '<script type="text/javascript" src="' . JQUERY3_SCRIPT . '"></script>' . "\r\n";
                    if (LOAD_MIGRATE3) {
                        $scripts .= '<script type="text/javascript" src="' . JQUERY3_MIGRATE_SCRIPT . '"></script>' . "\r\n";
                    }
                }

                if (!empty($bootstrapjs)) {
                    $scripts .= "\t" . "<!-- Twitter Bootstrap Scripts -->" . "\r\n";
                    if (USE_CDN) {
                        if (bs2()) {
                            $scripts .= "\t" . BS2_SCRIPT . "\r\n";
                        } elseif (bs3()) {
                            $scripts .= "\t" . BS3_SCRIPT . "\r\n";
                        } elseif (bs4()) {
                            $scripts .= "\t" . BS4_SCRIPT . "\r\n";
                        } else {
                            $scripts .= "\t" . BS5_SCRIPT . "\r\n";
                        }
                    } else {
                        if (bs2()) {
                            $bootstrappath = 'external/bootstrap/js/bootstrap-';
                        } elseif (bs3()) {
                            $bootstrappath = 'external/bootstrap3/js/';
                        } elseif (bs4()) {
                            $bootstrappath = 'external/bootstrap4/js/dist/';
                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'popper.js"></script>' . "\r\n";
                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'util.js"></script>' . "\r\n";
                        } else {
                            $bootstrappath = 'external/bootstrap5/js/dist/';
//                            $scripts .= "\t" . '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/data.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/event-handler.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/manipulator.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/selector-engine.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'base-component.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'util/index.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'util/sanitizer.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/data.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/event-handler.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/manipulator.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'util/config.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/selector-engine.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'base-component.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'util/component-functions.js"></script>' . "\r\n";
//                            $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'util/template-factory.js"></script>' . "\r\n";
                            $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . '../../dist/js/bootstrap.bundle.min.js"></script>' . "\r\n";
//                            $scripts .= "\t" . BS5_SCRIPT . "\r\n";
                        }
                        if (!bs5()) {
                            foreach ($bootstrapjs as $mod) {
                                if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js')) {
                                    $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js"></script>' . "\r\n";
                                } elseif (file_exists(BASE . $bootstrappath . $mod . '.js')) {
                                    $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . $mod . '.js"></script>' . "\r\n";
                                }
                            }
                        }
                    }
                }

                if (!empty($jqueryjs)) {
                    $scripts .= "\t" . "<!-- jQuery Addon Scripts -->" . "\r\n";
                    foreach ($jqueryjs as $mod) {
                        if ($mod === 'migrate' && !OLD_BROWSER_SUPPORT) {
                            if (DEVELOPMENT)
                                flash('warning', 'jQuery Migrate v1 load prevented while using jQuery v3');
                        } elseif ($mod === 'jqueryui') {
                            $scripts .= "\t" . '<script type="text/javascript" src="' . JQUERYUI_SCRIPT . '"></script>' . "\r\n";
                            expCSS::pushToHead(
                                array(
                                    'css_primer' => JQUERYUI_CSS
                                )
                            );
                        } elseif ($mod === 'jquery.dataTables') {
                            $scripts .= "\t" . '<script type="text/javascript" src="' . JQUERY_RELATIVE . 'addons/js/jquery.dataTables.js"></script>' . "\r\n";
                            if (bs5()) {
                                $scripts .= "\t" . '<script type="text/javascript" src="' . JQUERY_RELATIVE . 'addons/js/dataTables.bootstrap5.js"></script>' . "\r\n";
                                expCSS::pushToHead(array(
                           		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/dataTables.bootstrap5.css',
                           		    )
                           		);
                            } elseif (bs4()) {
                                $scripts .= "\t" . '<script type="text/javascript" src="' . JQUERY_RELATIVE . 'addons/js/dataTables.bootstrap4.js"></script>' . "\r\n";
                                expCSS::pushToHead(array(
                           		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/dataTables.bootstrap4.css',
                           		    )
                           		);
                            } elseif (bs3()) {
                                $scripts .= "\t" . '<script type="text/javascript" src="' . JQUERY_RELATIVE . 'addons/js/dataTables.bootstrap.js"></script>' . "\r\n";
                                expCSS::pushToHead(array(
                           		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/dataTables.bootstrap.css',
                           		    )
                           		);
                            } else {
                                expCSS::pushToHead(array(
                           		    "css_primer"=>JQUERY_RELATIVE . 'addons/css/jquery.dataTables.css',
                           		    )
                           		);
                            }
                        } else {
                            if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js')) {
                                $scripts .= "\t" . '<script type="text/javascript" src="' . PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js"></script>' . "\r\n";
                                if ((bs4() || bs5()) && file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.scss')) {
                                    expCSS::pushToHead(
                                        array(
//                           		    "unique"=>$mod,
                                            "scssprimer" => PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.scss',
                                        )
                                    );
                                } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.less')) {
                                    expCSS::pushToHead(
                                        array(
//                           		    "unique"=>$mod,
                                            "lessprimer" => PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.less',
                                        )
                                    );
                                } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.css')) {
                                    expCSS::pushToHead(
                                        array(
//                           		    "unique"=>$mod,
                                            "css_primer" => PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.css',
                                        )
                                    );
                                }
                            } elseif (file_exists(JQUERY_PATH . 'addons/js/' . $mod . '.js')) {
                                $scripts .= "\t" . '<script type="text/javascript" src="' . JQUERY_RELATIVE . 'addons/js/' . $mod . '.js"></script>' . "\r\n";
                                if ((bs4() || bs5()) && file_exists(JQUERY_PATH . 'addons/scss/' . $mod . '.scss')) {
                                    expCSS::pushToHead(
                                        array(
                                            "scssprimer" => JQUERY_RELATIVE . 'addons/scss/' . $mod . '.scss',
                                        )
                                    );
                                } elseif (file_exists(JQUERY_PATH . 'addons/less/' . $mod . '.less')) {
                                    expCSS::pushToHead(
                                        array(
                                            "lessprimer" => JQUERY_RELATIVE . 'addons/less/' . $mod . '.less',
                                        )
                                    );
                                } elseif (file_exists(JQUERY_PATH . 'addons/css/' . $mod . '.css')) {
                                    expCSS::pushToHead(
                                        array(
                                            "css_primer" => JQUERY_RELATIVE . 'addons/css/' . $mod . '.css',
                                        )
                                    );
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($yui3js)) {
                $scripts .=  "\t" . "<!-- YUI3 Script -->" . "\r\n\t" . '<script type="text/javascript" src="' . YUI3_RELATIVE . 'yui/yui-min.js"></script>' . "\r\n";
            }

            if (!empty($expJS)) {
                $scripts .= "\t" . "<!-- Other Scripts -->" . "\r\n";
                foreach ($expJS as $file) {
                    $scripts .= "\t" . '<script type="text/javascript" src="' . $file['fullpath'] . '"></script>' . "\r\n";
                }
            }
            $scripts .= "\t" . "<!-- Inline Code -->" . "\r\n";
        }

        return $scripts;
	}

	public static function footJavascriptOutput() {
        global $jsForHead, $js2foot;

        $html = "";
        // need to have some control over which scripts execute first.
        // solution: alphabetical by unique
        if(!empty($js2foot)){
            ksort($js2foot);
            foreach($js2foot as $file){
                $html .= $file . "\r\n";
            }
        }
        if (MINIFY == 1 && MINIFY_INLINE_JS == 1) {
            include_once(BASE.'external/minify/min/lib/JSMin.php');
            $html = JSMin::minify($html);
        }
        return "\r\n" . $jsForHead . '<script type="text/javascript" charset="utf-8">//<![CDATA['."\r\n".$html."\r\n".'//]]></script>' . "\r\n";
	}

    public static function pushToFoot($params) {
        global $js2foot, $yui3js, $jqueryjs, $bootstrapjs, $expJS;

    	if (!empty($params['src'])) {
            if (is_array($params['src'])) {
                foreach ($params['src'] as $unique => $url) {
                    //if (file_exists(str_replace(PATH_RELATIVE,"",$src))) {
                    if (is_int($unique)) {
//                        $unique = "unique-" . microtime();  // must be unique for each call
                        $unique = $params['unique'] . "-" . $unique;  // must be unique for each call
                    }
                    $expJS[$unique] = array(
                        "name" => $unique,
                        "type" => 'js',
                        "fullpath" => $url
                    );
                    // } else {
                    //     flash('error',"Exponent could not find ".$src.". Check to make sure the path is correct.");
                    // }
                }
            } else {
                //$src = str_replace(URL_FULL,PATH_RELATIVE,$params['src']);
           	    $src = $params['src'];
                //FIXME we need to allow for an array of scripts with unique+index as name
           	    //if (file_exists(str_replace(PATH_RELATIVE,"",$src))) {
                    $expJS[$params['unique']] = array(
                        "name" => $params['unique'],
                        "type" => 'js',
                        "fullpath" => $src
                    );
                // } else {
                //     flash('error',"Exponent could not find ".$src.". Check to make sure the path is correct.");
                // }
            }
    	}

        // insert the yui2mods wrapper if needed
        if (isset($params['yui2mods']) && strpos($params['content'], "YUI(") === false) {
            if (empty($params['yui3mods']))
                $params['yui3mods'] = 1;
            $yui2mods = !empty($params['yui2mods']) ? $params['yui2mods'] : null;
            $toreplace = array('"',"'"," ");  // strip quotes
            $stripmodquotes = str_replace($toreplace, "", $yui2mods);
            $splitmods = explode(",",$stripmodquotes);

            $y3wrap = "YUI(EXPONENT.YUI3_CONFIG).use(";
            $y3wrap .= "'yui2-yahoo-dom-event', ";
            foreach ($splitmods as $mod) {
                if ($mod === "menu") {
                    $y3wrap .= "'yui2-container', ";
                }
                $y3wrap .= "'yui2-".$mod."', ";
            }
            $y3wrap .= "function(Y) {\r\n";
            $y3wrap .= "var YAHOO=Y.YUI2;\r\n";
            $y3wrap .= $params['content'];
            $y3wrap .= "});";

            $params['content'] = $y3wrap;
            $yui3js = 1;
        }

        // do universal yui3mods replace
		if (isset($params['content']) && stripos($params['content'], "use('*',") !== false && isset($params['yui3mods'])) {
            $params['content'] = str_replace("use('*',",('use(\''.str_replace(',','\',\'',$params['yui3mods']).'\','),$params['content']);
            $yui3js = 1;
		}

    	if(!empty($params['yui3mods'])){
//            $toreplace = array('"',"'"," ");
//            $stripmodquotes = str_replace($toreplace, "", $params['yui3mods']);
//            $splitmods = explode(",",$stripmodquotes);
//            foreach ($splitmods as $val){
//                $yui3js[$val] = $val;
//            }
            $yui3js = 1;
        }

        if(!empty($params['bootstrap'])){
            $toreplace = array('"',"'"," ");  // strip quotes
            $stripmodquotes = str_replace($toreplace, "", $params['bootstrap']);
            $splitmods = explode(",",$stripmodquotes);
            foreach ($splitmods as $val){
                $bootstrapjs[$val] = $val;
            }
        }

        if(!empty($params['jquery'])){
            $toreplace = array('"',"'"," ");  // strip quotes
            $stripmodquotes = str_replace($toreplace, "", $params['jquery']);
            $splitmods = explode(",",$stripmodquotes);
            foreach ($splitmods as $val){
                $jqueryjs[$val] = $val;
            }
        }

        if (isset($params['content'])) $js2foot[$params['unique']] = $params['content'];

        // if within an ajax call, output the javascript
    	if (self::inAjaxAction() && !EXPORT_AS_PDF) {
		    echo "<div class=\"io-execute-response\">";
            if (!empty($params['bootstrap'])) {
                // we assume jquery is already loaded
                $scripts = '';
                if (USE_CDN) {
                    if (bs2()) {
                        $scripts .= "\t" . BS2_SCRIPT . "\r\n";
                    } elseif (bs3()) {
                        $scripts .= "\t" . BS3_SCRIPT . "\r\n";
                    } elseif (bs4()) {
                        $scripts .= "\t" . BS4_SCRIPT . "\r\n";
                    } else {
                        $scripts .= "\t" . BS5_SCRIPT . "\r\n";
                    }
                } else {
                    if (bs2()) {
                        $bootstrappath = 'external/bootstrap/js/bootstrap-';
                    } elseif (bs3()) {
                        $bootstrappath = 'external/bootstrap3/js/';
                    } elseif (bs4()) {
                        $bootstrappath = 'external/bootstrap4/js/dist/';
                        $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'popper.js"></script>' . "\r\n";
                        $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'util.js"></script>' . "\r\n";
                    } else {
                        $bootstrappath = 'external/bootstrap5/js/dist/';
//                        $scripts .= '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>' . "\r\n";
//                        $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/data.js"></script>' . "\r\n";
//                        $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/event-handler.js"></script>' . "\r\n";
//                        $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/manipulator.js"></script>' . "\r\n";
//                        $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . 'dom/selector-engine.js"></script>' . "\r\n";
                        $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . '../../dist/js/bootstrap.bundle.min.js"></script>' . "\r\n";
//                        $scripts .= "\t" . BS5_SCRIPT . "\r\n";
                    }
                    if (!bs5()) {
                        foreach ($bootstrapjs as $mod) {
                            if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js')) {
                                $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js"></script>' . "\r\n";
                            } elseif (file_exists(BASE . $bootstrappath . $mod . '.js')) {
                                $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . $bootstrappath . $mod . '.js"></script>' . "\r\n";
                            }
                        }
                    }
                }
                echo $scripts;
       	    }
            if (!empty($params['jquery'])) {
                // we assume jquery is already loaded along with requested bootstrap scripts
                $scripts = '';
                foreach ($jqueryjs as $mod) {
                    if ($mod === 'jqueryui') {
                        $scripts .= '<script type="text/javascript" src="' . JQUERYUI_SCRIPT . '"></script>' . "\r\n";
                        expCSS::pushToHead(
                            array(
                                'css_primer' => JQUERYUI_CSS
                            )
                        );
                    } else {
                        if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js')) {
                            $scripts .= '<script type="text/javascript" src="' . PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.js"></script>' . "\r\n";
                            if ((bs4() || bs5()) && file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.scss')) {
                                expCSS::pushToHead(
                                    array(
//                           		    "unique"=>$mod,
                                        "scssprimer" => PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.scss',
                                    )
                                );
                            } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.less')) {
                                expCSS::pushToHead(
                                    array(
//                           		    "unique"=>$mod,
                                        "lessprimer" => PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.less',
                                    )
                                );
                            } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.css')) {
                                expCSS::pushToHead(
                                    array(
//                           		    "unique"=>$mod,
                                        "css_primer" => PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/js/' . $mod . '.css',
                                    )
                                );
                            }
                        } elseif (file_exists(JQUERY_PATH . 'addons/js/' . $mod . '.js')) {
                            $scripts .= '<script type="text/javascript" src="' . JQUERY_RELATIVE . 'addons/js/' . $mod . '.js"></script>' . "\r\n";
                                if (file_exists(JQUERY_PATH . 'addons/scss/' . $mod . '.scss')) {
                                    expCSS::pushToHead(
                                        array(
                                            "scssprimer" => JQUERY_RELATIVE . 'addons/scss/' . $mod . '.scss',
                                        )
                                    );
                                } elseif (file_exists(JQUERY_PATH . 'addons/less/' . $mod . '.less')) {
                                expCSS::pushToHead(
                                    array(
                                        "lessprimer" => JQUERY_RELATIVE . 'addons/less/' . $mod . '.less',
                                    )
                                );
                            } elseif (file_exists(JQUERY_PATH . 'addons/css/' . $mod . '.css')) {
                                expCSS::pushToHead(
                                    array(
                                        "css_primer" => JQUERY_RELATIVE . 'addons/css/' . $mod . '.css',
                                    )
                                );
                            }
                        }
                    }
                }
                echo $scripts;
       	    }
    	    if (!empty($params['src'])) {
                if (is_array($params['src'])) {
                    foreach ($params['src'] as $url) {
                        echo '<script type="text/javascript" src="', $url, '"></script>';
                    }
                } else {
                    echo '<script type="text/javascript" src="',$params['src'],'"></script>';
                }
    	    }
            if (!empty($params['content'])) {
                echo "
                <script id=\"", $params['unique'], "\" type=\"text/javascript\" charset=\"utf-8\">
                  ", $params['content'], "
                </script>";
            }
            echo "
		    </div>
		    ";
		    return true;
    	}
    }

    /**
     * @deprecated 2.3.4 in favor of expAjaxReply
     * @param int $replyCode
     * @param string $replyText
     * @param $data
     *
     * @return string
     */
	public static function ajaxReply($replyCode=200, $replyText='Ok', $data='') {
        expCore::deprecated('expAjaxReply::');
        $ajaxObj['replyCode'] = $replyCode;
		$ajaxObj['replyText'] = $replyText;
		if (isset($data)) {
			$ajaxObj['data'] = $data;
			if (is_array($data)) {
				$ajaxObj['replyCode'] = 201;
			} elseif (is_string($data)) {
				$ajaxObj['replyCode'] = 202;
			} elseif (is_bool($data)) {
				$ajaxObj['replyCode'] = 203;
			} elseif (empty($data)) {
				$ajaxObj['replyCode'] = 204;
			}
		}
		return json_encode($ajaxObj);
	}

	/** exdoc
	 * Takes a stdClass object from PHP, and generates the
	 * corresponding Javascript class function.  The data in the
	 * members of the PHP object is not important, only the
	 * presence and names of said members.  Returns the
	 * javascript class function code.
	 *
	 * @param Object $object The object to translate
	 * @param string $name What to call the class in javascript
     * @return string
     * @node Subsystems:Javascript
	 */
	public static function jClass($object, $name) {
		$otherclasses = array();
		$js = "function $name(";
		$js1 = "";
		foreach (get_object_vars($object) as $var=>$val) {
//			$js .= "var_$var, ";
//			$js1 .= "\tthis.var_$var = var_$var;\n";
            $js .= "$var, ";
            $js1 .= "\tthis.$var = $var;\n";
			if (is_object($val)) {
				$otherclasses[] = array($name . "_" . $var, $val);
			}
		}
		$js = substr($js, 0, -2) . ") {\n" . $js1 . "}\n";
		foreach ($otherclasses as $other) {
			echo "/// Other Object : ",$other[1], ", ", $other[0],"\n";
			$js .= "\n" . self::jClass($other[1], $other[0]);
		}
		return $js;
	}

	/** exdoc
	 * Takes a stdClass object from PHP, and generates the
	 * corresponding Javascript calls to make a new Javascript
	 * object.  In order for the resulting Javascript to function
	 * properly, a call to expJavascript_class must have been
	 * made previously, and the same $name attribute used. Returns
	 * the javascript code to create a new object.
	 *
	 * The data in the members of the PHP object will be used to
	 * populate the members of the new Javascript object.
	 *
	 * @param Object $object The object to translate
	 * @param string $name The name of the javascript class
     * @return string
     * @node Subsystems:Javascript
	 */
	public static function jObject($object, $name="Array") {
		$js = "new $name(";

		//PHP4: "foreach" does not work on object properties
		if (is_object($object)) {
			//transform the object into an array
			$object = get_object_vars($object);
		}

		foreach ($object as $var=>$val) {
			switch (gettype($val)){
				case "string":
					$js .= "'" . str_replace( array("'", "\r\n", "\n"),	array("&apos;", "\\r\\n", "\\n"), $val) . "'";
					break;
				case "array":
					$js .= self::jObject($val);
					break;
				case "object":
					$js .= self::jObject($val, $name . "_" . $var);
					break;
				default:
					$js .= '"' . $val . '"';
			}
			$js .= ', ';
		}

		//if there have been any values
		if($js !== "new $name(") {
			//remove the useless last ", "
			$js = substr($js, 0, -2);
		}

		//close with ")"
		return  $js . ")";
	}

    /**
     * Create a YUI2 Panel?
     * @param $params
     * @deprecated yui2
     */
    public static function panel($params) {
        $content = json_encode("<div class=\"pnlmsg\">".str_replace("\n", '', str_replace("\r\n", '', trim($params['content'])))."</div>");
        $id = "exppanel".$params['id'];
        $width  = !empty($params['width']) ? $params['width'] : "800px";
        $type  = !empty($params['type']) ? $params['type'] : "info";
        $dialog  = !empty($params['dialog']) ? explode(":",$params['dialog']) : "";
        $header  = !empty($params['header']) ? $params['header'] : "&#160;";
        //$footer  = !empty($params['footer']) ? $params['footer'] : "&#160;";
        $renderto  = !empty($params['renderto']) ? $params['renderto'] : 'document.body';
        $on  = !empty($params['on']) ? $params['on'] : 'click';
        $onnogo  = !empty($params['onnogo']) ? $params['onnogo'] : '';
        $onyesgo  = !empty($params['onyesgo']) ? $params['onyesgo'] : '';
        $trigger  = !empty($params['trigger']) ? '"'.$params['trigger'].'"' : 'selfpop';
        $zindex  = !empty($params['zindex']) ? $params['zindex'] : "50";
        //$hide  = !empty($params['hide']) ? $params['hide'] : "hide";
        $fixedcenter  = !empty($params['fixedcenter']) ? $params['fixedcenter'] : "true";
        $fade  = !empty($params['fade']) ? $params['fade'] : null;
        $modal  = !empty($params['modal']) ? $params['modal'] : "true";
        $draggable  = empty($params['draggable']) ? "false" : $params['draggable'];
        $constraintoviewport  = !empty($params['constraintoviewport']) ? $params['constraintoviewport'] : "true";
        $fade  = !empty($params['fade']) ? "effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:".$params['fade']."}," : "";
        $close  = !empty($params['close']) ? $params['close'] : "true";

        $script = "";
        if (is_array($dialog)) {
            $script .= "
                var handleYes = function(e,o) {
                    this.hide();" . "\r\n";
                    if ($onyesgo!="") {
                        $script .= "document.location = '".trim($onyesgo)."'";
                    };
            $script .= "};
                var handleNo = function(e,o) {
                    this.hide();" . "\r\n";
                    if ($onyesgo!="") {
                        $script .= "var textlink = '".trim($onnogo)."';";
                        $script .= 'document.location = textlink.replace(/&amp;/g,"&");';
                    };
            $script .= "};" . "\r\n";

            $script .= "var ".$id." = new YAHOO.widget.SimpleDialog('".$id."', {" . "\r\n";
            $script .= "buttons: [ { text:'".$dialog[0]."', handler:handleYes, isDefault:true }," . (!empty($dialog[1])?"{ text:'".$dialog[1]."',  handler:handleNo }":"") . " ]," . "\r\n";
            //$script .= "text: 'Do you want to continue?',";
        } else {
            $script .= "var ".$id." = new YAHOO.widget.Panel('".$id."', { " . "\r\n";
        }
//FIXME $hide & $footer are not defined below
        $script .= "fixedcenter:".$fixedcenter.",
                draggable:".$draggable.",
                modal:".$modal.",
                class:'exp-".$type." ".$hide."',
                zIndex:".$zindex.","
                .$fade.
                "width:'".$width."',
                visible:false,
                constraintoviewport:".$constraintoviewport.",
                close:".$close." } );" . "\r\n";

            $script .= $id.".setHeader('".$header."');" . "\r\n";
            $script .= "var pnlcontent = ".$content.";" . "\r\n";

            $script .= $id.".setBody('<span class=\"type-icon\"></span>'+pnlcontent);" . "\r\n";

//            $script .= $id.".setFooter('".$footer."</div>');" . "\r\n";
            $script .= $id.".render(".$renderto.");" . "\r\n";
            $script .= "YAHOO.util.Dom.addClass('".$id."','exp-".$type."');" . "\r\n";
            if ($hide==false) {
                $script .= "YAHOO.util.Dom.addClass('".$id."','".$hide."');" . "\r\n";
            }

        switch ($trigger) {
            case 'selfpop':
            $script .= "YAHOO.util.Event.onDOMReady(".$id.".show, ".$id.", true);" . "\r\n";
                break;

            default:
            $script .= "YAHOO.util.Event.on(".$trigger.", '".$on."', function(e,o){
                YAHOO.util.Event.stopEvent(e);
                o.show();
            }, ".$id.", true);";
            break;
        }

        self::pushToFoot(array(
            "unique"=>'pop-'.$params['id'],
            "yui2mods"=>'animation,container',
            "content"=>$script,
         ));
        expCSS::pushToHead(array(
            "corecss"=>"panels",
        ));
    }

}

?>