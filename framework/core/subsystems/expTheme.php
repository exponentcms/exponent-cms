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
/**
 * This is the class expTheme
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */

class expTheme {

    public static function head($config = array()){
    	echo headerInfo($config); 
		echo exponent_theme_advertiseRSS();
    }
    
    public static function foot($params = array()) {
    	echo footerInfo($params); 
    }
    
    public static function main() {
    	exponent_theme_main();
    }
    
    public static function module($params) {
        if (isset($params['controller'])) {
            self::showController($params);
        } else if (isset($params['module'])) {
            $moduletitle = (isset($params['moduletitle'])) ? $params['moduletitle'] : "";
            $source = (isset($params['source'])) ? $params['source'] : "";
            $chrome = (isset($params['chrome'])) ? $params['chrome'] : false;
            $scope = (isset($params['scope'])) ? $params['scope'] : "global";
            
            if ($scope=="global") {
                exponent_theme_showModule($params['module']."module",$params['view'],$moduletitle,$source,false,null,$chrome);
            }
            if ($scope=="top-sectional") {
                exponent_theme_showTopSectionalModule($params['module']."module", //module
                                                    $params['view'], //view
                                                    $moduletitle, // Title
                                                    $source, // source
                                                    false, // prefix??  no idea...
                                                    null, // used to apply to source picker. does nothing now.
                                                    $chrome // Show chrome
                                                    );
            }
            if ($scope=="sectional") {
                exponent_theme_showSectionalModule($params['module']."module", //module
                                                    $params['view'], //view
                                                    $moduletitle, // title
                                                    $source, // source
                                                    false, // prefix??  no idea...
                                                    null, // used to apply to source picker. does nothing now.
                                                    $chrome // Show chrome
                                                    );
            }
        }
    }
    
    public static function showController($params=array()) {
        global $sectionObj, $db;
        if (empty($params)) return false;
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

        exponent_theme_showModule(getControllerClassName($params['controller']),$params['view'],$params['title'],$params['source'],false,null,$params['chrome'],$requestvars);
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
        if (self::inAction() && (!empty($router->url_parts[0]) && controllerExists($router->url_parts[0]))) {
            $classname = getControllerClassName($router->url_parts[0]);
            $controller = new $classname();
            $metainfo = $controller->metainfo();
        } else {
            $metainfo['title'] = ($sectionObj->page_title == "") ? SITE_TITLE : $sectionObj->page_title;	
	        $metainfo['keywords'] = ($sectionObj->keywords == "") ? SITE_KEYWORDS : $sectionObj->keywords;
	        $metainfo['description'] = ($sectionObj->description == "") ? SITE_DESCRIPTION : $sectionObj->description;	
        }
        
        return $metainfo;
    }
    
    public static function inAction() {
        return (isset($_REQUEST['action']) && (isset($_REQUEST['module']) || isset($_REQUEST['controller'])));
    }
    
    public function grabView($path,$filename) {        
        $dirs = array(
            BASE.'themes/'.DISPLAY_THEME_REAL.'/'.$path,
            BASE.'framework/'.$path,
        );
        
        foreach ($dirs as $dir) {
            if (file_exists($dir.$filename.'.tpl')) return $dir.$form.'.tpl';    
        }
        
        return false;
    }
    
    public function grabViews($path,$filter='') {        
        $dirs = array(
            BASE.'themes/'.DISPLAY_THEME_REAL.'/'.$path,
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
    
}

?>
