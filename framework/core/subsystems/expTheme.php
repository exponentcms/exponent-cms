<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * This is the class expTheme
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */

/** @define "BASE" "../../.." */
class expTheme
{

    public static function initialize()
    {
        global $auto_dirs2;

        // Initialize the theme subsystem 1.0 compatibility layer if requested
		if (defined('OLD_THEME_COMPATIBLE') && OLD_THEME_COMPATIBLE)
            require_once(BASE.'framework/core/compat/theme.php');

        if (!defined('DISPLAY_THEME')) {
            /* exdoc
             * The directory and class name of the current active theme.  This may be different
             * than the configured theme (DISPLAY_THEME_REAL) due to previewing.
             */
            define('DISPLAY_THEME', DISPLAY_THEME_REAL);
        }

        if (!defined('THEME_ABSOLUTE')) {
            /* exdoc
             * The absolute path to the current active theme's files.  This is similar to the BASE constant
             */
            define('THEME_ABSOLUTE', BASE . 'themes/' . DISPLAY_THEME . '/'); // This is the recommended way
        }

        if (!defined('THEME_RELATIVE')) {
            /* exdoc
             * The relative web path to the current active theme.  This is similar to the PATH_RELATIVE constant.
             */
            define('THEME_RELATIVE', PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/');
        }
        if (!defined('THEME_STYLE')) {
            /* exdoc
             * The name of the current active theme style.
             */
            define('THEME_STYLE', THEME_STYLE_REAL);
        }
        if (THEME_STYLE != '' && file_exists(BASE . 'themes/' . DISPLAY_THEME . '/config_' . THEME_STYLE . '.php')) {
            @include_once(BASE . 'themes/' . DISPLAY_THEME . '/config_' . THEME_STYLE . '.php');
        } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/config.php')) {
            @include_once(BASE . 'themes/' . DISPLAY_THEME . '/config.php');
        }
        if (!defined('BTN_SIZE')) {
            define('BTN_SIZE', 'medium');
        } // Awesome Button theme
        if (!defined('BTN_COLOR')) {
            define('BTN_COLOR', 'black');
        } // Awesome Button theme
        if (!defined('SWATCH')) {
            define('SWATCH', "''");
        } // Twitter Bootstrap theme

        // add our theme folder into autoload to prioritize custom (theme) modules
        array_unshift($auto_dirs2, BASE . 'themes/' . DISPLAY_THEME . '/modules');
    }

    public static function head($config = array())
    {
        echo self::headerInfo($config);
        self::advertiseRSS();
    }

    public static function headerInfo($config)
    {
        global $sectionObj, $validateTheme, $head_config, $auto_dirs, $less_vars, $framework;

        $validateTheme['headerinfo'] = true;
        // end checking for headerInfo

        // globalize header configuration
        $head_config = $config;

        // set theme framework type
        $framework = !empty($head_config['framework']) ? $head_config['framework'] : '';
        if (empty($framework)) {
            if (NEWUI) {
                $framework = 'newui';
            } else {
                $framework = 'yui';  // yui is the 2.x default framework
            }
        }
        expSession::set('framework', $framework);

        // set the global less variables from the head config
        if (!empty($config['lessvars'])) {
            $less_vars = $config['lessvars'];
        } else {
            $less_vars = array();
        }

        // check to see if we're in XHTML or HTML mode
        if (isset($config['xhtml']) && $config['xhtml'] == true) {
            define('XHTML', 1);
            define('XHTML_CLOSING', "/"); //default
        } else {
            define('XHTML', 0);
            define('XHTML_CLOSING', "");
        }

        // load primer, lessprimer, link (css) and lesscss & normalize CSS files
        if (!empty($config['css_primer']) || !empty($config['lessprimer']) || !empty($config['link']) || !empty($config['lesscss']) || !empty($config['normalize'])) {
            expCSS::pushToHead($config);
        };

        // default loading of primer CSS files to true if not set
        if (empty($config['css_primer']) && empty($config['lessprimer'])) {
            $head_config = array('css_primer' => true) + $head_config;
        }

        // parse & load core css files
        if (isset($config['css_core'])) {
            if (is_array($config['css_core'])) {
                $corecss = implode(",", $config['css_core']);
                expCSS::pushToHead(
                    array(
                        "corecss" => $corecss
                    )
                );
            }
        } else {
            $head_config['css_core'] = false;
        };

        // default loading of view based CSS inclusion is true if not set
        if (!empty($config['css_links']) || !isset($config['css_links'])) {
            $head_config['css_links'] = true;
        }

        // default theme css collecting is true if not set
        if (!empty($config['css_theme']) || !isset($config['css_theme'])) {
            $head_config['css_theme'] = true;
        }

        if (empty($sectionObj)) {
            return false;
        }

        // set up controls search order based on framework
        if (empty($head_config['framework'])) {
            $head_config['framework'] = '';
        }
        if (bs() || $framework == 'jquery') {
            array_unshift(
                $auto_dirs,
                BASE . 'framework/core/forms/controls/jquery'
            );
        }
        if (bs(true)) {
            array_unshift(
                $auto_dirs,
                BASE . 'framework/core/forms/controls/bootstrap'
            );
        }
        if (bs3(true)) {
            array_unshift(
                $auto_dirs,
                BASE . 'framework/core/forms/controls/bootstrap3'
            );
        }
        if (newui()) {
            expCSS::pushToHead(array(
                "lessprimer"=>"external/bootstrap3/less/newui.less",
//                "lessvars"=>array(
//                    'swatch'=>'cerulean',  // newui uses this swatch
//                    'themepath'=>'cerulean',  // hack to prevent crash
//                ),
            ));
            if (!defined("BTN_SIZE")) define("BTN_SIZE", 'small');
            array_unshift($auto_dirs, BASE . 'framework/core/forms/controls/newui');
        }
        array_unshift($auto_dirs, BASE . 'themes/' . DISPLAY_THEME . '/controls');

//        if (!expSession::is_set('framework') || expSession::get(
//                'framework'
//            ) != $head_config['framework']
//        ) {
//            expSession::set('framework', $head_config['framework']);
//        }
        // mark the theme framework

        $metainfo = self::pageMetaInfo();

        // default to showing all meta tags unless specifically set to false
        if (!isset($config['meta']['content_type'])) {
            $config['meta']['content_type'] = true;
        }
        if (!isset($config['meta']['content_language'])) {
            $config['meta']['content_language'] = true;
        }
        if (!isset($config['meta']['generator'])) {
            $config['meta']['generator'] = true;
        }
        if (!isset($config['meta']['keywords'])) {
            $config['meta']['keywords'] = true;
        }
        if (!isset($config['meta']['description'])) {
            $config['meta']['description'] = true;
        }
        if (!isset($config['meta']['canonical'])) {
            $config['meta']['canonical'] = true;
        }
        if (!isset($config['meta']['rich'])) {
            $config['meta']['rich'] = true;
        }
        if (!isset($config['meta']['fb'])) {
            $config['meta']['fb'] = true;
        }
        if (!isset($config['meta']['tw'])) {
            $config['meta']['tw'] = true;
        }
        if (!isset($config['meta']['viewport'])) {
            $config['meta']['viewport'] = true;
        }
        if (!isset($config['meta']['ie_compat'])) {
            $config['meta']['ie_compat'] = true;
        }

        $str = '';
        if ($config['meta']['content_type']) {
            $str .= '<meta charset="' . LANG_CHARSET . XHTML_CLOSING . '>' . "\n";  // html5
            $str .= "\t" . '<meta http-equiv="Content-Type" content="text/html; charset=' . LANG_CHARSET . '" ' . XHTML_CLOSING . '>' . "\n";  // html4 or xhtml?
        }
        if ($config['meta']['ie_compat']) {
            // turn off ie compatibility mode which will break the display
            $str .= "\t" . '<meta http-equiv="X-UA-Compatible" content="IE=edge"' . XHTML_CLOSING . '>' . "\n";
        }
        $str .= "\t" . '<title>' . $metainfo['title'] . "</title>\n";
        $locale = strtolower(str_replace('_', '-', LOCALE));
        if ($config['meta']['content_language']) {
            $str .= "\t" . '<meta http-equiv="Content-Language" content="' . $locale . '" ' . XHTML_CLOSING . '>' . "\n";
        }
        if ($config['meta']['generator']) {
            $str .= "\t" . '<meta name="Generator" content="Exponent Content Management System - v' . expVersion::getVersion(
                    true
                ) . self::getThemeDetails() . '" ' . XHTML_CLOSING . '>' . "\n";
        }
        if ($config['meta']['keywords']) {
            $str .= "\t" . '<meta name="Keywords" content="' . $metainfo['keywords'] . '" ' . XHTML_CLOSING . '>' . "\n";
        }
        if ($config['meta']['description']) {
            $str .= "\t" . '<meta name="Description" content="' . $metainfo['description'] . '" ' . XHTML_CLOSING . '>' . "\n";
        }
        if ($config['meta']['canonical'] && !empty($metainfo['canonical'])) {
            $str .= "\t" . '<link rel="canonical" href="' . $metainfo['canonical'] . '" ' . XHTML_CLOSING . '>' . "\n";
        }
        if ($config['meta']['rich'] && !empty($metainfo['rich'])) {
            $str .= "\t" . $metainfo['rich'] . "\n";
        }
        if ($config['meta']['fb'] && !empty($metainfo['fb'])) {
            foreach ($metainfo['fb'] as $key => $value) {
                if (!empty($value)) {
                    $str .= "\t" . '<meta property="og:' . $key . '" content="' . $value . '" ' . XHTML_CLOSING . '>' . "\n";
                }
            }
        }
        if ($config['meta']['tw'] && !empty($metainfo['tw'])) {
            foreach ($metainfo['tw'] as $key => $value) {
                if (!empty($value)) {
                    $str .= "\t" . '<meta name="twitter:' . $key . '" content="' . $value . '" ' . XHTML_CLOSING . '>' . "\n";
                }
            }
        }

        if ($metainfo['noindex'] || $metainfo['nofollow']) {
            $str .= "\t" . '<meta name="robots" content="' . (!empty($metainfo['noindex']) ? 'noindex' : '') . ' ' . ($metainfo['nofollow'] ? 'nofollow' : '') . '" ' . XHTML_CLOSING . '>' . "\n";
        }

        if (empty($config['viewport'])) {
            $viewport = 'width=device-width, user-scalable=yes';
        } else {
            if (!empty($config['viewport']['width'])) {
                $viewport = 'width=' . $config['viewport']['width'];
            } else {
                $viewport = 'width=device-width';
            }
            if (!empty($config['viewport']['height'])) {
                $viewport .= ', height=' . $config['viewport']['height'];
            }
            if (!empty($config['viewport']['initial_scale'])) {
                $viewport .= ' initial-scale=' . $config['viewport']['initial_scale'];
//            } else {
//                $viewport .= ', initial-scale=1.0';
            }
            if (!empty($config['viewport']['minimum_scale'])) {
                $viewport .= ', minimum-scale=' . $config['viewport']['minimum_scale'];
            }
            if (!empty($config['viewport']['maximum_scale'])) {
                $viewport .= ', maximum-scale=' . $config['viewport']['maximum_scale'];
            }
            if (!empty($config['viewport']['user_scalable'])) {
                $viewport .= ', user-scalable=' . ($config['viewport']['user_scalable'] ? "yes" : "no");
            } else {
                $viewport .= ', user-scalable=yes';
            }
        }
        if ($config['meta']['viewport']) {
            $str .= "\t" . '<meta name="viewport" content="' . $viewport . '" ' . XHTML_CLOSING . '>' . "\n";
        }

        // favicon
        if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/favicon.png')) {
            $str .= "\t" . '<link rel="icon" href="' . URL_FULL . 'themes/' . DISPLAY_THEME . '/favicon.png" type="image/png" ' . XHTML_CLOSING . '>' . "\n";
        } elseif (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/favicon.ico')) {
            $str .= "\t" . '<link rel="icon" href="' . URL_FULL . 'themes/' . DISPLAY_THEME . '/favicon.ico" type="image/x-icon" ' . XHTML_CLOSING . '>' . "\n";
        }
        // touch icons
        if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/apple-touch-icon.png')) {
            $str .= "\t" . '<link rel="apple-touch-icon" href="' . URL_FULL . 'themes/' . DISPLAY_THEME . '/apple-touch-icon.png" ' . XHTML_CLOSING . '>' . "\n";
        }
        if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/apple-touch-icon-precomposed.png')) {
            $str .= "\t" . '<link rel="apple-touch-icon-precomposed" href="' . URL_FULL . 'themes/' . DISPLAY_THEME . '/apple-touch-icon-precomposed.png" ' . XHTML_CLOSING . '>' . "\n";
        }

        // support for xmlrpc blog editors like Windows Live Writer, etc...
        if (USE_XMLRPC) {
            if (file_exists(BASE . 'rsd.xml')) {
                $str .= "\t" . '<link rel="EditURI" href="' . URL_FULL . 'rsd.xml" type="application/rsd+xml" ' . XHTML_CLOSING . '>' . "\n";
            }
            $str .= "\t" . '<link rel="wlwmanifest" href="' . URL_FULL . 'wlwmanifest.xml" type="application/wlwmanifest+xml" ' . XHTML_CLOSING . '>' . "\n";
        }

        // when minification is used, the comment below gets replaced when the buffer is dumped
        $str .= '<!-- MINIFY REPLACE -->';

        if ($config['meta']['ie_compat']) {
            // some IE 6 support
            $str .= "\t" . '<!--[if IE 6]><style type="text/css">  body { behavior: url(' . PATH_RELATIVE . 'external/csshover.htc); }</style><![endif]-->' . "\n";

            // css3 transform support for IE 6-8
//            $str .= "\t" . '<!--[if lt IE 9]><style type="text/css">  body { behavior: url(' . PATH_RELATIVE . 'external/ms-transform.htc); }</style><![endif]-->' . "\n";

            // html5 support for IE 6-8
            $str .= "\t" . '<!--[if lt IE 9]><script src="' . PATH_RELATIVE . 'external/html5shiv/html5shiv-shiv.js"></script><![endif]-->' . "\n";

            // media css support for IE 6-8
            $str .= "\t" . '<!--[if lt IE 9]><script src="' . PATH_RELATIVE . 'external/Respond-1.4.2/dest/respond.min.js"></script><![endif]-->' . "\n";

            // canvas support for IE 6-8 - now done by webshims
//            $str .= "\t" . '<!--[if lt IE 9]><script src="' . PATH_RELATIVE . 'external/excanvas.js"></script><![endif]-->' . "\n";

            //Win 8/IE 10 work around
            $str .= "\t" . '<!--[if IE 10]><link rel="stylesheet" href="' . PATH_RELATIVE . 'external/ie10-viewport-bug-workaround.css" type="text/css"' . XHTML_CLOSING . '><![endif]-->' . "\n";
            $str .= "\t" . '<!--[if IE 10]><script src="' . PATH_RELATIVE . 'external/ie10-viewport-bug-workaround.js"></script><![endif]-->' . "\n";
        }

        return $str;
    }

    public static function foot($params = array())
    {
        self::footerInfo($params);
    }

    public static function footerInfo($params = array())
    {
        // checks to see if the theme is calling footerInfo.
        global $validateTheme, $user, $jsForHead;

        $validateTheme['footerinfo'] = true;

        if (!empty($user->getsToolbar) && PRINTER_FRIENDLY != 1 && EXPORT_AS_PDF != 1 && !defined(
                'SOURCE_SELECTOR'
            ) && empty($params['hide-slingbar'])
        ) {
            self::module(array("controller" => "administration", "action" => "toolbar", "source" => "admin"));
        }

        if (MOBILE && is_readable(BASE . 'themes/' . DISPLAY_THEME . '/mobile/index.php')) {
            echo '<div style="text-align:center"><a href="', makeLink(
                    array('module' => 'administration', 'action' => 'togglemobile')
                ), '">', gt('View site in'), ' ', (MOBILE ? "Classic" : "Mobile"), ' ', gt('mode'), '</a></div>';
        }
        // load primer, lessprimer, & normalize CSS files

        if (!empty($params['src']) || !empty($params['content']) || !empty($params['yui3mods']) || !empty($params['jquery']) || !empty($params['bootstrap'])) {
            expJavascript::pushToFoot($params);
        }
        self::processCSSandJS();
        echo expJavascript::footJavascriptOutput();

        expSession::deleteVar(
            "last_POST"
        ); //ADK - putting this here so one form doesn't unset it before another form needs it.
        expSession::deleteVar(
            'last_post_errors'
        );
    }

    public static function pageMetaInfo()
    {
        global $sectionObj, $router;

        $metainfo = array();
        if (self::inAction() && (!empty($router->url_parts[0]) && expModules::controllerExists(
                    $router->url_parts[0]
                ))
        ) {
//            $classname = expModules::getControllerClassName($router->url_parts[0]);
//            $controller = new $classname();
            $controller = expModules::getController($router->url_parts[0]);
            $metainfo = $controller->metainfo();
        }
        if (empty($metainfo)) {
            $metainfo['title'] = empty($sectionObj->page_title) ? SITE_TITLE : $sectionObj->page_title;
            $metainfo['keywords'] = empty($sectionObj->keywords) ? SITE_KEYWORDS : $sectionObj->keywords;
            $metainfo['description'] = empty($sectionObj->description) ? SITE_DESCRIPTION : $sectionObj->description;
            $metainfo['canonical'] = empty($sectionObj->canonical) ? URL_FULL . $sectionObj->sef_name : $sectionObj->canonical;
            $metainfo['noindex'] = empty($sectionObj->noindex) ? false : $sectionObj->noindex;
            $metainfo['nofollow'] = empty($sectionObj->nofollow) ? false : $sectionObj->nofollow;
        }

        // clean up meta tag output
        foreach ($metainfo as $key=>$value) {
            $metainfo[$key] = expString::parseAndTrim($value, true);
        }
        return $metainfo;
    }

    public static function grabView($path, $filename)
    { //FIXME Not used
        $dirs = array(
            BASE . 'themes/' . DISPLAY_THEME . '/' . $path,
            BASE . 'framework/' . $path,
        );

        foreach ($dirs as $dir) {
            if (file_exists($dir . $filename . '.tpl')) {
                return $dir . $form . '.tpl';
            } //FIXME $form is not set??
        }

        return false;
    }

    public static function grabViews($path, $filter = '')
    { //FIXME Not used
        $dirs = array(
            BASE . 'framework/' . $path,
            BASE . 'themes/' . DISPLAY_THEME . '/' . $path,
        );

        $files = array();
        foreach ($dirs as $dir) {
            if (is_dir($dir) && is_readable($dir)) {
                $dh = opendir($dir);
                while (($filename = readdir($dh)) !== false) {
                    $file = $dir . $filename;
                    if (is_file($file)) { //FIXME this should be $file instead of $filename?
                        $files[$filename] = $file;
                    }
                }
            }
        }

        return $files;
    }

    public static function processCSSandJS()
    {
        global $jsForHead, $cssForHead;

        // returns string, either minified combo url or multiple link and script tags
        $jsForHead = expJavascript::parseJSFiles();
        $cssForHead = expCSS::parseCSSFiles();
    }

    public static function removeCss()
    {
        expFile::removeFilesInDirectory(BASE . 'tmp/minify'); // also clear the minify engine's cache
        return expFile::removeFilesInDirectory(BASE . 'tmp/css');
    }

    public static function clearSmartyCache()
    {
        self::removeSmartyCache();
        flash('message', gt("Smarty Cache has been cleared"));
        expHistory::back();
    }

    public static function removeSmartyCache()
    {
        expFile::removeFilesInDirectory(BASE . 'tmp/cache'); // alt location for cache
        return expFile::removeFilesInDirectory(BASE . 'tmp/views_c');
    }

    /** exdoc
     * Output <link /> elements for each RSS feed on the site
     *
     * @node Subsystems:Theme
     */
    public static function advertiseRSS()
    {
        if (defined('ADVERTISE_RSS') && ADVERTISE_RSS == 1) {
            echo "\t<!-- RSS Feeds -->\r\n";
            $rss = new expRss();
            $feeds = $rss->getFeeds('advertise=1');
            foreach ($feeds as $feed) {
                if ($feed->enable_rss) {
//					$title = empty($feed->feed_title) ? 'RSS' : htmlspecialchars($feed->feed_title, ENT_QUOTES);
                    $title = empty($feed->title) ? 'RSS - ' . ORGANIZATION_NAME : htmlspecialchars(
                        $feed->title,
                        ENT_QUOTES
                    );
                    $params['module'] = $feed->module;
                    $params['src'] = $feed->src;
//					echo "\t".'<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . expCore::makeRSSLink($params) . "\" />\n";
                    //FIXME need to use $feed instead of $params
                    echo "\t" . '<link rel="alternate" type="application/rss+xml" title="', $title, '" href="', expCore::makeLink(
                            array('controller' => 'rss', 'action' => 'feed', 'title' => $feed->sef_url)
                        ), "\" />\r\n";
                }
            }
        }
    }

    public static function loadActionMaps()
    {
        if (is_readable(BASE . 'themes/' . DISPLAY_THEME . '/action_maps.php')) {
            return include(BASE . 'themes/' . DISPLAY_THEME . '/action_maps.php');
        } else {
            return array();
        }
    }

    public static function satisfyThemeRequirements()
    {
        global $validateTheme;

        if ($validateTheme['headerinfo'] == false) {
            echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>expTheme::head() is a required function in your theme.  Please refer to the Exponent documentation for details:<br />
			<a href=\"http://docs.exponentcms.org/docs/current/header-info\" target=\"_blank\">http://docs.exponentcms.org/</a>
			</h1>";
            die();
        }

        if ($validateTheme['footerinfo'] == false) {
            echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>expTheme::foot() is a required function in your theme.  Please refer to the Exponent documentation for details:<br />
			<a href=\"http://docs.exponentcms.org/docs/current/footer-info\" target=\"_blank\">http://docs.exponentcms.org/</a>
			</h1>";
            die();
        }
    }

    public static function getTheme()
    {
        global $sectionObj, $router;

        // Grabs the action maps files for theme overrides
        $action_maps = self::loadActionMaps();

//		$mobile = self::is_mobile();

        // if we are in an action, get the particulars for the module
        if (self::inAction()) {
//            $module = isset($_REQUEST['module']) ? expString::sanitize(
//                $_REQUEST['module']
//            ) : expString::sanitize($_REQUEST['controller']);
            $module = isset($_REQUEST['module']) ? $_REQUEST['module'] : $_REQUEST['controller'];
        }

        // if we are in an action and have action maps to work with...
        if (self::inAction() && (!empty($action_maps[$module]) && (array_key_exists(
                        $_REQUEST['action'],
                        $action_maps[$module]
                    ) || array_key_exists('*', $action_maps[$module])))
        ) {
            $actionname = array_key_exists($_REQUEST['action'], $action_maps[$module]) ? $_REQUEST['action'] : '*';
            $actiontheme = explode(":", $action_maps[$module][$actionname]);

            // this resets the section object. we're suppressing notices with @ because getSectionObj sets constants, which cannot be changed
            // since this will be the second time Exponent calls this function on the page load.
            if (!empty($actiontheme[1])) {
                $sectionObj = @$router->getSectionObj($actiontheme[1]);
            }

            if ($actiontheme[0] == "default" || $actiontheme[0] == "Default" || $actiontheme[0] == "index") {
                if (MOBILE && is_readable(BASE . 'themes/' . DISPLAY_THEME . '/mobile/index.php')) {
                    $theme = BASE . 'themes/' . DISPLAY_THEME . '/mobile/index.php';
                } else {
                    $theme = BASE . 'themes/' . DISPLAY_THEME . '/index.php';
                }
            } elseif (is_readable(BASE . 'themes/' . DISPLAY_THEME . '/subthemes/' . $actiontheme[0] . '.php')) {
                if (MOBILE && is_readable(BASE . 'themes/' . DISPLAY_THEME . '/mobile/' . $actiontheme[0] . '.php')) {
                    $theme = BASE . 'themes/' . DISPLAY_THEME . '/mobile/' . $actiontheme[0] . '.php';
                } else {
                    $theme = BASE . 'themes/' . DISPLAY_THEME . '/subthemes/' . $actiontheme[0] . '.php';
                }
            } else {
                $theme = BASE . 'themes/' . DISPLAY_THEME . '/index.php';
            }
        } elseif ($sectionObj->subtheme != '' && is_readable(
                BASE . 'themes/' . DISPLAY_THEME . '/subthemes/' . $sectionObj->subtheme . '.php'
            )
        ) {
            if (MOBILE && is_readable(BASE . 'themes/' . DISPLAY_THEME . '/mobile/' . $sectionObj->subtheme . '.php')) {
                $theme = BASE . 'themes/' . DISPLAY_THEME . '/mobile/' . $sectionObj->subtheme . '.php';
            } elseif (MOBILE && is_readable(BASE . 'themes/' . DISPLAY_THEME . '/mobile/index.php')) {
                $theme = BASE . 'themes/' . DISPLAY_THEME . '/mobile/index.php';
            } else {
                $theme = BASE . 'themes/' . DISPLAY_THEME . '/subthemes/' . $sectionObj->subtheme . '.php';
            }
        } else {
            if (MOBILE && is_readable(BASE . 'themes/' . DISPLAY_THEME . '/mobile/index.php')) {
                $theme = BASE . 'themes/' . DISPLAY_THEME . '/mobile/index.php';
            } else {
                $theme = BASE . 'themes/' . DISPLAY_THEME . '/index.php';
            }
        }
        if (!is_readable($theme)) {
            if (is_readable(BASE . 'framework/core/index.php')) {
                $theme = BASE . 'framework/core/index.php';  // use the fallback bare essentials theme
            }
        }
        return $theme;
    }

    /** exdoc
     * @state <b>UNDOCUMENTED</b>
     *
     * @node  Undocumented
     *
     * @param bool   $include_default
     * @param string $theme
     *
     * @return array
     */
    public static function getSubthemes($include_default = true, $theme = DISPLAY_THEME)
    {
        $base = BASE . "themes/$theme/subthemes";
        // The array of subthemes.  If the theme has no subthemes directory,
        // or the directory is not readable by the web server, this empty array
        // will be returned (Unless the caller wanted us to include the default layout)
        $subs = array();
        if ($include_default == true) {
            // Caller wants us to include the default layout.
            $subs[''] = DEFAULT_VIEW; // Not really its intended use, but it works.
        }

        if (is_readable($base)) {
            // subthemes directory exists and is readable by the web server.  Continue on.
            $dh = opendir($base);
            // Read out all entries in the THEMEDIR/subthemes directory
            while (($s = readdir($dh)) !== false) {
                if (substr($s, -4, 4) == '.php' && substr($s, 0, 1) != '_' && is_file($base . "/$s") && is_readable(
                        $base . "/$s"
                    )
                ) {
                    // Only readable .php files are allowed to be subtheme files.
                    $subs[substr($s, 0, -4)] = substr($s, 0, -4);
                }
            }
            // Sort the subthemes by their keys (which are the same as the values)
            // using a natural string comparison function (PHP built-in)
            uksort($subs, 'strnatcmp');
        }
        return $subs;
    }

    public static function getPrinterFriendlyTheme()
    {
        global $framework;

        $common = 'framework/core/printer-friendly.php';
        $theme = 'themes/' . DISPLAY_THEME . '/printer-friendly.php';
        if (empty($framework)) {
            $fw = expSession::get('framework');
            $fwprint = 'framework/core/printer-friendly.' . $fw . '.php';
        } else {
            $fwprint = 'framework/core/printer-friendly.' . $framework . '.php';
        }

        if (is_readable($theme)) {
            return $theme;
        } elseif (is_readable($fwprint)) {
            return $fwprint;
        } elseif (is_readable($common)) {
            return $common;
        } else {
            return null;
        }
    }

    /** exdoc
     * Checks to see if the page is currently in an action.  Useful only if the theme does not use the self::main() function
     * Returns whether or not an action should be run.
     *
     * @node Subsystems:Theme
     * @return boolean
     */
    public static function inPreview()
    {
        $level = 99;
        if (expSession::is_set('uilevel')) {
            $level = expSession::get('uilevel');
        }
        return ($level == UILEVEL_PREVIEW);
    }

    public static function inAction($action=null)
    {
        return (isset($_REQUEST['action']) && (isset($_REQUEST['module']) || isset($_REQUEST['controller'])) && (!isset($action) || ($action == $_REQUEST['action'])));
    }

    public static function reRoutActionTo($theme = "")
    {
        if (empty($theme)) {
            return false;
        }
        if (self::inAction()) {
            include_once(BASE . "themes/" . DISPLAY_THEME . "/" . $theme);
            exit;
        }
        return false;
    }

    /** exdoc
     * Runs the appropriate action, by looking at the $_REQUEST variable.
     *
     * @node Subsystems:Theme
     * @return bool
     */
    public static function runAction()
    {
        global $user;

        if (self::inAction()) {
            if (!AUTHORIZED_SECTION && !expJavascript::inAjaxAction())
                notfoundController::handle_not_authorized();
//			if (expSession::is_set("themeopt_override")) {
//				$config = expSession::get("themeopt_override");
//				echo "<a href='".$config['mainpage']."'>".$config['backlinktext']."</a><br /><br />";
//			}

            //FIXME clean our passed parameters
//            foreach ($_REQUEST as $key=>$param) {  //FIXME need array sanitizer
//                $_REQUEST[$key] = expString::sanitize($param);
//            }
//            if (empty($_REQUEST['route_sanitized'])) {
            if (!$user->isAdmin())
                expString::sanitize($_REQUEST);
//            } elseif (empty($_REQUEST['array_sanitized'])) {
//                $tmp =1;  //FIXME we've already sanitized at this point
//            } else {
//                $tmp =1;  //FIXME we've already sanitized at this point
//            }

            //FIXME: module/controller glue code..remove ASAP
            $module = empty($_REQUEST['controller']) ? $_REQUEST['module'] : $_REQUEST['controller'];
//			$isController = expModules::controllerExists($module);

//			if ($isController && !isset($_REQUEST['_common'])) {
            if (expModules::controllerExists($module)) {
                // this is being set just in case the url said module=modname instead of controller=modname
                // with SEF URls turned on its not really an issue, but with them off some of the links
                // aren't being made correctly...depending on how the {link} plugin was used in the view.
                $_REQUEST['controller'] = $module;

//                if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'showall';
//                if (isset($_REQUEST['view']) && $_REQUEST['view'] != $_REQUEST['action']) {
//                    $test = explode('_',$_REQUEST['view']);
//                    if ($test[0] != $_REQUEST['action']) {
//                        $_REQUEST['view'] = $_REQUEST['action'].'_'.$_REQUEST['view'];
//                    }
//                } elseif (!empty($_REQUEST['action'])) {
//                    $_REQUEST['view'] = $_REQUEST['action'];
//                } else {
//                    $_REQUEST['view'] = 'showall';
//                }

                echo renderAction($_REQUEST);
//			} else {
//				if ($_REQUEST['action'] == 'index') {
//					$view = empty($_REQUEST['view']) ? 'Default' : $_REQUEST['view'];
//					$title = empty($_REQUEST['title']) ? '' : expString::sanitize($_REQUEST['title']);
//					$src = empty($_REQUEST['src']) ? null : expString::sanitize($_REQUEST['src']);
//					self::showModule($module, $view, $title, $src);
//					return true;
//				}
//
//				global $db, $user;  // these globals are needed for the old school actions which are loaded
//
//				// the only reason we should have a controller down in this section is if we are hitting a common action like
//				// userperms or groupperms...deal with it.
////				$loc = new stdClass();
////				$loc->mod = $module;
////				$loc->src = (isset($_REQUEST['src']) ? expString::sanitize($_REQUEST['src']) : "");
////				$loc->int = (!empty($_REQUEST['int']) ? strval(intval($_REQUEST['int'])) : "");
//                $loc = expCore::makeLocation($module,(isset($_REQUEST['src']) ? expString::sanitize($_REQUEST['src']) : ""),(!empty($_REQUEST['int']) ? strval(intval($_REQUEST['int'])) : ""));
//				//if (isset($_REQUEST['act'])) $loc->act = $_REQUEST['act'];
//
//				if (isset($_REQUEST['_common'])) {
//					$actfile = "/common/actions/" . $_REQUEST['action'] . ".php";
//				} else {
//					$actfile = "/" . $module . "/actions/" . $_REQUEST['action'] . ".php";
//				}
//
//				if (is_readable(BASE."themes/".DISPLAY_THEME."/modules".$actfile)) {
//                    include_once(BASE."themes/".DISPLAY_THEME."/modules".$actfile);
////				} elseif (is_readable(BASE.'framework/modules-1/'.$actfile)) {
////					include_once(BASE.'framework/modules-1/'.$actfile);
//				} else {
//					echo SITE_404_HTML . '<br /><br /><hr size="1" />';
//					echo sprintf(gt('No such module action').' : %1 : %2',strip_tags($module),strip_tags($_REQUEST['action']));
//					echo '<br />';
//				}
            }
        }
        return false;
    }

    public static function showAction($module, $action, $src = "", $params = array())
    { //FIXME only used by smarty functions, old school?
        global $user;

        $loc = expCore::makeLocation($module, (isset($src) ? $src : ""), (isset($int) ? $int : ""));

        $actfile = "/" . $module . "/actions/" . $action . ".php";
        if (isset($params)) {
//            foreach ($params as $key => $value) {  //FIXME need array sanitizer
////                $_GET[$key] = $value;
//                $_GET[$key] = expString::sanitize($value);
//            }
            if (!$user->isAdmin())
                expString::sanitize($_GET);
        }
        //if (isset($['_common'])) $actfile = "/common/actions/" . $_REQUEST['action'] . ".php";

        if (is_readable(BASE . "themes/" . DISPLAY_THEME . "/modules" . $actfile)) {
            include(BASE . "themes/" . DISPLAY_THEME . "/modules" . $actfile);
//   		} elseif (is_readable(BASE.'framework/modules-1/'.$actfile)) {
//   			include(BASE.'framework/modules-1/'.$actfile);
        } else {
            notfoundController::handle_not_found();
            echo '<br /><hr size="1" />';
            echo sprintf(
                gt('No such module action') . ' : %1 : %2',
                strip_tags($_REQUEST['module']),
                strip_tags($_REQUEST['action'])
            );
            echo '<br />';
        }
    }

    /** exdoc
     * Redirect User to Default Section
     *
     * @node Subsystems:Theme
     */
    public static function goDefaultSection()
    {
        $last_section = expSession::get("last_section");
        if (defined('SITE_DEFAULT_SECTION') && SITE_DEFAULT_SECTION != $last_section) {
            header("Location: " . URL_FULL . "index.php?section=" . SITE_DEFAULT_SECTION);
            exit();
        } else {
            global $db;

            $section = $db->selectObject("section", "public = 1 AND active = 1"); // grab first section, go there
            if ($section) {
                header("Location: " . URL_FULL . "index.php?section=" . $section->id);
                exit();
            } else {
                notfoundController::handle_not_found();
            }
        }
    }

    /** exdoc
     * Takes care of all the specifics of either showing a sectional container or running an action.
     *
     * @node Subsystems:Theme
     */
    public static function main()
    {
        global $db;

        if ((!defined('SOURCE_SELECTOR') || SOURCE_SELECTOR == 1)) {
            $last_section = expSession::get("last_section");
            $section = $db->selectObject("section", "id=" . $last_section);
            // View authorization will be taken care of by the runAction and mainContainer functions
            if (self::inAction()) {
                if (!PRINTER_FRIENDLY && !EXPORT_AS_PDF)
                    echo show_msg_queue();
                self::runAction();
            } else {
                if ($section == null) {
                    self::goDefaultSection();
                } else {
                    if (!PRINTER_FRIENDLY && !EXPORT_AS_PDF)
                        echo show_msg_queue();
                    self::mainContainer();
                }
            }
//        } else {
//            if (isset($_REQUEST['module'])) {
//                include_once(BASE."framework/modules/container/orphans_content.php");  //FIXME not sure how to convert this yet
//            } else {
//                echo gt('Select a module');
//            }
        }
    }

    /** exdoc
     * Useful only if theme does not use self::main
     *
     * @return void
     * @internal param bool $public Whether or not the page is public.
     * @node     Subsystems:Theme
     */
    public static function mainContainer()
    {
        global $router;

        if (!AUTHORIZED_SECTION) {
            // Set this so that a login on an Auth Denied page takes them back to the previously Auth-Denied page
            //			expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_SECTIONAL);
            expHistory::set('manageable', $router->params);
            notfoundController::handle_not_authorized();
            return;
        }

        if (PUBLIC_SECTION) {
            //			expHistory::flowSet(SYS_FLOW_PUBLIC,SYS_FLOW_SECTIONAL);
            expHistory::set('viewable', $router->params);
        } else {
            //			expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_SECTIONAL);
            expHistory::set('manageable', $router->params);
        }

        #   if (expSession::is_set("themeopt_override")) {
        #       $config = expSession::get("themeopt_override");
//   			self::showSectionalModule("containermodule","Default","","@section",false,true);  //FIXME change to showModule call
        self::module(
            array(
                "controller" => "container",
                "action"     => "showall",
                "view"       => "showall",
                "source"     => "@section",
                "scope"      => "sectional"
            )
        );

        #   } else {
        #       self::showSectionalModule("containermodule","Default","","@section");
        #   }
    }

    /** exdoc
     * Calls the necessary methods to show a specific module, in a section-sensitive way.
     *
     * @param string $module   The classname of the module to display
     * @param string $view     The name of the view to display the module with
     * @param string $title    The title of the module (support is view-dependent)
     * @param string $prefix   The prefix of the module's source.  The current section id will be appended to this
     * @param bool   $pickable Whether or not the module is pickable in the Source Picker.
     * @param bool   $hide_menu
     *
     * @return void
     * @node Subsystems:Theme
     * @deprecated 2.2.1
     */
    public static function showSectionalModule(
        $module,
        $view,
        $title,
        $prefix = null,
        $pickable = false,
        $hide_menu = false
    ) {
        global $module_scope;

        self::deprecated('expTheme::module()', $module, $view);
        $module = expModules::getModuleName($module); //FIXME patch to cleanup module name
        if ($prefix == null) {
            $prefix = "@section";
        }

        $src = $prefix;

//		if (expSession::is_set("themeopt_override")) {
//			$config = expSession::get("themeopt_override");
//			if (in_array($module,$config['ignore_mods'])) return;
//			$src = $config['src_prefix'].$prefix;  //FIXME there is no such config index
//			$section = null;
//		} else {
        global $sectionObj;

        //$last_section = expSession::get("last_section");
        //$section = $db->selectObject("section","id=".$last_section);
        $src .= $sectionObj->id;
//		}
        $module_scope[$src][$module] = new stdClass();
        $module_scope[$src][$module]->scope = 'sectional';

        self::showModule($module, $view, $title, $src, false, null, $hide_menu);
    }

    /** exdoc
     * Calls the necessary methods to show a specific module in such a way that the current
     * section displays the same content as its top-level parent and all of the top-level parent's
     * children, grand-children, grand-grand-children, etc.
     *
     * @param string $module   The classname of the module to display
     * @param string $view     The name of the view to display the module with
     * @param string $title    The title of the module (support is view-dependent)
     * @param string $prefix   The prefix of the module's source.  The current section id will be appended to this
     * @param bool   $pickable Whether or not the module is pickable in the Source Picker.
     * @param bool   $hide_menu
     *
     * @node Subsystems:Theme
     * @deprecated 2.2.1
     */
    public static function showTopSectionalModule(
        $module,
        $view,
        $title,
        $prefix = null,
        $pickable = false,
        $hide_menu = false
    ) {
        global $db, $module_scope, $sectionObj;

        self::deprecated('expTheme::module()', $module, $view);
        $module = expModules::getModuleName($module); //FIXME patch to cleanup module name
        if ($prefix == null) {
            $prefix = "@section";
        }
//		$last_section = expSession::get("last_section");
//		$section = $db->selectObject("section","id=".$last_section);
        $section = $sectionObj; //FIXME let's try $sectionObj instead of last_section
//        $module_scope[$prefix.$section->id][$module] = new stdClass();
        $module_scope[$prefix . $section->id][$module]->scope = 'top-sectional';
        // Loop until we find the top level parent.
        while ($section->parent != 0) {
            $section = $db->selectObject("section", "id=" . $section->parent);
        }

        self::showModule($module, $view, $title, $prefix . $section->id, false, null, $hide_menu);
    }

    /** exdoc
     * Calls the necessary methods to show a specific controller, in a section-sensitive way.
     *
     * @param array $params
     *
     * @internal param string $module The classname of the module to display
     * @internal param string $view The name of the view to display the module with
     * @internal param string $title The title of the module (support is view-dependent)
     * @internal param string $prefix The prefix of the module's source.  The current section id will be appended to this
     * @internal param bool $pickable Whether or not the module is pickable in the Source Picker.
     * @internal param bool $hide_menu
     * @return void
     * @node     Subsystems:Theme
     * @deprecated 2.2.1
     */
    public static function showSectionalController($params = array())
    { //FIXME not used in base system (custom themes?)
        global $sectionObj, $module_scope;

        $src = "@section" . $sectionObj->id;
        $params['source'] = $src;
//        $module_scope[$params['source']][(isset($params['module'])?$params['module']:$params['controller'])] = new stdClass();
        $module_scope[$params['source']][(isset($params['module']) ? $params['module'] : $params['controller'])]->scope = 'sectional';
        $module = !empty($params['module']) ? $params['module'] : $params['controller'];
        $view = !empty($params['action']) ? $params['action'] : $params['view'];
        self::deprecated('expTheme::module()', $module, $view);
        self::module($params);
    }

    /**
     * @deprecated 2.2.1
     */
    public static function showController($params = array())
    {
        $module = !empty($params['module']) ? $params['module'] : $params['controller'];
        $view = !empty($params['action']) ? $params['action'] : $params['view'];
        self::deprecated('expTheme::module()', $module, $view);
        self::module($params);
//        global $sectionObj, $db, $module_scope;
//        if (empty($params)) {
//	        return false;
//        } elseif (isset($params['module'])) {
//            self::module($params);
//        } else if (isset($params['controller'])) {
//			$params['view'] = isset($params['view']) ? $params['view'] : $params['action'];
//			$params['title'] = isset($params['moduletitle']) ? $params['moduletitle'] : '';
//			$params['chrome'] = (!isset($params['chrome']) || (isset($params['chrome'])&&empty($params['chrome']))) ? true : false;
//			$params['scope'] = isset($params['scope']) ? $params['scope'] : 'global';
//
//			// set the controller and action to the one called via the function params
//			$requestvars = isset($params['params']) ? $params['params'] : array();
//			$requestvars['controller'] = $params['controller'];
//			$requestvars['action'] = isset($params['action']) ? $params['action'] : null;
//			$requestvars['view'] = isset($params['view']) ? $params['view'] : null;
//
//			// figure out the scope of the module and set the source accordingly
//			if ($params['scope'] == 'global') {
//				$params['source'] = isset($params['source']) ? $params['source'] : null;
//			} elseif ($params['scope'] == 'sectional') {
//				$params['source']  = isset($params['source']) ? $params['source'] : '@section';
//				$params['source'] .= $sectionObj->id;
//			} elseif ($params['scope'] == 'top-sectional') {
//				$params['source']  = isset($params['source']) ? $params['source'] : '@section';
//				$section = $sectionObj;
//				while ($section->parent > 0) $section = $db->selectObject("section","id=".$section->parent);
//				$params['source'] .= $section->id;
//			}
////            $module_scope[$params['source']][(isset($params['module'])?$params['module']:$params['controller'])] = new stdClass();
//            $module_scope[$params['source']][(isset($params['module'])?$params['module']:$params['controller'])]->scope = $params['scope'];
//			self::showModule(expModules::getControllerClassName($params['controller']),$params['view'],$params['title'],$params['source'],false,null,$params['chrome'],$requestvars);
//        }
//        return false;
    }

    /**
     * Entry point for displaying modules
     * Packages $params for calling showModule method
     *
     * @param array $params list of module parameters
     *
     * @return bool
     */
    public static function module($params)
    {
        global $db, $module_scope, $sectionObj;

        if (empty($params)) {
            return false;
        } elseif (isset($params['module']) && expModules::controllerExists($params['module'])) {
            // hack to add compatibility for modules converted to controllers, but still hard-coded the old way
            $params['controller'] = $params['module'];
            unset($params['module']);
        }
        if (!isset($params['action'])) {
            $params['action'] = 'showall';
        }
        if (isset($params['view']) && $params['view'] != $params['action']) {
            $test = explode('_', $params['view']);
            if ($test[0] != $params['action']) {
                $params['view'] = $params['action'] . '_' . $params['view'];
            }
        } elseif (!empty($params['action'])) {
            $params['view'] = $params['action'];
        } else {
            $params['view'] = 'showall';
        }
//	    if (isset($params['controller'])) {
        $controller = expModules::getModuleName($params['controller']);
//            $params['view'] = isset($params['view']) ? $params['view'] : $params['action'];
        $params['title'] = isset($params['moduletitle']) ? $params['moduletitle'] : '';
        $params['chrome'] = (!isset($params['chrome']) || (isset($params['chrome']) && empty($params['chrome']))) ? true : false;
        $params['scope'] = isset($params['scope']) ? $params['scope'] : 'global';

        // set the controller and action to the one called via the function params
        $requestvars = isset($params['params']) ? $params['params'] : array();
        $requestvars['controller'] = $controller;
        $requestvars['action'] = isset($params['action']) ? $params['action'] : null;
        $requestvars['view'] = isset($params['view']) ? $params['view'] : null;

        // figure out the scope of the module and set the source accordingly
        if ($params['scope'] == 'global') {
            $params['source'] = isset($params['source']) ? $params['source'] : null;
        } elseif ($params['scope'] == 'sectional') {
            $params['source'] = isset($params['source']) ? $params['source'] : '@section';
            $params['source'] .= $sectionObj->id;
        } elseif ($params['scope'] == 'top-sectional') {
            $params['source'] = isset($params['source']) ? $params['source'] : '@section';
            $section = $sectionObj;
            while ($section->parent > 0) {
                $section = $db->selectObject("section", "id=" . $section->parent);
            }
            $params['source'] .= $section->id;
        }
        $module_scope[$params['source']][$controller] = new stdClass();
        $module_scope[$params['source']][$controller]->scope = $params['scope'];
//            self::showModule(expModules::getControllerClassName($params['controller']),$params['view'],$params['title'],$params['source'],false,null,$params['chrome'],$requestvars);
        return self::showModule(
            $controller,
            $params['view'],
            $params['title'],
            $params['source'],
            false,
            null,
            $params['chrome'],
            $requestvars
        );
//        } elseif (isset($params['module'])) {
//            $module = expModules::getModuleClassName($params['module']);
//            $moduletitle = (isset($params['moduletitle'])) ? $params['moduletitle'] : "";
//            $source = (isset($params['source'])) ? $params['source'] : "";
//            $chrome = (isset($params['chrome'])) ? $params['chrome'] : false;
//            $scope = (isset($params['scope'])) ? $params['scope'] : "global";
//
//            if ($scope=="global") {
//                self::showModule($module,$params['view'],$moduletitle,$source,false,null,$chrome);
//            }
//            if ($scope=="top-sectional") {
////                self::showTopSectionalModule($params['module']."module", //module
////                                             $params['view'], //view
////                                             $moduletitle, // Title
////                                             $source, // source
////                                             false, // used to apply to source picker. does nothing now.
////                                             $chrome // Show chrome
////                                            );
//                if ($source == null) $source = "@section";
//                //FIXME - $section might be empty!  We're getting it from last_section instead of sectionObj??
////                $last_section = expSession::get("last_section");
////                $section = $db->selectObject("section","id=".$last_section);
//                $section = $sectionObj;  //FIXME let's try $sectionObj instead of last_section
//                // Loop until we find the top level parent.
//                while ($section->parent != 0) $section = $db->selectObject("section","id=".$section->parent);
//                $module_scope[$source.$section->id][$module]= new stdClass();
//                $module_scope[$source.$section->id][$module]->scope = 'top-sectional';
//                self::showModule($module,$params['view'],$moduletitle,$source.$section->id,false,null,$chrome);
//            }
//            if ($scope=="sectional") {
////                self::showSectionalModule($params['module']."module", //module
////                                          $params['view'], //view
////                                          $moduletitle, // title
////                                          $source, // source/prefix
////                                          false, // used to apply to source picker. does nothing now.
////                                          $chrome // Show chrome
////                                        );
//                if ($source == null) $source = "@section";
//                $src = $source;
//                $src .= $sectionObj->id;
//                $module_scope[$src][$module] = new stdClass();
//                $module_scope[$src][$module]->scope = 'sectional';
//                self::showModule($module,$params['view'],$moduletitle,$src,false,null,$chrome);
//            }
//        }
//        return false;
    }

    /** exdoc
     * Calls the necessary methods to show a specific module - NOT intended to be called directly from theme
     *
     * @param string $module   The classname of the module to display
     * @param string $view     The name of the view to display the module with
     * @param string $title    The title of the module (support is view-dependent)
     * @param string $source   The source of the module.
     * @param bool   $pickable Whether or not the module is pickable in the Source Picker.
     * @param null   $section
     * @param bool   $hide_menu
     * @param array  $params
     *
     * @return void
     * @node Subsystems:Theme
     */
    public static function showModule(
        $module,
        $view = "Default",
        $title = "",
        $source = null,
        $pickable = false,
        $section = null,
        $hide_menu = false,
        $params = array()
    ) {
        $module = expModules::getModuleName($module); //FIXME patch to cleanup module name
        if (!AUTHORIZED_SECTION && $module != 'navigation' && $module != 'login') {
            return;
        }

        global $db, $sectionObj, $module_scope;

        // Ensure that we have a section
        //FJD - changed to $sectionObj
        if ($sectionObj == null) {
            $section_id = expSession::get('last_section');
            if ($section_id == null) {
                $section_id = SITE_DEFAULT_SECTION;
            }
            $sectionObj = $db->selectObject('section', 'id=' . $section_id);
            //$section->id = $section_id;
        }
        if ($module == "login" && defined('PREVIEW_READONLY') && PREVIEW_READONLY == 1) {
            return;
        }

//		if (expSession::is_set("themeopt_override")) {
//			$config = expSession::get("themeopt_override");
//			if (in_array($module,$config['ignore_mods'])) return;
//		}
        if (empty($params['action'])) {
            $params['action'] = $view;
        }
        $loc = expCore::makeLocation($module, $source . "");

        if (empty($module_scope[$source][$module]->scope)) {
            $module_scope[$source][$module] = new stdClass();
            $module_scope[$source][$module]->scope = 'global';
        }
        // make sure we've added this module to the sectionref table
        $secref = $db->selectObject("sectionref", "module='$module' AND source='" . $loc->src . "'");
        if ($secref == null) {
            $secref = new stdClass();
            $secref->module = $module;
            $secref->source = $loc->src;
            $secref->internal = "";
            $secref->refcount = 1000; // only hard-coded modules should be missing
            if ($sectionObj != null) {
                $secref->section = $sectionObj->id;
            }
//			  $secref->is_original = 1;
            $db->insertObject($secref, 'sectionref');
//        } elseif ($sectionObj != null && $secref->section != $sectionObj->id) {
//            $secref->section = $sectionObj->id;
//            $db->updateObject($secref, 'sectionref');
        }
        // add (hard-coded) modules to the container table, nested containers added in container showall method??
        $container = $db->selectObject('container', "internal='" . serialize($loc) . "'");
        if (empty($container->id)) {
            //if container isn't here already, then create it...hard-coded from theme template
            $newcontainer = new stdClass();
            $newcontainer->internal = serialize($loc);
            $newcontainer->external = serialize(null);
            $newcontainer->title = $title;
            $newcontainer->view = $view;
            $newcontainer->action = $params['action'];
            $newcontainer->id = $db->insertObject($newcontainer, 'container');
        }
        if (empty($title) && !empty($container->title)) {
            $title = $container->title;
        }
//		$iscontroller = expModules::controllerExists($module);

        if (defined('SELECTOR') && call_user_func(array(expModules::getModuleClassName($module), "hasSources"))) {
            containerController::wrapOutput($module, $view, $loc, $title);
        } else {
//			if (is_callable(array($module,"show")) || $iscontroller) {
            if (expModules::controllerExists($module)) {
                // FIXME: we are checking here for a new MVC style controller or an old school module. We only need to perform
                // this check until we get the old modules all gone...until then we have the check and a lot of code duplication
                // in the if blocks below...oh well, that's life.
//				if (!$iscontroller) {
////					if ((!$hide_menu && $loc->mod != "containermodule" && (call_user_func(array($module,"hasSources")) || $db->tableExists($loc->mod."_config")))) {
//                    if ((!$hide_menu && (call_user_func(array($module,"hasSources")) || $db->tableExists($loc->mod."_config")))) {
//                        $container = new stdClass();  //php 5.4
//						$container->permissions = array(
//							'manage'=>(expPermissions::check('manage',$loc) ? 1 : 0),
//							'configure'=>(expPermissions::check('configure',$loc) ? 1 : 0)
//						);
//
//						if ($container->permissions['manage'] || $container->permissions['configure']) {
//							$container->randomizer = mt_rand(1,ceil(microtime(1)));
//							$container->view = $view;
//							$container->info['class'] = expModules::getModuleClassName($loc->mod);
//							$container->info['module'] = call_user_func(array($module,"name"));
//							$container->info['source'] = $loc->src;
//                            $container->info['scope'] = $module_scope[$source][$module]->scope;
//							$container->info['hasConfig'] = $db->tableExists($loc->mod."_config");
////							$template = new template('containermodule','_hardcoded_module_menu',$loc);
////                            $template = new template('containerController','_hardcoded_module_menu',$loc,false,'controllers');
//                            $c2 = new containerController();
//                            $template = expTemplate::get_template_for_action($c2,'_hardcoded_module_menu');
//							$template->assign('container', $container);
//							$template->output();
//						}
//					}
//				} else {
                // if we hit here we're dealing with a hard-coded controller...not a module
                if (!$hide_menu && $loc->mod != "container") {
                    $controller = expModules::getController($module);
//                        $controller = expModules::getControllerClassName($module);
                    $hccontainer = new stdClass(); //php 5.4
                    $hccontainer->permissions = array(
                        'manage'    => (expPermissions::check('manage', $loc) ? 1 : 0),
                        'configure' => (expPermissions::check('configure', $loc) ? 1 : 0)
                    );

                    if ($hccontainer->permissions['manage'] || $hccontainer->permissions['configure']) {
                        $hccontainer->randomizer = mt_rand(1, ceil(microtime(1)));
                        $hccontainer->view = $view;
                        $hccontainer->action = $params['action'];
                        $hccontainer->info['class'] = expModules::getModuleClassName($loc->mod);
                        $hccontainer->info['module'] = $controller->displayname();
//                            $hccontainer->info['module'] = $controller::displayname();
                        $hccontainer->info['source'] = $loc->src;
                        $hccontainer->info['scope'] = $module_scope[$source][$module]->scope;
//							$hccontainer->info['hasConfig'] = true;
//							$template = new template('containermodule','_hardcoded_module_menu',$loc);
//							$template = new template('containerController','_hardcoded_module_menu',$loc,false,'controllers');
                        $c2 = new containerController();
                        $template = expTemplate::get_template_for_action($c2, '_hardcoded_module_menu');
                        $template->assign('container', $hccontainer);
                        $template->output();
                    }
                }
//				}

//				if ($iscontroller) {
                $params['src'] = $loc->src;
                $params['controller'] = $module;
                $params['view'] = $view;
                $params['moduletitle'] = $title;
                return renderAction($params);
//				} else {
//					call_user_func(array($module,"show"),$view,$loc,$title);
//				}
            } else {
                echo sprintf(gt('The module "%s" was not found in the system.'), $module);
                return false;
            }
        }
    }

    public static function getThemeDetails() {
        $theme_file = DISPLAY_THEME;
        if (is_readable(BASE.'themes/'.$theme_file.'/class.php')) {
            // Need to avoid the duplicate theme problem.
            if (!class_exists($theme_file)) {
                include_once(BASE.'themes/'.$theme_file.'/class.php');
            }

            if (class_exists($theme_file)) {
                // Need to avoid instantiating non-existent classes.
                $theme = new $theme_file();
                return ' ' . gt('using') . ' ' . $theme->name() . ' ' . gt('by') . ' ' . $theme->author();
            }
        }
        return '';
    }

    /**
     * Return the color style for the current framework
     *
     * @param        $color
     *
     * @return mixed|string
     */
    public static function buttonColor($color = null)
    {
        $colors = array(
            'green'   => 'btn-success',
            'blue'    => 'btn-primary',
            'red'     => 'btn-danger',
            'magenta' => 'btn-danger',
            'orange'  => 'btn-warning',
            'yellow'  => 'btn-warning',
            'grey'    => 'btn-default',
            'purple'  => 'btn-info',
            'black'   => 'btn-inverse',
            'pink'    => 'btn-danger',
        );
        if (bs()) {
            if (!empty($colors[$color])) { // awesome to bootstrap button conversion
                $found = $colors[$color];
            } else {
                $found = 'btn-default';
            }
        } else {
            $found = array_search($color, $colors); // bootstrap to awesome button conversion?
            if (empty($found)) {
                $found = $color;
            } else {
                $found = BTN_COLOR;
            }
        }
        return $found;
    }

    /**
     * Return the button size for the current framework
     *
     * @param        $size
     *
     * @return mixed|string
     */
    public static function buttonSize($size = null)
    {
        if (bs2()) {
            if (BTN_SIZE == 'large' || (!empty($size) && $size == 'large')) {
                $btn_size = ''; // actually default size, NOT true bootstrap large
            } elseif (BTN_SIZE == 'small' || (!empty($size) && $size == 'small')) {
                $btn_size = 'btn-mini';
            } else { // medium
                $btn_size = 'btn-small';
            }
            return $btn_size;
        } elseif (bs3()) {
            if (BTN_SIZE == 'large' || (!empty($size) && $size == 'large')) {
                $btn_size = 'btn-lg';
            } elseif (BTN_SIZE == 'small' || (!empty($size) && $size == 'small')) {
                $btn_size = 'btn-sm';
            } elseif (BTN_SIZE == 'extrasmall' || (!empty($size) && $size == 'extrasmall')) {
                $btn_size = 'btn-xs';
            } else { // medium
                $btn_size = '';
            }
            return $btn_size;
        } else {
            if (empty($size)) {
                $size = BTN_SIZE;
            }
            return $size;
        }
    }

    /**
     * Return the button color and size style for the current framework
     *
     * @param null   $color
     * @param        $size
     *
     * @return mixed|string
     */
    public static function buttonStyle($color = null, $size = null)
    {
        if (bs()) {
            $btn_class = 'btn ' . self::buttonColor($color) . ' ' . self::buttonSize($size);
        } else {
            $btn_size = !empty($size) ? $size : BTN_SIZE;
            $btn_color = !empty($color) ? $color : BTN_COLOR;
            $btn_class = "awesome " . $btn_size . " " . $btn_color;
        }
        return $btn_class;
    }

    /**
     * Return the icon associated for the current frameowrk
     *
     * @param        $class
     *
     * @return stdClass|string
     */
    public static function buttonIcon($class, $size=null)
    {
        $btn_type = '';
        if (bs2()) {
            switch ($class) {
                case 'delete' :
                case 'delete-title' :
                    $class = "remove-sign";
                    $btn_type = "btn-danger"; // red
                    break;
                case 'add' :
                case 'add-title' :
                case 'add-body' :
                case 'switchtheme add' :
                    $class = "plus-sign";
                    $btn_type = "btn-success"; // green
                    break;
                case 'copy' :
                    $class = "copy";
                    break;
                case 'downloadfile' :
                case 'export' :
                    $class = "download-alt";
                    break;
                case 'uploadfile' :
                case 'import' :
                    $class = "upload-alt";
                    break;
                case 'manage' :
                    $class = "briefcase";
                    break;
                case 'merge' :
                case 'arrow_merge' :
                    $class = "signin";
                    break;
                case 'reranklink' :
                case 'alphasort' :
                    $class = "sort";
                    break;
                case 'configure' :
                    $class = "wrench";
                    break;
                case 'view' :
                    $class = "search";
                    break;
                case 'page_next' :
                    $class = 'double-angle-right';
                    break;
                case 'page_prev' :
                    $class = 'double-angle-left';
                    break;
                case 'password' :
                case 'change_password' :
                    $class = 'key';
                    break;
                case 'clean' :
                    $class = 'check';
                    break;
                case 'userperms' :
                    $class = 'user';
                    break;
                case 'groupperms' :
                    $class = 'group';
                    break;
                case 'monthviewlink' :
                case 'weekviewlink' :
                    $class = 'calendar';
                    break;
                case 'listviewlink' :
                    $class = 'list';
                    break;
                case 'adminviewlink' :
                    $class = 'cogs';
                    break;
                case 'approve' :
                    $class = "check";
                    $btn_type = "btn-success"; // green
                    break;
                case 'ajax' :
                    $class = "spinner icon-spin";
                    break;
            }
            $found = new stdClass();
            $found->type = $btn_type;
            $found->class = $class;
            $found->size = self::iconSize($size);
            $found->prefix = 'icon-';
            return $found;
        } elseif (bs3()) {
            switch ($class) {
                case 'delete' :
                case 'delete-title' :
                    $class = "times-circle";
                    $btn_type = "btn-danger";  // red
                    break;
                case 'add' :
                case 'add-title' :
                case 'add-body' :
                case 'switchtheme add' :
                    $class = "plus-circle";
                    $btn_type = "btn-success";  // green
                    break;
                case 'copy' :
                    $class = "files-o";
                    break;
                case 'downloadfile' :
                case 'export' :
                    $class = "download";
                    break;
                case 'uploadfile' :
                case 'import' :
                    $class = "upload";
                    break;
                case 'manage' :
                    $class = "briefcase";
                    break;
                case 'merge' :
                case 'arrow_merge' :
                    $class = "sign-in";
                    break;
                case 'reranklink' :
                case 'alphasort' :
                    $class = "sort";
                    break;
                case 'configure' :
                    $class = "wrench";
                    break;
                case 'view' :
                    $class = "search";
                    break;
                case 'page_next' :
                    $class ='angle-double-right';
                    break;
                case 'page_prev' :
                    $class = 'angle-double-left';
                    break;
                case 'password' :
                case 'change_password' :
                    $class = 'key';
                    break;
                case 'clean' :
                    $class = 'check-square-o';
                    break;
                case 'trash' :
                    $class = "trash-o";
                    break;
                case 'userperms' :
                    $class = 'user';
                    break;
                case 'groupperms' :
                    $class = 'group';
                    break;
                case 'monthviewlink' :
                case 'weekviewlink' :
                    $class = 'calendar';
                    break;
                case 'listviewlink' :
                    $class = 'list';
                    break;
                case 'adminviewlink' :
                    $class = 'cogs';
                    break;
                case 'approve' :
                    $class = "check";
                    $btn_type = "btn-success"; // green
                    break;
                case 'ajax' :
                    $class = "spinner fa-spin";
                    break;
            }
            $found = new stdClass();
            $found->type = $btn_type;
            $found->class = $class;
            $found->size = self::iconSize($size);
            $found->prefix = 'fa fa-';
            return $found;
        } else {
            return $class;
        }
    }

    /**
     * Return the full icon style string for the current framework
     *
     * @param        $class
     *
     * @return string
     */
    public static function iconStyle($class, $text = null) {
        $style = self::buttonIcon($class);
        if (!empty($style->prefix)) {
            if ($text) {
                return '<i class="' .$style->prefix . $style->class . '"></i> '. $text;
            } else {
                return $style->prefix . $style->class;
            }
        } else {
            return $style;
        }
    }

    /**
     * Return the icon size for the current framework
     *
     * @param        $size
     *
     * @return mixed|string
     */
    public static function iconSize($size = null)
    {
        if (bs2()) {
            if (BTN_SIZE == 'large' || (!empty($size) && $size == 'large')) {
                $icon_size = 'icon-large';
            } elseif (BTN_SIZE == 'small' || (!empty($size) && $size == 'small')) {
                $icon_size = '';
            } else { // medium
                $icon_size = 'icon-large';
            }
            return $icon_size;
        } elseif (bs3()) {
            if (BTN_SIZE == 'large' || (!empty($size) && $size == 'large')) {
                $icon_size = 'fa-lg';
            } elseif (BTN_SIZE == 'small' || (!empty($size) && $size == 'small')) {
                $icon_size = '';
            } else { // medium
                $icon_size = 'fa-lg';
            }
            return $icon_size;
        } else {
            return BTN_SIZE;
        }
    }

    public static function is_mobile()
    {
        $tablet_browser = 0;
        $mobile_browser = 0;

        if (preg_match(
            '/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i',
            strtolower($_SERVER['HTTP_USER_AGENT'])
        )
        ) {
            $tablet_browser++;
        }

        if (preg_match(
            '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i',
            strtolower($_SERVER['HTTP_USER_AGENT'])
        )
        ) {
            $mobile_browser++;
        }

        if ((!empty($_SERVER['HTTP_ACCEPT']) && strpos(
                    strtolower($_SERVER['HTTP_ACCEPT']),
                    'application/vnd.wap.xhtml+xml'
                ) > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))
        ) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ',
            'acs-',
            'alav',
            'alca',
            'amoi',
            'audi',
            'avan',
            'benq',
            'bird',
            'blac',
            'blaz',
            'brew',
            'cell',
            'cldc',
            'cmd-',
            'dang',
            'doco',
            'eric',
            'hipt',
            'inno',
            'ipaq',
            'java',
            'jigs',
            'kddi',
            'keji',
            'leno',
            'lg-c',
            'lg-d',
            'lg-g',
            'lge-',
            'maui',
            'maxo',
            'midp',
            'mits',
            'mmef',
            'mobi',
            'mot-',
            'moto',
            'mwbp',
            'nec-',
            'newt',
            'noki',
            'palm',
            'pana',
            'pant',
            'phil',
            'play',
            'port',
            'prox',
            'qwap',
            'sage',
            'sams',
            'sany',
            'sch-',
            'sec-',
            'send',
            'seri',
            'sgh-',
            'shar',
            'sie-',
            'siem',
            'smal',
            'smar',
            'sony',
            'sph-',
            'symb',
            't-mo',
            'teli',
            'tim-',
            'tosh',
            'tsm-',
            'upg1',
            'upsi',
            'vk-v',
            'voda',
            'wap-',
            'wapa',
            'wapi',
            'wapp',
            'wapr',
            'webc',
            'winw',
            'winw',
            'xda ',
            'xda-'
        );

        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'opera mini') > 0) {
            $mobile_browser++;
            //Check for tablets on opera mini alternative headers
            $stock_ua = strtolower(
                isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) ? $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'] : (isset($_SERVER['HTTP_DEVICE_STOCK_UA']) ? $_SERVER['HTTP_DEVICE_STOCK_UA'] : '')
            );
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
                $tablet_browser++;
            }
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
            $mobile_browser = 0;
        }

        if ($tablet_browser > 0) {
            // do something for tablet devices
//           print 'is tablet';
        } elseif ($mobile_browser > 0) {
            // do something for mobile devices
//            print 'is mobile';
        } else {
            // do something for everything else
//            print 'is desktop';
        }

        return $mobile_browser;
    }

    /**
     * Warn admin of obsolete theme methods
     *
     * @param string $newcall
     * @param null $controller
     * @param null $actionview
     */
    public static function deprecated($newcall = "expTheme::module()", $controller = null, $actionview = null)
    {
        global $user;

        if ($user->isAdmin() && DEVELOPMENT) {
            $trace = debug_backtrace();
            $caller = $trace[1];
            if (substr($caller['file'], -16, 6) == 'compat') {
                $caller = $trace[2];
            }
            $oldcall = $caller['function'];
            if ($caller['class'] == 'expTheme') {
                $oldcall = $caller['class'] . '::' . $oldcall;
            }
            $message = '<strong>' . $oldcall . '</strong> ' . gt(
                    'is deprecated and should be replaced by'
                ) . ' <strong>' . $newcall . '</strong>';
            if (!empty($controller)) {
                $message .= '<br>' . gt(
                        'for hard coded module'
                    ) . ' - <strong>' . $controller . ' / ' . $actionview . '</strong>';
            }
            $message .= '<br>' . gt('line') . ' #' . $caller['line'] . ' ' . gt('of') . $caller['file'];
            $message .= ' <a class="helplink" title="' . gt('Get Theme Update Help') . '" href="' . help::makeHelpLink(
                    'theme_update'
                ) . '" target="_blank">' . gt('Help') . '</a>';
            flash('notice', $message);
        }
    }

}

?>
