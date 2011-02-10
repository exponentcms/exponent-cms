<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Written and Designed by James Hunt
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

/* exdoc
 * The definition of this constant lets other parts of the system know
 * that the subsystem has been included for use.
 * @node Subsystems:Theme
 */
define("SYS_THEME",1);

$css_files = array();  // This array keeps track of all the css files that need to be included via the minify script
$jsfiles = array();
$validateTheme = array("headerinfo"=>false,"footerinfo"=>false);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_theme_loadCommonCSS() {
    global $css_files;

    $commondir = 'themes/common/css';

    if (is_dir(BASE . $commondir) && is_readable(BASE . $commondir) ) {
        $dh = opendir(BASE . $commondir);
        while (($cssfile = readdir($dh)) !== false) {
            $filename = BASE . $commondir.'/'.$cssfile;
            if ( is_file($filename) && substr($filename,-4,4) == ".css") {
                $css_files["1-common-".substr($cssfile,0,-4)] = PATH_RELATIVE.$commondir.'/'.$cssfile;
                if (is_readable('1-themes/'.DISPLAY_THEME.'/css/'.$cssfile)) {
                    $css_files["1-usertheme-".substr($cssfile,0,-4)] = PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/css/'.$cssfile;
                } elseif (is_readable('themes/'.DISPLAY_THEME.'/'.$cssfile)) {
                    $css_files["1-usertheme-".substr($cssfile,0,-4)] = PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/'.$cssfile;
                }
            }
        }
    }
}

function exponent_theme_cssPrimer($css_primer) {
	global $css_files;
	
	$i = 0;
    foreach ($css_primer as $css_file) {
        $css_files = array_merge(array("css_primer".$i=>$css_file), $css_files);  
        $i++;
    }
}

function exponent_theme_loadRequiredCSS() {
    global $css_files;

    $requireddir = 'themes/common/css/required/';
    $requiredthemedir = 'themes/'.DISPLAY_THEME.'/css/required/';
    if (is_dir(BASE . $requireddir) && is_readable(BASE . $requireddir) ) {
        $dh = opendir( BASE . $requireddir);
        while (($cssfile = readdir($dh)) !== false) {
            $filename = BASE . $requireddir.$cssfile;
            $themefilename = $requiredthemedir.$cssfile;
            if ( is_file($filename) && substr($filename,-4,4) == ".css") {
                $css_files["0-common-required-".substr($cssfile,0,-4)] = PATH_RELATIVE.$requireddir.$cssfile;
            }
            if (is_file($themefilename) && substr($themefilename,-4,4) == ".css") {
                $css_files["0-theme-required-".substr($cssfile,0,-4)] = PATH_RELATIVE.$requiredthemedir.$cssfile;
            }
        }
    }
    //eDebug($css_files);
}

/* exdoc
 * @function include_css()
 * checks for a css document to include on your page.
 * checks first in your theme/css/ folder, then in the 
 * root of your theme, then in themes/common/css/
 * @node Subsystems:Theme
 */
function exponent_theme_includeThemeCSS($files) {
    global $css_files;

    if (is_bool($files)) {
        global $css_files;
        //exponent_theme_resetCSS();
        //exponent_theme_loadYUICSS(array('menu'));
        //exponent_theme_loadExpDefaults();

        $cssdirs = array('themes/'.DISPLAY_THEME.'/css/', URL_FULL.'themes/'.DISPLAY_THEME.'/');
        
        foreach ($cssdirs as $cssdir) {
                if (is_dir($cssdir) && is_readable($cssdir) ) {
                        $dh = opendir($cssdir);
                        while (($cssfile = readdir($dh)) !== false) {
                                $filename = $cssdir.$cssfile;
                                if ( is_file($filename) && substr($filename,-4,4) == ".css" && !array_key_exists(substr("2-usertheme-".$cssfile,0,-4), $css_files)) {
                                        $css_files["2-usertheme-".substr($cssfile,0,-4)] = PATH_RELATIVE.$cssdir.$cssfile;
                                }
                        }
                }
        }
    } else {    
        foreach ($files as $file) {
            if (is_readable( BASE . 'themes/'.DISPLAY_THEME.'/css/'.$file.".css")) {
                $css_files["2-usertheme-".substr($file,0,-4)] = PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/css/'.$file.".css";
            } elseif (is_readable(BASE . 'themes/'.DISPLAY_THEME.'/'.$file.".css")) {
                $css_files["2-usertheme-".substr($file,0,-4)] = PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/'.$file.".css";
            }
        }
    }
    return $css_files;
    //eDebug($css_files);
}

function css_file_needs_rebuilt() {
    if (!is_readable(BASE.'tmp/css/exp-styles-min.css')) {
        return true;
    } else {
        return false;
    }
}


function rebuild_css(){
    if (css_file_needs_rebuilt()) {
        global $css_files;
        //eDebug($css_files);
        //exit;
        // Load the Minify library if needed.                 
        include_once(BASE.'external/minify/minify.php');                 
        // Create new Minify objects.                 
        $minifyCSS = new Minify(Minify::TYPE_CSS);                         

        // Specify the files to be minified. Full URLs are allowed as long as they point                 
        // to the same server running Minify. 
            $minifyCSS->addFile($css_files);

        // Establish the file where we will build the compiled CSS file
            $compiled_file = fopen(BASE.'tmp/css/exp-styles-min.css', 'w');
        //  eDebug($minifyCSS->combine());

        fwrite($compiled_file, $minifyCSS->combine());
        fclose($compiled_file);
    }
}

function exponent_theme_remove_css() {
    if (!defined('SYS_FILES')) include_once(BASE.'subsystems/files.php');
    return exponent_files_remove_files_in_directory(BASE.'tmp/css');
}

function exponent_theme_remove_smarty_cache() {
    if (!defined('SYS_FILES')) include_once(BASE.'subsystems/files.php');
    return exponent_files_remove_files_in_directory(BASE.'tmp/views_c');    
}

function exponent_theme_buildCSSFile($cssfile) {
    if (DEVELOPMENT > 0 || !is_readable(BASE.$cssfile)) {
        global $css_files;
        define('MINIFY_CACHE_DIR',BASE.'tmp/minify');           // Define the cache dir first.
        if (!is_dir(BASE.'tmp/minify')) mkdir(BASE.'tmp/minify');   //if the dir doesnt exist- create it  
        include_once(BASE.'external/minify/minify.php');        // Load the Minify library if needed.                 
        
        // Create new Minify objects.                 
        $minifyCSS = new Minify(Minify::TYPE_CSS);                         

        // Specify the files to be minified. Full URLs are allowed as long as they point to the same server running Minify. 
        $minifyCSS->addFile($css_files);

        // Establish the file where we will build the compiled CSS file
        $compiled_file = fopen(BASE . $cssfile, 'w');

        fwrite($compiled_file, $minifyCSS->combine());
        fclose($compiled_file);
    }
}

function exponent_theme_includeCSS($cssfile) {
    $str = "";
    
    if (DEVELOPMENT == 0 && DISPLAY_THEME==DISPLAY_THEME_REAL) {
        $str = "\t".'<link rel="stylesheet" type="text/css" href="'.URL_FULL.$cssfile.'" '.XHTML_CLOSING.'>'."\r\n";
    } else {
        global $css_files;
        foreach ($css_files as $file) {
            $str .= "\t".'<link rel="stylesheet" type="text/css" href="'.$file.'" '.XHTML_CLOSING.'>'."\r\n";
        }
    }

    return $str;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_theme_headerInfo($config) {
    echo headerInfo($config);
}

function headerInfo($config) {
    global $sectionObj, $validateTheme, $head_config;
    
    // may not need this soon here...
    $langinfo = include(BASE.'subsystems/lang/'.LANG.'.php');
    
    $validateTheme['headerinfo'] = true;
    // end checking for headerInfo

    // globalize header configuration
    $head_config = $config;

    // check to see if we're in XHTML or HTML mode
    if(empty($config['xhtml'])||($config['xhtml']==true)){ 
        define("XHTML",1);define("XHTML_CLOSING",""); //default
    } else {
        define("XHTML",0); define("XHTML_CLOSING","/");
    }

    // Load primer CSS files, or default to false if not set.
    if(!empty($config['css_primer'])){
        expCSS::pushToHead($config);
    } else {
        $config['css_primer'] = false;
    };

    if(isset($config['css_core'])) {
        if (is_array($config['css_core'])) {
            $corecss = implode(",",$config['css_core']);
            expCSS::pushToHead(array(
    		    "corecss"=>$corecss
    	    ));
        }
    } else {
        $config['css_core'] = false;
    };

    // Default the running of view based CSS inclusion to true
    if(empty($config['css_links'])){
        $config['css_links'] = true;
    }
    
    // default theme css collecting to true if not set
    if(empty($config['css_theme'])){
        $config['css_theme'] = true;
    }
        
    //eDebug($config);

    if (empty($sectionObj)) return false;
    
    $metainfo = expTheme::pageMetaInfo();
    
    $str = '';
    $str = '<title>'.$metainfo['title']."</title>\r\n";
    $str .= "\t".'<meta http-equiv="Content-Type" content="text/html; charset='.$langinfo['charset'].'" '.XHTML_CLOSING.'>'."\n";
    $str .= "\t".'<meta name="Generator" content="Exponent Content Management System - '.EXPONENT_VERSION_MAJOR.'.'.EXPONENT_VERSION_MINOR.'.'.EXPONENT_VERSION_REVISION.'.'.EXPONENT_VERSION_TYPE.'" '.XHTML_CLOSING.'>' . "\n";
    $str .= "\t".'<meta name="Keywords" content="'.$metainfo['keywords'] . '" />'."\n";
    $str .= "\t".'<meta name="Description" content="'.$metainfo['description']. '" '.XHTML_CLOSING.'>'."\n";
		
    //the last little bit of IE 6 support
    $str .= "\t".'<!--[if IE 6]><style type="text/css">  body { behavior: url('.PATH_RELATIVE.'external/csshover.htc); }</style><![endif]-->'."\n";

    // when minification is used, the comment below gets replaced when the buffer is dumped
    $str .= '<!-- MMINIFY REPLACE -->';
        
    if(file_exists(BASE.'themes/'.DISPLAY_THEME_REAL.'/favicon.ico')) {
        $str .= "\t".'<link rel="shortcut icon" href="'.URL_FULL.'themes/'.DISPLAY_THEME_REAL.'/favicon.ico" type="image/x-icon" />'."\r\n";
    }

    return $str;
}

function exponent_theme_footerInfo($params = array()) {
    footerInfo($params);
}

function footerInfo($params) {
    // checks to see if the theme is calling footerInfo.
    global $validateTheme, $user;
    
    $validateTheme['footerinfo'] = true;

    if (!empty($user->getsToolbar) && PRINTER_FRIENDLY != 1 && !defined('SOURCE_SELECTOR') && empty($params['hide-slingbar'])) {
        expTheme::showController(array("controller"=>"administration","action"=>"toolbar","source"=>"admin"));
    }
    
    //echo expJavascript::parseJSFiles();
    echo expTheme::processCSSandJS();
    echo expJavascript::footJavascriptOutput();
    
    
    expSession::deleteVar("last_POST");  //ADK - putting this here so one form doesn't unset it before another form needs it.
    expSession::deleteVar('last_post_errors');
}



/* exdoc
 * Prints the HTML for the Source Selector header table.  This is required
 * of all themes, so that the source selector allows users to browse to Archived
 * content.
 * @node Subsystems:Theme
 */
function exponent_theme_sourceSelectorInfo() {
    if (defined("SOURCE_SELECTOR") || defined("CONTENT_SELECTOR")) {
        $i18n = exponent_lang_loadFile('subsystems/theme.php');
        ?>
        <script type="text/javascript">
        window.resizeTo(800,600);
        </script>
        <table cellspacing="0" cellpadding="5" width="100%" border="0">
            <tr>
                <td width="70%">
                    <b><?php echo $i18n['selector']; ?></b>
                </td>
                <td width="30%" align="right">
                    [ <a class="mngmntlink" href="orphan_source_selector.php"><?php echo $i18n['archived_content']; ?></a> ]
                </td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="5" width="100%" border="0">
            <tr>
                <td colspan="2" style="background-color: #999; color: #fff; border-bottom: 1px solid #000; padding-bottom: .5em;">
                    <i><?php echo $i18n['selector_desc']; ?></i>
                </td>
            </tr>
        </table>
        <?php
    }
}

/* exdoc
 * Calls the necessary methods to show a specific module, in a section-sensitive way.
 *
 * @param string $module The classname of the module to display
 * @param string $view The name of the view to display the module with
 * @param string $title The title of the module (support is view-dependent)
 * @param string $prefix The prefix of the module's source.  The current section id will be appended to this
 * @param bool $pickable Whether or not the module is pickable in the Source Picer.
 * @node Subsystems:Theme
 */
function exponent_theme_showSectionalModule($module,$view,$title,$prefix = null, $pickable = false, $hide_menu=false) {
    global $db;

    if ($prefix == null) $prefix = "@section";

    $src = $prefix;

    if (exponent_sessions_isset("themeopt_override")) {
        $config = exponent_sessions_get("themeopt_override");
        if (in_array($module,$config['ignore_mods'])) return;
        $src = $config['src_prefix'].$prefix;
        $section = null;
    } else {
        global $sectionObj;
        //$last_section = exponent_sessions_get("last_section");
        //$section = $db->selectObject("section","id=".$last_section);
        $src .= $sectionObj->id;
    }


    exponent_theme_showModule($module,$view,$title,$src,$pickable,$sectionObj->id, $hide_menu);
}

/* exdoc
 * Calls the necessary methods to show a specific module in such a way that the current
 * section displays the same content as its top-level parent and all of the top-level parent's
 * children, grand-children, grand-grand-children, etc.
 *
 * @param string $module The classname of the module to display
 * @param string $view The name of the view to display the module with
 * @param string $title The title of the module (support is view-dependent)
 * @param string $prefix The prefix of the module's source.  The current section id will be appended to this
 * @param bool $pickable Whether or not the module is pickable in the Source Picer.
 * @node Subsystems:Theme
 */
function exponent_theme_showTopSectionalModule($module,$view,$title,$prefix = null, $pickable = false, $hide_menu=false) {
    global $db;

    if ($prefix == null) $prefix = "@section";
    $last_section = exponent_sessions_get("last_section");

    $section = $db->selectObject("section","id=".$last_section);
    // Loop until we find the top level parent.
    while ($section->parent != 0) $section = $db->selectObject("section","id=".$section->parent);

    exponent_theme_showModule($module,$view,$title,$prefix.$section->id,$pickable,$section, $hide_menu);
}

/* exdoc
 * Calls the necessary methods to show a specific module
 *
 * @param string $module The classname of the module to display
 * @param string $view The name of the view to display the module with
 * @param string $title The title of the module (support is view-dependent)
 * @param string $source The source of the module.
 * @param bool $pickable Whether or not the module is pickable in the Source Picer.
 * @node Subsystems:Theme
 */
function exponent_theme_showModule($module,$view="Default",$title="",$source=null,$pickable=false,$section=null,$hide_menu=false,$params=array()) {
    if (!AUTHORIZED_SECTION && $module != 'navigationmodule' && $module != 'loginmodule') return;

    global $db, $sectionObj;
    // Ensure that we have a section
    //FJD - changed to $sectionObj
    if ($sectionObj == null) {
        $section_id = exponent_sessions_get('last_section');
        if ($section_id == null) {
            $section_id = SITE_DEFAULT_SECTION;
        }
        $sectionObj = $db->selectObject('section','id='.$section_id);
        //$section->id = $section_id;
    }
    if ($module == "loginmodule" && defined("PREVIEW_READONLY") && PREVIEW_READONLY == 1) return;

    if (exponent_sessions_isset("themeopt_override")) {
        $config = exponent_sessions_get("themeopt_override");
        if (in_array($module,$config['ignore_mods'])) return;
    }
    $loc = exponent_core_makeLocation($module,$source."");

    if ($db->selectObject("locationref","module='$module' AND source='".$loc->src."'") == null) {
        $locref = null;
        $locref->module = $module;
        $locref->source = $loc->src;
        $locref->internal = "";
        $locref->refcount = 1000;
        $db->insertObject($locref,"locationref");
        if ($sectionObj != null) {
            $locref->section = $sectionObj->id;
            $locref->is_original = 1;
            $db->insertObject($locref,'sectionref');
        }
    }

    $iscontroller = controllerExists($module);

    if (defined("SELECTOR") && call_user_func(array($module,"hasSources"))) {
        containermodule::wrapOutput($module,$view,$loc,$title);
    } else {
        if (is_callable(array($module,"show")) || $iscontroller) {
            // FIXME: we are checking here for a new MVC style controller or an old school module. We only need to perform 
            // this check until we get the old modules all gone...until then we have the check and a lot of code duplication
            // in the if blocks below...oh well, that's life.
            if (!$iscontroller) {
                if ((!$hide_menu && $loc->mod != "containermodule" && (call_user_func(array($module,"hasSources")) || $db->tableExists($loc->mod."_config")))) {
                    $container->permissions = array(
                        'administrate'=>(exponent_permissions_check('administrate',$loc) ? 1 : 0),
                        'configure'=>(exponent_permissions_check('configure',$loc) ? 1 : 0)
                    );

                    if ($container->permissions['administrate'] || $container->permissions['configure']) {
                        $container->randomizer = mt_rand(1,ceil(microtime(1)));
                        $container->view = $view;
                        $container->info['class'] = $loc->mod;
                        $container->info['module'] = call_user_func(array($module,"name"));
                        $container->info['source'] = $loc->src;
                        $container->info['hasConfig'] = $db->tableExists($loc->mod."_config");
                        $template = new template('containermodule','_hardcoded_module_menu',$loc);
                        $template->assign('container', $container);
                        $template->output();
                    }
                }
            } else {
                // if we hit here we're dealing with a controller...not a module
                if (!$hide_menu ) {
                    $controller = getController($module);
                    $container->permissions = array(
                        'administrate'=>(exponent_permissions_check('administrate',$loc) ? 1 : 0),
                        'configure'=>(exponent_permissions_check('configure',$loc) ? 1 : 0)
                    );

                    if ($container->permissions['administrate'] || $container->permissions['configure']) {
                        $container->randomizer = mt_rand(1,ceil(microtime(1)));
                        $container->view = $view;
                        $container->action = $params['action'];
                        $container->info['class'] = $loc->mod;
                        $container->info['module'] = $controller->displayname();
                        $container->info['source'] = $loc->src;
                        $container->info['hasConfig'] = true;
                        $template = new template('containermodule','_hardcoded_module_menu',$loc);
                        $template->assign('container', $container);
                        $template->output();
                    }
                }
            }

            if ($iscontroller) {
                $params['src'] = $loc->src;
                $params['controller'] = $module;                
                $params['view'] = $view;
                $params['moduletitle'] = $title;
                if (empty($params['action'])) $params['action'] = $view;
                renderAction($params);
            } else {
                call_user_func(array($module,"show"),$view,$loc,$title);
            }
        } else {
            $i18n = exponent_lang_loadFile('subsystems/theme.php');
            echo sprintf($i18n['mod_not_found'],$module);
        }
    }
}

/* exdoc
 * Checks to see if the page is currently in an action.  Useful only if the theme does not use the exponent_theme_main() function
 * Returns whether or not an action should be run.
 * @node Subsystems:Theme
 */
function exponent_theme_inAction() {
    return (isset($_REQUEST['action']) && (isset($_REQUEST['module']) || isset($_REQUEST['controller'])));
    //return (isset($_REQUEST['action']) && isset($_REQUEST['module']) );
}
/* exdoc
 * Checks to see if the page is currently in an action.  Useful only if the theme does not use the exponent_theme_main() function
 * Returns whether or not an action should be run.
 * @node Subsystems:Theme
 */
function exponent_theme_reRoutActionTo($theme = "") {
    if (empty($theme)) {
        return false;
    }
    if (exponent_theme_inAction()) {
        include_once(BASE."themes/".DISPLAY_THEME_REAL."/".$theme);
        exit;
    }
}

/* exdoc
 * Checks to see if the user is authorized to view the current section.
 * Retursn whether or not the user is authorized.
 * @node Subsystems:Theme
 */
function exponent_theme_canViewPage() {
    return AUTHORIZED_SECTION;
    /*
    global $db;
    $last_section = exponent_sessions_get("last_section");
    $section = $db->selectObject("section","id=".$last_section);
    if ($section && navigationModule::canView($section)) {
        $sloc = exponent_core_makeLocation("navigationmodule","",$section->id);
        return exponent_permissions_check("view",$sloc);
    } else return true;
    */
}

/* exdoc
 * Looks at the attributes of the current section and properly calls exponent_flow_set
 * @node Subsystems:Theme
 */
function exponent_theme_setFlow() {
    if ((!defined("SOURCE_SELECTOR") || SOURCE_SELECTOR == 1) && (!defined("CONTENT_SELECTOR") || CONTENT_SELECTOR == 1)) {
        global $db;
        $last_section = exponent_sessions_get("last_section");
        $section = $db->selectObject("section","id=".$last_section);

        if ($section && $section->public == 0) {
            exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_SECTIONAL);
        } else if ($section && $section->public == 1) {
            exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_SECTIONAL);
        }
    }
}

/* exdoc
 * Takes care of all the specifics of either showing a sectional container or running an action.
 * @node Subsystems:Theme
 */
function exponent_theme_main() {
    global $db, $user;

    echo show_msg_queue();
    if ((!defined("SOURCE_SELECTOR") || SOURCE_SELECTOR == 1) && (!defined("CONTENT_SELECTOR") || CONTENT_SELECTOR == 1)) {
        $last_section = exponent_sessions_get("last_section");
        $section = $db->selectObject("section","id=".$last_section);
        // View authorization will be taken care of by the runAction and mainContainer functions
        if (exponent_theme_inAction()) {
            exponent_theme_runAction();
        } else if ($section == null) {
            exponent_theme_goDefaultSection();
        } else {
            exponent_theme_mainContainer();
        }
    } else {
        if (isset($_REQUEST['module'])) {
            include_once(BASE."modules/containermodule/actions/orphans_content.php");
        } else {
            $i18n = exponent_lang_loadFile('subsystems/theme.php');
            echo $i18n['select_module'];
        }
    }
}

/* exdoc
 * Runs the approriate action, by looking at the $_REQUEST variable.
 * @node Subsystems:Theme
 */
function exponent_theme_runAction() {
    if (exponent_theme_inAction()) {
        if (!AUTHORIZED_SECTION) {
            echo SITE_403_HTML;
        }
        if (exponent_sessions_isset("themeopt_override")) {
            $config = exponent_sessions_get("themeopt_override");
            echo "<a class='mngmntlink sitetemplate_mngmntlink' href='".$config['mainpage']."'>".$config['backlinktext']."</a><br /><br />";
        }

        //FIXME: module/controller glue code..remove ASAP
        $module = empty($_REQUEST['controller']) ? $_REQUEST['module'] : $_REQUEST['controller'];
        $isController = controllerExists($module);

        if ($isController && !isset($_REQUEST['_common'])) {
            // this is being set just incase the url said module=modname instead of controller=modname
            // with SEF URls turned on its not really an issue, but with them off some of the links
            // aren't being made correctly...depending on how the {link} plugin was used in the view.
            $_REQUEST['controller'] = $module;  
            
            echo renderAction($_REQUEST); 
        } else {
            if ($_REQUEST['action'] == 'index') {
                $view = empty($_REQUEST['view']) ? 'Default' : $_REQUEST['view'];           
                $title = empty($_REQUEST['title']) ? '' : $_REQUEST['title'];   
                $src = empty($_REQUEST['src']) ? null : $_REQUEST['src'];       
                exponent_theme_showModule($module, $view, $title, $src);
                return true;
            }

            global $db, $user;

            // the only reason we should have a controller down in this section is if we are hitting a common action like 
            // userperms or groupperms...deal wit it.
            $loc = null;
            $loc->mod = $module;
            $loc->src = (isset($_REQUEST['src']) ? $_REQUEST['src'] : "");
            $loc->int = (isset($_REQUEST['int']) ? $_REQUEST['int'] : "");
            //if (isset($_REQUEST['act'])) $loc->act = $_REQUEST['act'];

            if (isset($_REQUEST['_common'])) {
                $actfile = "/common/actions/" . $_REQUEST['action'] . ".php";
            } else {
                $actfile = "/" . $module . "/actions/" . $_REQUEST['action'] . ".php";
            }

            if (is_readable(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile)) {
                include_once(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile);
            } elseif (is_readable(BASE.'modules/'.$actfile)) {
                include_once(BASE.'modules/'.$actfile);
            } else {
                $i18n = exponent_lang_loadFile('subsystems/theme.php');
                echo SITE_404_HTML . '<br /><br /><hr size="1" />';
                echo sprintf($i18n['no_action'],strip_tags($module),strip_tags($_REQUEST['action']));
                echo '<br />';
            }
        }
    }
}

function exponent_theme_showAction($module, $action, $src="", $params="") {
    global $db, $user;

    $loc = null;
    $loc->mod = $module;
    $loc->src = (isset($src) ? $src : "");
    $loc->int = (isset($int) ? $int : "");

    $actfile = "/" . $module . "/actions/" . $action . ".php";
    //if (isset($['_common'])) $actfile = "/common/actions/" . $_REQUEST['action'] . ".php";

    if (is_readable(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile)) {
        include(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile);
    } elseif (is_readable(BASE.'modules/'.$actfile)) {
            include(BASE.'modules/'.$actfile);
    } else {
            $i18n = exponent_lang_loadFile('subsystems/theme.php');
            echo SITE_404_HTML . '<br /><br /><hr size="1" />';
            echo sprintf($i18n['no_action'],strip_tags($_REQUEST['module']),strip_tags($_REQUEST['action']));
            echo '<br />';
    }
}

/* exdoc
 * Redirect User to Default Section
 * @node Subsystems:Theme
 */
function exponent_theme_goDefaultSection() {
    $last_section = exponent_sessions_get("last_section");
    if (defined("SITE_DEFAULT_SECTION") && SITE_DEFAULT_SECTION != $last_section) {
        header("Location: ".URL_FULL."index.php?section=".SITE_DEFAULT_SECTION);
        exit();
    } else {
        global $db;
        $section = $db->selectObject("section","public = 1 AND active = 1"); // grab first section, go there
        if ($section) {
            header("Location: ".URL_FULL."index.php?section=".$section->id);
            exit();
        } else {
            echo SITE_404_HTML;
        }
    }
}

/* exdoc
 * Useful only if theme does not use exponent_theme_main
 *
 * @param bool $public Whether or not the page is public.
 * @node Subsystems:Theme
 */
function exponent_theme_mainContainer() {
    if (!AUTHORIZED_SECTION) {
        // Set this so that a login on an Auth Denied page takes them back to the previously Auth-Denied page
        exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_SECTIONAL);
        echo SITE_403_HTML;
        return;
    }

    if (PUBLIC_SECTION) exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_SECTIONAL);
    else exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_SECTIONAL);

#   if (exponent_sessions_isset("themeopt_override")) {
#       $config = exponent_sessions_get("themeopt_override");
        exponent_theme_showSectionalModule("containermodule","Default","","@section",false,true);
#   } else {
#       exponent_theme_showSectionalModule("containermodule","Default","","@section");
#   }
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_theme_getSubthemes($include_default = true,$theme = DISPLAY_THEME) {
    $base = BASE."themes/$theme/subthemes";
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
            if (substr($s,-4,4) == '.php' && substr($s,0,1) != '_' && is_file($base."/$s") && is_readable($base."/$s")) {
                // Only readable .php files are allowed to be subtheme files.
                $subs[substr($s,0,-4)] = substr($s,0,-4);
            }
        }
        // Sort the subthemes by their keys (which are the same as the values)
        // using a natural string comparison funciton (PHP built-in)
        uksort($subs,'strnatcmp');
    }
    return $subs;
}

function exponent_theme_getPrinterFriendlyTheme() {
    $common = 'themes/common/printer-friendly/index.php';
    $theme = 'themes/'.DISPLAY_THEME.'/printer-friendly/index.php';

    if (is_readable($theme)) {
        return $theme;
        } elseif (is_readable($common)) {
        return $common;
    } else {
        return null;
    }

}

function exponent_theme_getTheme() {
    global $sectionObj, $router;
    // Grabs the action maps files for theme overrides
    $action_maps = exponent_theme_loadActionMaps();
    
    // if we are in an action, get the particulars for the module
    if (exponent_theme_inAction()) $module = isset($_REQUEST['module']) ? $_REQUEST['module'] : $_REQUEST['controller'];

    // if we are in an action and have action maps to work with...
    if (exponent_theme_inAction() && (!empty($action_maps[$module]) && (array_key_exists($_REQUEST['action'], $action_maps[$module]) || array_key_exists('*', $action_maps[$module])))) {
        $actionname = array_key_exists($_REQUEST['action'], $action_maps[$module]) ? $_REQUEST['action'] : '*';
        $actiontheme = explode(":",$action_maps[$module][$actionname]);
        
        // this resets the section object. we're supressing notices with @ because getSectionObj sets constants, which cannot be changed
        // since this will be the second sime Exponent calls this function on the page load.
        if (!empty($actiontheme[1])) $sectionObj = @$router->getSectionObj($actiontheme[1]); 
        
        if ($actiontheme[0]=="default" || $actiontheme[0]=="Default" || $actiontheme[0]=="index"){
            $theme = BASE.'themes/'.DISPLAY_THEME.'/index.php';
        } else {
            $theme = BASE.'themes/'.DISPLAY_THEME.'/subthemes/'.$actiontheme[0].'.php';
        }
        return $theme;
    } elseif ($sectionObj->subtheme != '' && is_readable(BASE.'themes/'.DISPLAY_THEME.'/subthemes/'.$sectionObj->subtheme.'.php')) {
            return BASE.'themes/'.DISPLAY_THEME.'/subthemes/'.$sectionObj->subtheme.'.php';
    } else {
            return BASE.'themes/'.DISPLAY_THEME.'/index.php';
    }
}

function exponent_theme_loadActionMaps() {
    if (is_readable(BASE.'themes/'.DISPLAY_THEME.'/action_maps.php')) {
        return include(BASE.'themes/'.DISPLAY_THEME.'/action_maps.php');
    } else {
        return array();
    }
}


function exponent_theme_satisfyThemeRequirements() {
    global $validateTheme;
    if ($validateTheme['headerinfo']==false) {
        echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>exponent_theme_headerInfo() is a required function in your theme.  Please refer to the Exponent documentation for details:<br />
        <a href=\"http://docs.exponentcms.org/New_Themes_Guide\" target=\"_blank\">http://docs.exponentcms.org/</a>
        </h1>";
        die();
    }
    
    if ($validateTheme['footerinfo']==false) {
        echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>exponent_theme_footerInfo() is a required function in your theme.  Please refer to the Exponent documentation for details:<br />
        <a href=\"http://docs.exponentcms.org/New_Themes_Guide\" target=\"_blank\">http://docs.exponentcms.org/</a>
        </h1>";
        die();
    }
}


?>
