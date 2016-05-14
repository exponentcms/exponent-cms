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
 * This is the class expCore
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */

/** @define "BASE" "../../.." */
class expCore
{

    /**
     * Return an exponent location object with corrected module name
     *
     * @static
     *
     * @param null $mod
     * @param null $src
     * @param null $int
     *
     * @return object
     */
    public static function makeLocation($mod = null, $src = null, $int = null)
    {
        $loc = new stdClass();
        $loc->mod = !empty($mod) ? expModules::getModuleName(
            $mod
        ) : ''; // this will remove 'Controller' or add 'module'
        $loc->src = !empty($src) ? $src : '';
        $loc->int = !empty($int) ? strval(intval($int)) : '';
        return $loc;
    }

    /** exdoc
     * Return a full URL, given the desired querystring arguments as an associative array.
     * This function does take into account the SEF URLs settings and the SSL urls in the site config.
     *
     * @param Array  $params An associative array of the desired querystring parameters.
     * @param string $type
     * @param string $sef_name
     *
     * @return string
     * @node Subsystems:expCore
     */
    public static function makeLink($params, $type = '', $sef_name = '')
    {
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
     *
     * @param $params
     *
     * @return string
     */
    public static function makeRSSLink($params)
    {
//		$link = (ENABLE_SSL ? NONSSL_URL : URL_BASE);
        $link = URL_BASE;

        //FIXME: Hardcoded controller stuff!!
        if (expModules::controllerExists($params['module'])) {
            $link .= SCRIPT_RELATIVE . "site_rss.php" . "?";
        }

        foreach ($params as $key => $value) {
            $value = trim($value);
            $key = trim($key);
            if ($value != "") {
                $link .= urlencode($key) . "=" . urlencode($value) . "&";
            }
        }
        $link = substr($link, 0, -1);
        return htmlspecialchars($link, ENT_QUOTES);
    }

    /**
     * Return an old style podcast link
     * COMPATIBILITY - we now use the {rss_link} smarty function to build rss & podcast links
     *
     * @static
     *
     * @param $params
     *
     * @return string
     */
    public static function makePodcastLink($params)
    {
        self::makeRSSLink($params); // all rss links are now alike
    }

    /** exdoc
     * Return a full URL, given the desired querystring arguments as an associative array.
     * This function does take into account the SEF URLs settings and the SSL urls in the site config,
     * and uses the SSL url is the site is configured to use SSL.  Otherwise, it works exactly like
     * self::makeLink.
     *
     * @param Array $params An associative array of the desired querystring parameters.
     *
     * @return string
     * @node Subsystems:expCore
     */
    public static function makeSecureLink($params)
    {
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
                $value = trim($value);
                $key = trim($key);
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
    public static function makeSefUrl($title, $model)
    {
        global $db, $router;

        if (!empty($title)) {
            $sef_name = $router->encode($title);
        } else {
            $sef_name = $router->encode('Untitled');
        }
//        $dupe = $db->selectValue($model, 'sef_name', 'sef_name="' . $sef_name . '"');
        $dupe = $db->selectValue($model, 'sef_url', 'sef_url="' . $sef_name . '"');
        if (!empty($dupe)) {
            list($u, $s) = explode(' ', microtime());
            $sef_name .= '-' . $s . '-' . $u;
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
     *
     * @return bool
     * @node Subsystems:expCore
     */
    public static function URLisValid($url)
    {
        return (
            substr($url, 0, 7) == "http://" ||
            substr($url, 0, 8) == "https://" ||
            substr($url, 0, 7) == "mailto:" ||
            substr($url, 0, 6) == "ftp://"
        );
    }

    /** exdoc
     * Generates and returns a string stating the current maximum accepted size of
     * uploaded files.  It intelligently parses the php.ini configuration, so that settings of
     * 2K and 2048 are treated identically.
     *
     * @node Subsystems:expCore
     * @return string
     */
    public static function maxUploadSize()
    {
        $size = ini_get("upload_max_filesize");
        //		$size_msg = "";
        $type = substr($size, -1, 1);
        $shorthand_size = substr($size, 0, -1);
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
                if ($size >= 1024 * 1024 * 1024) { // Gigs
                    $size_msg = round(($size / (1024 * 1024 * 1024)), 2) . " GB";
                } else {
                    if ($size >= 1024 * 1024) { // Megs
                        $size_msg = round(($size / (1024 * 1024)), 2) . " MB";
                    } else {
                        if ($size >= 1024) { // Kilo
                            $size_msg = round(($size / 1024), 2) . " kB";
                        } else {
                            $size_msg = $size . " bytes";
                        }
                    }
                }
        }
        return $size_msg;
    }

    /** exdoc
     * Generates and returns a message stating the current maximum accepted size of
     * uploaded files.
     *
     * @node Subsystems:expCore
     * @return string
     */
    public static function maxUploadSizeMessage()
    {
        return sprintf(
            gt(
                'The maximum size of uploaded files is %s.  Uploading files larger than that may result in erratic behavior.'
            ),
            self::maxUploadSize()
        );
    }

    /** exdoc
     * This function converts an absolute path, such as the one provided
     * by the expTemplate::resolveFilePaths() function into a relative one.
     * This is useful if the file is not to be included server-
     * but loaded client-side
     *
     * @param string $inPath The absolute file path
     *
     * @return string
     * @node Subsystems:expCore
     */
    public static function abs2rel($inPath)
    {
        //TODO: Investigate the chances of BASE occurring more than once
        $outPath = str_replace(BASE, PATH_RELATIVE, $inPath);
        return $outPath;
    }

    public static function glob2keyedArray($workArray)
    {
        return expTemplate::glob2keyedArray($workArray);
    }

    public static function resolveFilePaths($type, $name, $subtype, $subname)
    {
        return expTemplate::resolveFilePaths($type, $name, $subtype, $subname);
    }

    public static function buildNameList($type, $name, $subtype, $subname)
    {
        return expTemplate::buildNameList($type, $name, $subtype, $subname);
    }

    /**
     * Return the appropriate currency symbol
     *
     * @static
     *
     * @param $currency_type
     *
     * @return string
     */
    public static function getCurrencySymbol($currency_type = ECOM_CURRENCY)
    {
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
     * Return the amount properly formatted with the currency symbol
     *
     * @static
     *
     * @param $amount
     * @param $currency_type
     *
     * @return string
     */
    public static function getCurrency($amount, $currency_type = ECOM_CURRENCY)
    {
        return self::getCurrencySymbol() . number_format($amount,2,".",",");
    }

    /**
     * Use cUrl to get data from url
     *
     * @static
     *
     * @param      $url
     * @param bool $ref
     * @param bool $post
     *
     * @return mixed
     */
    public static function loadData($url, $ref = false, $post = false)
    {
        $chImg = curl_init($url);
        curl_setopt($chImg, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chImg, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt(
            $chImg,
            CURLOPT_USERAGENT,
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0) Gecko/20100101 Firefox/4.0"
        );
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
     *
     * @param      $url
     * @param      $filename
     * @param bool $ref
     * @param bool $post
     */
    public static function saveData($url, $filename, $ref = false, $post = false)
    {
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

     */
    public static function cast($source, $destinationtype)
    {
        $destination = new $destinationtype();
        if (is_null($destination)) {
            return $destination;
        }
        $sourceReflection = new ReflectionObject($source);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $name = $sourceProperty->getName();
            $destination->{$name} = $source->$name;
        }
        return $destination;
    }

    /**
   	 * Determines if the current version of PHP is equal to or greater than the supplied value
   	 *
   	 * @param	string
   	 * @return	bool	TRUE if the current version is $version or higher
   	 */
    public static function is_php($version)
   	{
   		static $_is_php;
   		$version = (string) $version;

   		if ( ! isset($_is_php[$version]))
   		{
   			$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
   		}

   		return $_is_php[$version];
   	}

    public static function array_diff_assoc_recursive($array1, $array2)
    {
        foreach($array1 as $key => $value)
        {
            if(is_array($value))
            {
                  if(!isset($array2[$key]))
                  {
                      $difference[$key] = $value;
                  }
                  elseif(!is_array($array2[$key]))
                  {
                      $difference[$key] = $value;
                  }
                  else
                  {
                      $new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
                      if($new_diff != FALSE)
                      {
                            $difference[$key] = $new_diff;
                      }
                  }
              }
              elseif(!isset($array2[$key]) || $array2[$key] != $value)
              {
                  $difference[$key] = $value;
              }
        }
        return !isset($difference) ? 0 : $difference;
    }
}

?>