<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expTheme class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/** @define "BASE" "../../.." */

/**
 * This is the class expTheme
 *
 * @subpackage Core-Subsystems
 * @package Framework
 */

class expTheme {

	public static function initialize() {
		global $auto_dirs2, $user;
		// Initialize the theme subsystem 1.0 compatibility layer
		require_once(BASE.'framework/core/compat/theme.php');
		if (!defined('DISPLAY_THEME')) {
			/* exdoc
			 * The directory and class name of the current active theme.  This may be different
			 * than the configured theme (DISPLAY_THEME_REAL) due to previewing.
			 */
			define('DISPLAY_THEME',DISPLAY_THEME_REAL);
		}

		if (!defined('THEME_ABSOLUTE')) {
			/* exdoc
			 * The absolute path to the current active theme's files.  This is similar to the BASE constant
			 */
			define('THEME_ABSOLUTE',BASE.'themes/'.DISPLAY_THEME.'/'); // This is the recommended way
		}

		if (!defined('THEME_RELATIVE')) {
			/* exdoc
			 * The relative web path to the current active theme.  This is similar to the PATH_RELATIVE constant.
			 */
			define('THEME_RELATIVE',PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/');
		}
		if (!defined('THEME_STYLE')) {
			/* exdoc
			 * The name of the current active theme style.
			 */
			define('THEME_STYLE',THEME_STYLE_REAL);
		}
		if (THEME_STYLE != '') {
			if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/config_'.THEME_STYLE.'.php')){
			  @include_once(BASE.'themes/'.DISPLAY_THEME.'/config_'.THEME_STYLE.'.php');
			}
		} else {
			if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/config.php')) {
			  @include_once(BASE.'themes/'.DISPLAY_THEME.'/config.php');
			}
		}
		if (!defined('BTN_SIZE')) define('BTN_SIZE','medium');
		if (!defined('BTN_COLOR')) define('BTN_COLOR','black');
		// add our theme folder to autoload and place it first
		//$auto_dirs2[] = BASE.'themes/'.DISPLAY_THEME_REAL.'/modules';
		$auto_dirs2[] = BASE.'themes/'.DISPLAY_THEME.'/modules';
		$auto_dirs2 = array_reverse($auto_dirs2);
	}

    public static function head($config = array()){
    	echo self::headerInfo($config);
		echo self::advertiseRSS();
    }

	public static function foot($params = array()) {
		echo self::footerInfo($params);
	}

	/** exdoc
	* Takes care of all the specifics of either showing a sectional container or running an action.
	* @node Subsystems:Theme
	*/
    public static function main() {
		global $db, $user;

		echo show_msg_queue();
		if ((!defined('SOURCE_SELECTOR') || SOURCE_SELECTOR == 1)) {
			$last_section = expSession::get("last_section");
			$section = $db->selectObject("section","id=".$last_section);
			// View authorization will be taken care of by the runAction and mainContainer functions
			if (self::inAction()) {
				self::runAction();
			} else if ($section == null) {
				self::goDefaultSection();
			} else {
				self::mainContainer();
			}
		} else {
			if (isset($_REQUEST['module'])) {
				include_once(BASE."framework/modules-1/containermodule/actions/orphans_content.php");
			} else {
				echo gt('Select a module');
			}
		}
    }
    
    public static function module($params) {
	    if (empty($params)) {
		    return false;
	    } elseif (isset($params['controller'])) {
            self::showController($params);
        } elseif (isset($params['module'])) {
            $moduletitle = (isset($params['moduletitle'])) ? $params['moduletitle'] : "";
            $source = (isset($params['source'])) ? $params['source'] : "";
            $chrome = (isset($params['chrome'])) ? $params['chrome'] : false;
            $scope = (isset($params['scope'])) ? $params['scope'] : "global";
            
            if ($scope=="global") {
                self::showModule($params['module']."module",$params['view'],$moduletitle,$source,false,null,$chrome);
            }
            if ($scope=="top-sectional") {
                self::showTopSectionalModule($params['module']."module", //module
                                                    $params['view'], //view
                                                    $moduletitle, // Title
                                                    $source, // source
                                                    false, // prefix??  no idea...
                                                    null, // used to apply to source picker. does nothing now.
                                                    $chrome // Show chrome
                                                    );
            }
            if ($scope=="sectional") {
                self::showSectionalModule($params['module']."module", //module
                                                    $params['view'], //view
                                                    $moduletitle, // title
                                                    $source, // source
                                                    false, // prefix??  no idea...
                                                    null, // used to apply to source picker. does nothing now.
                                                    $chrome // Show chrome
                                                    );
            }
        } else {
		    return false;
	    }
    }
    
    public static function showController($params=array()) {
        global $sectionObj, $db;
        if (empty($params)) {
	        return false;
        } elseif (isset($params['module'])) {
            self::module($params);
        } else if (isset($params['controller'])) {
			$params['view'] = isset($params['view']) ? $params['view'] : $params['action'];
			$params['title'] = isset($params['moduletitle']) ? $params['moduletitle'] : '';
			$params['chrome'] = (!isset($params['chrome']) || (isset($params['chrome'])&&empty($params['chrome']))) ? true : false;
			$params['scope'] = isset($params['scope']) ? $params['scope'] : 'global';

			// set the controller and action to the one called via the function params
			$requestvars = isset($params['params']) ? $params['params'] : array();
			$requestvars['controller'] = $params['controller'];
			$requestvars['action'] = isset($params['action']) ? $params['action'] : null;
			$requestvars['view'] = isset($params['view']) ? $params['view'] : null;

			// figure out the scope of the module and set the source accordingly
			if ($params['scope'] == 'global') {
				$params['source'] = isset($params['source']) ? $params['source'] : null;
			} elseif ($params['scope'] == 'sectional') {
				$params['source']  = isset($params['source']) ? $params['source'] : '@section';
				$params['source'] .= $sectionObj->id;
			} elseif ($params['scope'] == 'top-sectional') {
				$params['source']  = isset($params['source']) ? $params['source'] : '@section';
				$section = $sectionObj;
				while ($section->parent > 0) $section = $db->selectObject("section","id=".$section->parent);
				$params['source'] .= $section->id;
			}
			self::showModule(expModules::getControllerClassName($params['controller']),$params['view'],$params['title'],$params['source'],false,null,$params['chrome'],$requestvars);
        } else {
	        return false;
        }
    }

    public function showSectionalController($params=array()) {
        global $sectionObj;
        $src = "@section" . $sectionObj->id;
        $params['source'] = $src;
        self::showController($params);
    }
    
    public static function pageMetaInfo() {
        global $sectionObj, $db, $router;
        
        $metainfo = array();
        if (self::inAction() && (!empty($router->url_parts[0]) && expModules::controllerExists($router->url_parts[0]))) {
            $classname = expModules::getControllerClassName($router->url_parts[0]);
            $controller = new $classname();
            $metainfo = $controller->metainfo();
        } else {
            $metainfo['title'] = ($sectionObj->page_title == "") ? SITE_TITLE : $sectionObj->page_title;	
	        $metainfo['keywords'] = ($sectionObj->keywords == "") ? SITE_KEYWORDS : $sectionObj->keywords;
	        $metainfo['description'] = ($sectionObj->description == "") ? SITE_DESCRIPTION : $sectionObj->description;	
        }
        
        return $metainfo;
    }

	/** exdoc
	 * Checks to see if the page is currently in an action.  Useful only if the theme does not use the self::main() function
	 * Returns whether or not an action should be run.
	 * @node Subsystems:Theme
	 * @return bool
	 */
    public static function inAction() {
        return (isset($_REQUEST['action']) && (isset($_REQUEST['module']) || isset($_REQUEST['controller'])));
    }
    
    public static function reRoutActionTo($theme = "") {
        if (empty($theme)) {
            return false;
        }
        if (self::inAction()) {
//            include_once(BASE."themes/".DISPLAY_THEME_REAL."/".$theme);
            include_once(BASE."themes/".DISPLAY_THEME."/".$theme);
            exit;
        }
    }

    public function grabView($path,$filename) {        
        $dirs = array(
//            BASE.'themes/'.DISPLAY_THEME_REAL.'/'.$path,
            BASE.'themes/'.DISPLAY_THEME.'/'.$path,
            BASE.'framework/'.$path,
        );
        
        foreach ($dirs as $dir) {
            if (file_exists($dir.$filename.'.tpl')) return $dir.$form.'.tpl';  //FIXME $form is not set??
        }
        
        return false;
    }
    
    public function grabViews($path,$filter='') {        
        $dirs = array(
//            BASE.'themes/'.DISPLAY_THEME_REAL.'/'.$path,
            BASE.'themes/'.DISPLAY_THEME.'/'.$path,
            BASE.'framework/'.$path,
        );
                
        foreach ($dirs as $dir) {
            if (is_dir($dir) && is_readable($dir) ) {
                $dh = opendir($dir);
                while (($filename = readdir($dh)) !== false) {
                    $file = $dir.$filename;
                    if (is_file($filename)) {
                        $files[$filename] = $file;
                    }
                }
            }
        }
        
        return $files;
    }
    
    public static function processCSSandJS() {
        global $jsForHead, $cssForHead;
        // resturns string, either minified combo url or multiple link and script tags 
        $jsForHead = expJavascript::parseJSFiles();
        $cssForHead = expCSS::parseCSSFiles();
    }

	public static function removeCss() {
		expFile::removeFilesInDirectory(BASE.'tmp/minify');  // also clear the minify engine's cache
		return expFile::removeFilesInDirectory(BASE.'tmp/css');
	}

	public static function clearSmartyCache() {
		self::removeSmartyCache();
		flash('message',gt("Smarty Cache has been cleared"));
		expHistory::back();
	}

	public static function removeSmartyCache() {
		expFile::removeFilesInDirectory(BASE.'tmp/cache');  // alt location for cache
		return expFile::removeFilesInDirectory(BASE.'tmp/views_c');
	}

	/* exdoc
	 * Output <link /> elements for each RSS feed on the site
	 *
	 * @node Subsystems:Theme
	 */
	public static function advertiseRSS() {
		if (defined('ADVERTISE_RSS') && ADVERTISE_RSS == 1) {
			echo "\n\t<!-- RSS Feeds -->\n";
			$rss = new expRss();
			$feeds = $rss->getFeeds();
			foreach ($feeds as $feed) {
				if ($feed->enable_rss) {
					$title = empty($feed->feed_title) ? 'RSS' : htmlspecialchars($feed->feed_title, ENT_QUOTES);
					$params['module'] = $feed->module;
					$params['src'] = $feed->src;
					echo "\t".'<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . expCore::makeRSSLink($params) . "\" />\n";
				}
			}

			// now for the old school module rss feeds
			global $db;

			$modules = $db->selectObjects("sectionref", "refcount > 0");  // get all the modules being using
			$feeds = array();
			foreach ($modules as $module) {
				if (isset($feeds[$module->source])) continue;
				$location->mod = $module->module;
				$location->src = $module->source;
				$location->int = $module->internal;

				if (!expModules::controllerExists($module->module)) {
					//get the module's config data
					$config = $db->selectObject($module->module."_config", "location_data='".serialize($location)."'");
					if (!empty($config->enable_rss)) {
						$title = empty($config->feed_title) ? 'RSS' : htmlspecialchars($config->feed_title, ENT_QUOTES);
						$params['module'] = $module->module;
						$params['src'] = $module->source;
						if (!empty($module->internal)) $params['int'] = $module->internal;

						echo "\t".'<link rel="alternate" type="application/rss+xml" title="' . $title . '" href="' . expCore::makeRSSLink($params) . "\" />\n";
						$feeds[$module->source] = $title;
					}
				}
			}
		}
	}

	///* exdoc
	// * @state <b>UNDOCUMENTED</b>
	// * @node Undocumented
	// */
	//function self::headerInfo($config) {
	//    echo headerInfo($config);
	//	echo self::advertiseRSS();
	//}

	public static function headerInfo($config) {
		global $sectionObj, $validateTheme, $head_config;

		$validateTheme['headerinfo'] = true;
		// end checking for headerInfo

		// globalize header configuration
		$head_config = $config;

		// check to see if we're in XHTML or HTML mode
		if(isset($config['xhtml']) && $config['xhtml']==true){
			define('XHTML',1);define('XHTML_CLOSING',"/"); //default
		} else {
			define('XHTML',0); define('XHTML_CLOSING',"");
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

		$metainfo = self::pageMetaInfo();

		$str = '';
		$str = '<title>'.$metainfo['title']."</title>\r\n";
		$str .= "\t".'<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'" '.XHTML_CLOSING.'>'."\n";
		$str .= "\t".'<meta name="Generator" content="Exponent Content Management System - '.expVersion::getVersion(true).'" '.XHTML_CLOSING.'>' . "\n";
		$str .= "\t".'<meta name="Keywords" content="'.$metainfo['keywords'] . '" '.XHTML_CLOSING.'>'."\n";
		$str .= "\t".'<meta name="Description" content="'.$metainfo['description']. '" '.XHTML_CLOSING.'>'."\n";

		//the last little bit of IE 6 support
		$str .= "\t".'<!--[if IE 6]><style type="text/css">  body { behavior: url('.PATH_RELATIVE.'external/csshover.htc); }</style><![endif]-->'."\n";

		// when minification is used, the comment below gets replaced when the buffer is dumped
		$str .= '<!-- MINIFY REPLACE -->';

//		if(file_exists(BASE.'themes/'.DISPLAY_THEME_REAL.'/favicon.ico')) {
//			$str .= "\t".'<link rel="shortcut icon" href="'.URL_FULL.'themes/'.DISPLAY_THEME_REAL.'/favicon.ico" type="image/x-icon" />'."\r\n";
		if(file_exists(BASE.'themes/'.DISPLAY_THEME.'/favicon.ico')) {
			$str .= "\t".'<link rel="shortcut icon" href="'.URL_FULL.'themes/'.DISPLAY_THEME.'/favicon.ico" type="image/x-icon" '.XHTML_CLOSING.'>'."\r\n";
		}

		return $str;
	}

	public static function footerInfo($params = array()) {
		// checks to see if the theme is calling footerInfo.
		global $validateTheme, $user;

		$validateTheme['footerinfo'] = true;

		if (!empty($user->getsToolbar) && PRINTER_FRIENDLY != 1 && !defined('SOURCE_SELECTOR') && empty($params['hide-slingbar'])) {
			self::showController(array("controller"=>"administration","action"=>"toolbar","source"=>"admin"));
		}

		if ((self::is_mobile() || FORCE_MOBILE) && is_readable(BASE.'themes/'.DISPLAY_THEME.'/mobile/index.php')) {
			echo ('<div style="text-align:center"><a href="'.makeLink(array('module' => 'administration','action' => 'toggle_mobile')).'">View site in '.(MOBILE ? "Classic":"Mobile").' mode</a></div>');
		}
		//echo expJavascript::parseJSFiles();
		echo self::processCSSandJS();
		echo expJavascript::footJavascriptOutput();

		expSession::deleteVar("last_POST");  //ADK - putting this here so one form doesn't unset it before another form needs it.
		expSession::deleteVar('last_post_errors');
	}

	public static function satisfyThemeRequirements() {
		global $validateTheme;
		if ($validateTheme['headerinfo']==false) {
			echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>expTheme::head() is a required function in your theme.  Please refer to the Exponent documentation for details:<br />
			<a href=\"http://docs.exponentcms.org/New_Themes_Guide\" target=\"_blank\">http://docs.exponentcms.org/</a>
			</h1>";
			die();
		}

		if ($validateTheme['footerinfo']==false) {
			echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>expTheme::foot() is a required function in your theme.  Please refer to the Exponent documentation for details:<br />
			<a href=\"http://docs.exponentcms.org/New_Themes_Guide\" target=\"_blank\">http://docs.exponentcms.org/</a>
			</h1>";
			die();
		}
	}

	public static function getTheme() {
		global $sectionObj, $router;
		// Grabs the action maps files for theme overrides
		$action_maps = self::loadActionMaps();

//		$mobile = self::is_mobile();

		// if we are in an action, get the particulars for the module
		if (self::inAction()) $module = isset($_REQUEST['module']) ? $_REQUEST['module'] : $_REQUEST['controller'];

		// if we are in an action and have action maps to work with...
		if (self::inAction() && (!empty($action_maps[$module]) && (array_key_exists($_REQUEST['action'], $action_maps[$module]) || array_key_exists('*', $action_maps[$module])))) {
			$actionname = array_key_exists($_REQUEST['action'], $action_maps[$module]) ? $_REQUEST['action'] : '*';
			$actiontheme = explode(":",$action_maps[$module][$actionname]);

			// this resets the section object. we're supressing notices with @ because getSectionObj sets constants, which cannot be changed
			// since this will be the second sime Exponent calls this function on the page load.
			if (!empty($actiontheme[1])) $sectionObj = @$router->getSectionObj($actiontheme[1]);

			if ($actiontheme[0]=="default" || $actiontheme[0]=="Default" || $actiontheme[0]=="index") {
				if (MOBILE && is_readable(BASE.'themes/'.DISPLAY_THEME.'/mobile/index.php')) {
					$theme = BASE.'themes/'.DISPLAY_THEME.'/mobile/index.php';
				} else {
					$theme = BASE.'themes/'.DISPLAY_THEME.'/index.php';
				}
			} elseif (is_readable(BASE.'themes/'.DISPLAY_THEME.'/subthemes/'.$actiontheme[0].'.php')) {
				if (MOBILE && is_readable(BASE.'themes/'.DISPLAY_THEME.'/mobile/'.$actiontheme[0].'.php')) {
					$theme = BASE.'themes/'.DISPLAY_THEME.'/mobile/'.$actiontheme[0].'.php';
				} else {
					$theme = BASE.'themes/'.DISPLAY_THEME.'/subthemes/'.$actiontheme[0].'.php';
				}
			} else {
				$theme =  BASE.'themes/'.DISPLAY_THEME.'/index.php';
			}
		} elseif ($sectionObj->subtheme != '' && is_readable(BASE.'themes/'.DISPLAY_THEME.'/subthemes/'.$sectionObj->subtheme.'.php')) {
			if (MOBILE && is_readable(BASE.'themes/'.DISPLAY_THEME.'/mobile/'.$sectionObj->subtheme.'.php')) {
				$theme = BASE.'themes/'.DISPLAY_THEME.'/mobile/'.$sectionObj->subtheme.'.php';
			} elseif (MOBILE && is_readable(BASE.'themes/'.DISPLAY_THEME.'/mobile/index.php')) {
				$theme = BASE.'themes/'.DISPLAY_THEME.'/mobile/index.php';
			} else {
				$theme =  BASE.'themes/'.DISPLAY_THEME.'/subthemes/'.$sectionObj->subtheme.'.php';
			}
		} else {
			if (MOBILE && is_readable(BASE.'themes/'.DISPLAY_THEME.'/mobile/index.php')) {
				$theme = BASE.'themes/'.DISPLAY_THEME.'/mobile/index.php';
			} else {
				$theme =  BASE.'themes/'.DISPLAY_THEME.'/index.php';
			}
		}
		return $theme;
	}

	public static function getPrinterFriendlyTheme() {
		$common = 'framework/core/printer-friendly.php';
		$theme = 'themes/'.DISPLAY_THEME.'/printer-friendly.php';

		if (is_readable($theme)) {
			return $theme;
		} elseif (is_readable($common)) {
			return $common;
		} else {
			return null;
		}

	}


	/** exdoc
	 * Runs the approriate action, by looking at the $_REQUEST variable.
	 * @node Subsystems:Theme
	 * @return bool
	 */
	public static function runAction() {
		if (self::inAction()) {
			if (!AUTHORIZED_SECTION) {
				echo SITE_403_HTML;
			}
			if (expSession::is_set("themeopt_override")) {
				$config = expSession::get("themeopt_override");
				echo "<a class='mngmntlink sitetemplate_mngmntlink' href='".$config['mainpage']."'>".$config['backlinktext']."</a><br /><br />";
			}

			//FIXME: module/controller glue code..remove ASAP
			$module = empty($_REQUEST['controller']) ? $_REQUEST['module'] : $_REQUEST['controller'];
			$isController = expModules::controllerExists($module);

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
					self::showModule($module, $view, $title, $src);
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

//				if (is_readable(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile)) {
//					include_once(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile);
				if (is_readable(BASE."themes/".DISPLAY_THEME."/modules".$actfile)) {
						include_once(BASE."themes/".DISPLAY_THEME."/modules".$actfile);
				} elseif (is_readable(BASE.'framework/modules-1/'.$actfile)) {
					include_once(BASE.'framework/modules-1/'.$actfile);
				} else {
					echo SITE_404_HTML . '<br /><br /><hr size="1" />';
					echo sprintf(gt('No such module action').' : %1 : %2',strip_tags($module),strip_tags($_REQUEST['action']));
					echo '<br />';
				}
			}
		}
	}

	/** exdoc
	 * Calls the necessary methods to show a specific module
	 *
	 * @param string $module The classname of the module to display
	 * @param string $view The name of the view to display the module with
	 * @param string $title The title of the module (support is view-dependent)
	 * @param string $source The source of the module.
	 * @param bool $pickable Whether or not the module is pickable in the Source Picer.
	 * @param null $section
	 * @param bool $hide_menu
	 * @param array $params
	 * @return
	 * @node Subsystems:Theme
	 */
	public static function showModule($module,$view="Default",$title="",$source=null,$pickable=false,$section=null,$hide_menu=false,$params=array()) {
		if (!AUTHORIZED_SECTION && $module != 'navigationmodule' && $module != 'loginmodule') return;

		global $db, $sectionObj;
		// Ensure that we have a section
		//FJD - changed to $sectionObj
		if ($sectionObj == null) {
			$section_id = expSession::get('last_section');
			if ($section_id == null) {
				$section_id = SITE_DEFAULT_SECTION;
			}
			$sectionObj = $db->selectObject('section','id='.$section_id);
			//$section->id = $section_id;
		}
		if ($module == "loginmodule" && defined('PREVIEW_READONLY') && PREVIEW_READONLY == 1) return;

		if (expSession::is_set("themeopt_override")) {
			$config = expSession::get("themeopt_override");
			if (in_array($module,$config['ignore_mods'])) return;
		}
		$loc = expCore::makeLocation($module,$source."");

		if ($db->selectObject("sectionref","module='$module' AND source='".$loc->src."'") == null) {
				$secref = null;
				$secref->module = $module;
				$secref->source = $loc->src;
				$secref->internal = "";
				$secref->refcount = 1000;
				if ($sectionObj != null) {
					$secref->section = $sectionObj->id;
				}
				$secref->is_original = 1;
				$db->insertObject($secref,'sectionref');
		}
		$iscontroller = expModules::controllerExists($module);

		if (defined('SELECTOR') && call_user_func(array($module,"hasSources"))) {
			containermodule::wrapOutput($module,$view,$loc,$title);
		} else {
			if (is_callable(array($module,"show")) || $iscontroller) {
				// FIXME: we are checking here for a new MVC style controller or an old school module. We only need to perform
				// this check until we get the old modules all gone...until then we have the check and a lot of code duplication
				// in the if blocks below...oh well, that's life.
				if (!$iscontroller) {
					if ((!$hide_menu && $loc->mod != "containermodule" && (call_user_func(array($module,"hasSources")) || $db->tableExists($loc->mod."_config")))) {
						$container->permissions = array(
							'administrate'=>(expPermissions::check('administrate',$loc) ? 1 : 0),
							'configure'=>(expPermissions::check('configure',$loc) ? 1 : 0)
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
						$controller = expModules::getController($module);
						$container->permissions = array(
							'administrate'=>(expPermissions::check('administrate',$loc) ? 1 : 0),
							'configure'=>(expPermissions::check('configure',$loc) ? 1 : 0)
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
				echo sprintf(gt('The module "%s" was not found in the system.'),$module);
			}
		}
	}

	/* exdoc
	 * Runs the approriate action, by looking at the $_REQUEST variable.
	 * @node Subsystems:Theme
	 */
	//function self::runAction() {
	//    if (self::inAction()) {
	//        if (!AUTHORIZED_SECTION) {
	//            echo SITE_403_HTML;
	//        }
	//        if (expSession::is_set("themeopt_override")) {
	//            $config = expSession::get("themeopt_override");
	//            echo "<a class='mngmntlink sitetemplate_mngmntlink' href='".$config['mainpage']."'>".$config['backlinktext']."</a><br /><br />";
	//        }
	//
	//        //FIXME: module/controller glue code..remove ASAP
	//        $module = empty($_REQUEST['controller']) ? $_REQUEST['module'] : $_REQUEST['controller'];
	//        $isController = controllerExists($module);
	//
	//        if ($isController && !isset($_REQUEST['_common'])) {
	//            // this is being set just incase the url said module=modname instead of controller=modname
	//            // with SEF URls turned on its not really an issue, but with them off some of the links
	//            // aren't being made correctly...depending on how the {link} plugin was used in the view.
	//            $_REQUEST['controller'] = $module;
	//
	//            echo renderAction($_REQUEST);
	//        } else {
	//            if ($_REQUEST['action'] == 'index') {
	//                $view = empty($_REQUEST['view']) ? 'Default' : $_REQUEST['view'];
	//                $title = empty($_REQUEST['title']) ? '' : $_REQUEST['title'];
	//                $src = empty($_REQUEST['src']) ? null : $_REQUEST['src'];
	//                self::showModule($module, $view, $title, $src);
	//                return true;
	//            }
	//
	//            global $db, $user;
	//
	//            // the only reason we should have a controller down in this section is if we are hitting a common action like
	//            // userperms or groupperms...deal wit it.
	//            $loc = null;
	//            $loc->mod = $module;
	//            $loc->src = (isset($_REQUEST['src']) ? $_REQUEST['src'] : "");
	//            $loc->int = (isset($_REQUEST['int']) ? $_REQUEST['int'] : "");
	//            //if (isset($_REQUEST['act'])) $loc->act = $_REQUEST['act'];
	//
	//            if (isset($_REQUEST['_common'])) {
	//                $actfile = "/common/actions/" . $_REQUEST['action'] . ".php";
	//            } else {
	//                $actfile = "/" . $module . "/actions/" . $_REQUEST['action'] . ".php";
	//            }
	//
	//            if (is_readable(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile)) {
	//                include_once(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile);
	//            } elseif (is_readable(BASE.'framework/modules-1/'.$actfile)) {
	//                include_once(BASE.'framework/modules-1/'.$actfile);
	//            } else {
	//                echo SITE_404_HTML . '<br /><br /><hr size="1" />';
	//                echo sprintf(gt('No such module action').' : %1 : %2',strip_tags($module),strip_tags($_REQUEST['action']));
	//                echo '<br />';
	//            }
	//        }
	//    }
	//}

	public static function showAction($module, $action, $src="", $params="") {
		global $db, $user;

		$loc = null;
		$loc->mod = $module;
		$loc->src = (isset($src) ? $src : "");
		$loc->int = (isset($int) ? $int : "");

		$actfile = "/" . $module . "/actions/" . $action . ".php";
		if (isset($params)) {
			foreach ($params as $key => $value) {
				$_GET[$key] = $value;
			}
		}
		//if (isset($['_common'])) $actfile = "/common/actions/" . $_REQUEST['action'] . ".php";

//		if (is_readable(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile)) {
//			include(BASE."themes/".DISPLAY_THEME_REAL."/modules".$actfile);
		if (is_readable(BASE."themes/".DISPLAY_THEME."/modules".$actfile)) {
				include(BASE."themes/".DISPLAY_THEME."/modules".$actfile);
		} elseif (is_readable(BASE.'framework/modules-1/'.$actfile)) {
			include(BASE.'framework/modules-1/'.$actfile);
		} else {
			echo SITE_404_HTML . '<br /><br /><hr size="1" />';
			echo sprintf(gt('No such module action').' : %1 : %2',strip_tags($_REQUEST['module']),strip_tags($_REQUEST['action']));
			echo '<br />';
		}
	}

	/* exdoc
	 * @state <b>UNDOCUMENTED</b>
	 * @node Undocumented
	 */
	public static function getSubthemes($include_default = true,$theme = DISPLAY_THEME) {
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
	public static function showTopSectionalModule($module,$view,$title,$prefix = null, $pickable = false, $hide_menu=false) {
		global $db;

		if ($prefix == null) $prefix = "@section";
		$last_section = expSession::get("last_section");

		$section = $db->selectObject("section","id=".$last_section);
		// Loop until we find the top level parent.
		while ($section->parent != 0) $section = $db->selectObject("section","id=".$section->parent);

		self::showModule($module,$view,$title,$prefix.$section->id,$pickable,$section, $hide_menu);
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
	public static function showSectionalModule($module,$view,$title,$prefix = null, $pickable = false, $hide_menu=false) {
		global $db;

		if ($prefix == null) $prefix = "@section";

		$src = $prefix;

		if (expSession::is_set("themeopt_override")) {
			$config = expSession::get("themeopt_override");
			if (in_array($module,$config['ignore_mods'])) return;
			$src = $config['src_prefix'].$prefix;
			$section = null;
		} else {
			global $sectionObj;
			//$last_section = expSession::get("last_section");
			//$section = $db->selectObject("section","id=".$last_section);
			$src .= $sectionObj->id;
		}


		self::showModule($module,$view,$title,$src,$pickable,$sectionObj->id, $hide_menu);
	}

	/* exdoc
	 * Redirect User to Default Section
	 * @node Subsystems:Theme
	 */
	public static function goDefaultSection() {
		$last_section = expSession::get("last_section");
		if (defined('SITE_DEFAULT_SECTION') && SITE_DEFAULT_SECTION != $last_section) {
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
	 * Useful only if theme does not use self::main
	 *
	 * @param bool $public Whether or not the page is public.
	 * @node Subsystems:Theme
	 */
	public static function mainContainer() {
		global $router;

		if (!AUTHORIZED_SECTION) {
			// Set this so that a login on an Auth Denied page takes them back to the previously Auth-Denied page
//			expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_SECTIONAL);
			expHistory::set('manageable', $router->params);
			echo SITE_403_HTML;
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
			self::showSectionalModule("containermodule","Default","","@section",false,true);
	#   } else {
	#       self::showSectionalModule("containermodule","Default","","@section");
	#   }
	}

	public static function loadActionMaps() {
		if (is_readable(BASE.'themes/'.DISPLAY_THEME.'/action_maps.php')) {
			return include(BASE.'themes/'.DISPLAY_THEME.'/action_maps.php');
		} else {
			return array();
		}
	}

	public static function is_mobile() {
		//point the mobile browser to the right page
		$mobile_browser = 0;

		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}

		if (isset($_SERVER['HTTP_ACCEPT']) && (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }

		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki',/*'oper',*/'palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-');

		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}

		//if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
		//    $mobile_browser++;
		//}

		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
			$mobile_browser = 0;
		}

		return $mobile_browser;
	}

}

?>
