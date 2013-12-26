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
 * This is the class expCore
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expCore {

	/**
	 * Return an exponent location object with corrected module name
	 *
	 * @static
	 * @param null $mod
	 * @param null $src
	 * @param null $int
	 * @return object
	 */
	public static function makeLocation($mod=null,$src=null,$int=null) {
		$loc = new stdClass();
		$loc->mod = !empty($mod) ? expModules::getModuleName($mod) : '';  // this will remove 'Controller' or add 'module'
		$loc->src = !empty($src) ? $src : '';
		$loc->int = !empty($int) ? strval(intval($int)) : '';
		return $loc;
	}

	/** exdoc
	 * Return a full URL, given the desired querystring arguments as an associative array.
	 *
	 * This function does take into account the SEF URLs settings and the SSL urls in the site config.
	 *
	 * @param Array $params An associative array of the desired querystring parameters.
	 * @param string $type
	 * @param string $sef_name
	 * @return string
	 * @node Subsystems:expCore
	 */
	public static function makeLink($params,$type='',$sef_name='') {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		//   Now that we have the router class, this function is here for compatibility reasons only.
		//   it will most likely be deprecated in newer releases of exponent.
		//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $router;

		// this is here for compatibility with the navigation module and the old way make link used prior
		// to having the router class
		$params['sef_name'] = $sef_name;

		// now that we have the router class we'll use it to build the link and then return it.
		return $router->makeLink($params);
	}

	/**
	 * Return an old style rss link
	 * COMPATIBILITY - we now use the {rss_link} smarty function to build rss links
     *
	 * @static
	 * @param $params
	 * @return string
	 */
	public static function makeRSSLink($params) {
//		$link = (ENABLE_SSL ? NONSSL_URL : URL_BASE);
        $link = URL_BASE;

		//FIXME: Hardcoded controller stuff!!
		if (expModules::controllerExists($params['module'])) {
			$link .= SCRIPT_RELATIVE . "site_rss.php" . "?";
		}

		foreach ($params as $key=>$value) {
			$value = chop($value);
			$key = chop($key);
			if ($value != "") $link .= urlencode($key)."=".urlencode($value)."&";
		}
		$link = substr($link,0,-1);
		return htmlspecialchars($link,ENT_QUOTES);
	}

	/**
	 * Return an old style podcast link
     * COMPATIBILITY - we now use the {rss_link} smarty function to build rss & podcast links
	 *
	 * @static
	 * @param $params
	 * @return string
	 */
	public static function makePodcastLink($params) {
        self::makeRSSLink($params);  // all rss links are now alike
	}

	/** exdoc
	 * Return a full URL, given the desired querystring arguments as an associative array.
	 *
	 * This function does take into account the SEF URLs settings and the SSL urls in the site config,
	 * and uses the SSL url is the site is configured to use SSL.  Otherwise, it works exactly like
	 * self::makeLink.
	 *
	 * @param Array $params An associative array of the desired querystring parameters.
	 * @return string
	 * @node Subsystems:expCore
	 */
	public static function makeSecureLink($params) {
		global $router;

			// this is here for compatibility with the navigation module and the old way make link used prior
			// to having the router class
//			$params['sef_name'] = sef_name;  //FIXME $sef_name isn't set??

			// now that we have the router class we'll use it to build the link and then return it.
			return $router->makeLink($params, false, true);
	/*
		if (!ENABLE_SSL) return self::makeLink($params);
		$link = SSL_URL .  SCRIPT_RELATIVE . SCRIPT_FILENAME . "?";
		foreach ($params as $key=>$value) {
			$value = chop($value);
			$key = chop($key);
			if ($value != "") $link .= urlencode($key)."=".urlencode($value)."&";
		}
		$link = substr($link,0,-1);
		return $link;
	*/
	}

    /**
     * make an sef_name for specific model
     *
     * @param string $title
     * @param string $model
     *
     * @return mixed|string
     */
    public static function makeSefUrl($title,$model) {
        global $db, $router;

        if (!empty($title)) {
            $sef_name = $router->encode($title);
        } else {
            $sef_name = $router->encode('Untitled');
        }
        $dupe = $db->selectValue($model, 'sef_name', 'sef_name="'.$sef_name.'"');
        if (!empty($dupe)) {
            list($u, $s) = explode(' ',microtime());
            $sef_name .= '-'.$s.'-'.$u;
        }
        return $sef_name;
    }

	/** exdoc
	 * This function checks a full URL against a set of
	 * known protocols (like http and https) and determines
	 * if the URL is valid.  Returns true if the URL is valid,
	 * and false if otherwise.
	 *
	 * @param string $url The URL to test for validity
	 * @return bool
	 * @node Subsystems:expCore
	 */
	public static function URLisValid($url) {
		return (
			substr($url,0,7) == "http://" ||
			substr($url,0,8) == "https://" ||
			substr($url,0,7) == "mailto:" ||
			substr($url,0,6) == "ftp://"
		);
	}

    /** exdoc
   	 * Generates and returns a string stating the current maximum accepted size of
   	 * uploaded files.  It intelligently parses the php.ini configuration, so that settings of
   	 * 2K and 2048 are treated identically.
   	 * @node Subsystems:expCore
   	 * @return string
   	 */
   	public static function maxUploadSize() {
   		$size = ini_get("upload_max_filesize");
   //		$size_msg = "";
   		$type = substr($size,-1,1);
   		$shorthand_size = substr($size,0,-1);
   		switch ($type) {
   			case 'M':
   				$size_msg = $shorthand_size . ' MB';
   				break;
   			case 'K':
   				$size_msg = $shorthand_size . ' kB';
   				break;
   			case 'G': // PHP5 +
   				$size_msg = $shorthand_size . ' GB';
   				break;
   			default:
   				if ($size >= 1024*1024*1024) { // Gigs
   					$size_msg = round(($size / (1024*1024*1024)),2) . " GB";
   				} else if ($size >= 1024*1024) { // Megs
   					$size_msg = round(($size / (1024*1024)),2) . " MB";
   				} else if ($size >= 1024) { // Kilo
   					$size_msg = round(($size / 1024),2) . " kB";
   				} else {
   					$size_msg = $size . " bytes";
   				}
   		}
   		return $size_msg;
   	}

	/** exdoc
	 * Generates and returns a message stating the current maximum accepted size of
	 * uploaded files.
	 * @node Subsystems:expCore
	 * @return string
	 */
	public static function maxUploadSizeMessage() {
		return sprintf(gt('The maximum size of uploaded files is %s.  Uploading files larger than that may result in erratic behavior.'),self::maxUploadSize());
	}

	/** exdoc
	 * This function converts an absolute path, such as the one provided
	 * by the self::resolveFilePaths() function into a relative one.
	 *
	 * This is useful if the file is not to be included server-
	 * but loaded client-side
	 *
	 * @param string $inPath The absolute file path
	 * @return string
	 * @node Subsystems:expCore
	 */
	public static function abs2rel($inPath) {
		//TODO: Investigate the chances of BASE occurring more than once
		$outPath = str_replace(BASE, PATH_RELATIVE, $inPath);
		return $outPath;
	}

	/**
	 * helper function
	 *
	 * @param $workArray
	 * @return array
	 */
	public static function glob2keyedArray($workArray){
		$temp = array();
        if (is_array($workArray)) {
            foreach($workArray as $myWorkFile){
                $temp[basename($myWorkFile)] = $myWorkFile;
            }
        }
		return $temp;
	}

	/** exdoc
	 * This function finds the most appropriate version of a file
	 *  - if given wildcards, files -
	 * and returns an array with the file's physical location's full path or,
	 * if no file was found, false
	 *
	 * @param string $type (to be superseded) type of base resource (= directory name)
	 * @param string $name (hopefully in the future type named) Resource identifier (= class name = directory name)
	 * @param string $subtype type of the actual file (= file extension = (future) directory name)
	 * @param string $subname name of the actual file (= filename name without extension)
	 *
	 * @return mixed
	 * @node Subsystems:expCore
	 */
	public static function resolveFilePaths($type, $name, $subtype, $subname) {
		//TODO: implement caching
		//TODO: optimization - walk the tree backwards and stop on the first match
	   // eDebug($type);
	   // eDebug($name);
	   // eDebug($subtype);
	   // eDebug($subname);
		//once baseclasses are in place, simply lookup the baseclass name of an object
		if($type == "guess") {
			// new style name processing
			//$type = array_pop(preg_split("*(?=[A-Z])*", $name));

			//TODO: convert everything to the new naming model
			if(stripos($name, "module") != false){
				$type = "modules";
			} else if (stripos($name, "control") != false) {
				$type = "controls";
			} else if (stripos($name, "theme") != false) {
				$type = "themes";
			}
		}

		// convert types into paths
		$relpath = '';
		if ($type == "modules" || $type == 'profileextension') {
            $relpath .= "framework/modules-1/";
        } elseif($type == "Controller" || $type=='controllers') {
            $relpath .= "framework/views/";
        } elseif($type == "forms") {
            if ($name == "event/email") {
                $relpath .= "framework/modules/events/views/";
            } elseif ($name == "forms/calendar") {  //TODO  forms/calendar only used by calendarmodule
                $relpath .= "framework/modules-1/calendarmodule/";
            } else {
                $relpath .= "framework/core/forms/";
            }
        } elseif($type == "themes" || $type == "Control" || $type == "Theme") {
            $relpath .= "themes/";
        } elseif($type == "models") {
            $relpath .= "models/";
        } elseif($type == "controls") {
//			$relpath .= "themes/";
            $relpath .= "external/";
//        } elseif($type == "Control") {
//            $relpath .= "themes/";
        } elseif($type == "Form") {
            $relpath .= "framework/core/forms/";
        } elseif($type == "Module") {
            $relpath .= "modules/";
//        } elseif($type == "Theme") {
//            $relpath .= "themes/";
        }

		// for later use for searching in lib/common
		$typepath = $relpath;
//		if ($name != "" && $name != "forms/calendar") {  //TODO  forms/calendar only used by calendarmodule
        if ($name != "" && $name != "event/email" && $name != "forms/calendar") {  //TODO  forms/calendar only used by calendarmodule
			$relpath .= $name . "/";
		}

		// for later use for searching in lib/common
		$relpath2 = '';
		if ($subtype == "css") {
            $relpath2 .= "css/";
        } elseif($subtype == "js") {
            $relpath2 .= "js/";
        } elseif($subtype == "tpl") {
            if ($type == 'Controller' || $type == 'controllers') {
                //do nothing
            } elseif ($name == "forms/calendar") {  //TODO  forms/calendar only used by calendarmodule
                $relpath2 .= "forms/calendar/";
            } elseif ($name == "event/email") {
//				$relpath2 .= "/";
                $relpath2 .= "event/email/";
            } elseif ($type == 'controls' || $type == 'Control') {
                $relpath2 .= 'editors/';
            } elseif ($type == 'profileextension') {
                $relpath2 .= "extensions/";
            } elseif ($type == 'globalviews') {
                $relpath2 .= "framework/core/views/";
            } else {
                $relpath2 .= "views/";
            }
        } elseif($subtype == "form") {
            $relpath2 .= "views/";
        } elseif($subtype == "action") {
            $relpath2 .= "actions/";
            //HACK: workaround for now
            $subtype = "php";
        }

		$relpath2 .= $subname;
		if($subtype != "") {
			$relpath2 .= "." . $subtype;
		}

		$relpath .= $relpath2;

		//TODO: handle subthemes
		//TODO: now that glob is used build a syntax for it instead of calling it repeatedly
		//latter override the precursors
		$locations = array(BASE, THEME_ABSOLUTE);
		$checkpaths = array();
		foreach($locations as $location) {
			$checkpaths[] = $location . $typepath . $relpath2;
			if (strstr($location,THEME_ABSOLUTE) && strstr($relpath,"framework/modules-1")) {
				$checkpaths[] = $location . str_replace("framework/modules-1", "modules", $relpath);
			} else {
				$checkpaths[] = $location . $relpath;
			}
			//eDebug($relpath);
		}
//		eDebug($checkpaths);

		//TODO: handle the - currently unused - case where there is the same file in different $type categories
		$myFiles = array();
		foreach($checkpaths as $checkpath) {
//		eDebug($checkpath);
			$tempFiles = self::glob2keyedArray(glob($checkpath));
			if ($tempFiles != false) {
				$myFiles = array_merge($myFiles, $tempFiles);
			}
		}
//        eDebug($myFiles);
		if(count($myFiles) != 0) {
			return array_values($myFiles);
		} else {
			//TODO: invent better error handling, maybe an error message channel ?
			//die("The file " . basename($filepath) . " could not be found in the filesystem");
			return false;
		}

	}

	/** exdoc
	 * This function is a wrapper around self::resolveFilePaths()
	 * and returns a list of the basenames, minus the file extensions - if any
	 *
	 * @param string $type (to be superseded) type of base resource (= directory name)
	 * @param string $name (hopefully in the future type named) Resource identifier (= class name = directory name)
	 * @param string $subtype type of the actual file (= file extension = (future) directory name)
	 * @param string $subname name of the actual file (= filename name without extension)
	 *
	 * @return array
	 * @node Subsystems:expCore
	 */
	public static function buildNameList($type, $name, $subtype, $subname) {  //FIXME only used by 1) event module edit action (email forms) & 2) expTemplate::listModuleViews for OS modules
		$nameList = array();
		$fileList = self::resolveFilePaths($type, $name, $subtype, $subname);
		if ($fileList != false) {
			foreach ($fileList as $file) {
				// self::resolveFilePaths() might also return directories
				if (basename($file) != "") {
					// just to make sure: do we have an extension ?
					// relying on there is only one dot in the filename
					$extension = strstr(basename($file), ".");
					$nameList[basename($file, $extension)] = basename($file, $extension);
				} else {
					// don't know where this might be needed, but...
					$nameList[] = array_pop(explode("/", $file));
				}
			}
		}
		return $nameList;
	}

	/**
	 * Return the appropriate currency symbol
	 *
	 * @static
	 * @param $currency_type
	 * @return string
	 */
	public static function getCurrencySymbol($currency_type=ECOM_CURRENCY) {
		switch ($currency_type) {
			case "USD":
				return "$";
				break;
			case "CAD":
			case "AUD":
				return "$";
				break;
			case "EUR":
				return "&euro;";
				break;
			case "GBP":
				return "&#163;";
				break;
			case "JPY":
				return "&#165;";
			break;
			default:
				return "$";
	    }
	}

    /**
     * Use cUrl to get data from url
     *
     * @static
     * @param $url
     * @param bool $ref
     * @param bool $post
     * @return mixed
     */
    public static function loadData($url, $ref = false, $post = false) {
    	$chImg = curl_init($url);
    	curl_setopt($chImg, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chImg, CURLOPT_CONNECTTIMEOUT, 30);
    	curl_setopt($chImg, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0) Gecko/20100101 Firefox/4.0");
        if ($post) {
            curl_setopt($chImg, CURLOPT_POST, true);
        }
    	if ($ref) {
    		curl_setopt($chImg, CURLOPT_REFERER, $ref);
    	}
    	$curl_scraped_data = curl_exec($chImg);
        if ($post) {
            curl_setopt($chImg, CURLOPT_POST, false);
        }
    	curl_close($chImg);
    	return $curl_scraped_data;
    }

    /**
     * Use cUrl to save data from url to file (download)
     *
     * @static
     * @param $url
     * @param $filename
     * @param bool $ref
     * @param bool $post
     */
    public static function saveData($url, $filename, $ref = false, $post = false) {
    	$chImg = curl_init($url);
        $fp = fopen($filename, 'w');
        curl_setopt($chImg, CURLOPT_FILE, $fp);
//    	curl_setopt($chImg, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chImg, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($chImg, CURLOPT_CONNECTTIMEOUT, 30);
//    	curl_setopt($chImg, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0) Gecko/20100101 Firefox/4.0");
        if ($post) {
            curl_setopt($chImg, CURLOPT_POST, true);
        }
    	if ($ref) {
    		curl_setopt($chImg, CURLOPT_REFERER, $ref);
    	}
        $curl_scraped_data = curl_exec($chImg);
        if ($post) {
            curl_setopt($chImg, CURLOPT_POST, false);
        }
        curl_close($chImg);
        fclose($fp);
//    	return $curl_scraped_data;
    }

    /**
     * Casts one object type to another object type
     *
     */
    public static function cast($source, $destinationtype) {
        $destination = new $destinationtype();
        if (is_null($destination)) return $destination;
        $sourceReflection = new ReflectionObject($source);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $name = $sourceProperty->getName();
            $destination->{$name} = $source->$name;
        }
        return $destination;
    }

    /**
     * Return the 'color code' for the other framework
     * default is to convert a standard button color to the bootstrap button color
     *
     * @param        $color
     * @param        $size
     * @param string $returntype
     *
     * @return mixed|string
     */
    public static function buttonColor($color, $size=null, $returntype='bootstrap') {
        $colors = array(
            'green' => 'btn-success',
            'blue' => 'btn-primary',
            'red' => 'btn-danger',
            'magenta' => 'btn-danger',
            'orange' => 'btn-warning',
            'yellow' => 'btn-warning',
            'grey' => 'btn-default',
            'purple' => 'btn-info',
            'black' => 'btn-inverse',
            'pink' => 'btn-danger',
        );
        if ($returntype == 'bootstrap') {
            if (!empty($colors[$color])) {  // awesome to bootstrap button conversion
                $found = $colors[$color];
            } else {
                $found = 'btn-default';
            }
            if (BTN_SIZE != 'large' || (!empty($size) && $size != 'large')) {
                $btn_size = 'btn-mini';
                $icon_size = '';
            } else {
                $btn_size = 'btn-small';
                $icon_size = 'icon-large';
            }
            $found .= ' ' . $btn_size;
            return $found;
        } else {
            return array_search($color, $colors);  // bootstrap to awesome button conversion
        }
    }

    /**
     * Return the bootstrap icon type associated with standard icon name/type
     *
     * @param        $class
     * @param string $returntype
     *
     * @return stdClass|string
     */
    public static function buttonIcon($class, $returntype='bootstrap') {
        if ($returntype == 'bootstrap') {
            $btn_type = '';
            switch ($class) {
                case 'delete' :
                case 'deletetitle' :
                    $class = "remove-sign";
                    $btn_type = "btn-danger";  // red
                    break;
                case 'add' :
                case 'addtitle' :
                case 'switchtheme add' :
                    $class = "plus-sign add";
                    $btn_type = "btn-success";  // green
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
                    $class ='double-angle-right';
                    break;
                case 'page_prev' :
                    $class = 'double-angle-left';
                    break;
                case 'change_password' :
                    $class = 'key';
                    break;
                case 'clean' :
                    $class = 'check';
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
                    $class = 'check';
                    $btn_type = "btn-success";  // green
                    break;
            }
            $found = new stdClass();
            $found->type = $btn_type;
            $found->class = $class;
            return $found;
        } else {
            return $class;
        }
    }

}
?>