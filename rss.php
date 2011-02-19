<?php
##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

if (!defined("EXPONENT")) include_once('exponent.php');
if (!defined('SYS_RSS')) include_once('core_rss.php');

//$location = new Location($_REQUEST['module'],$_REQUEST['identifier'],$_REQUEST['internal']);
$location->mod = "";
$location->src = "";
$location->int = "";
if (isset($_REQUEST['module'])) $location->mod = $_REQUEST['module'];
if (isset($_REQUEST['src'])) $location->src = $_REQUEST['src'];
if (isset($_REQUEST['int'])) $location->int = $_REQUEST['int'];

$module = null;
if (isset($_REQUEST['module'])) {
	 $module = $_REQUEST['module']; // we passed something like '?module=news'
}
//echo "module: ".$module;
if (isset($module)) {
		//get the RSS Items from the module	
		include_once('modules/'.$module.'/class.php');
		$obj = new $module();	
		$rss_items = $obj->getRSSContent($location);

		//get the modules config data which should have the feed title & desc
		$config = $db->selectObject($module."_config", "location_data='".serialize($location)."'");		

		if ($config->enable_rss == true) {
			$rss = new UniversalFeedCreator();
			$rss->cssStyleSheet = "";
			$rss->useCached();
			$rss->title = $config->feed_title;
			$rss->description = $config->feed_desc;
			$rss->link = "http://".HOSTNAME.PATH_RELATIVE;
			$rss->syndicationURL = "http://".HOSTNAME.PATH_RELATIVE.$_SERVER['PHP_SELF'];	

			foreach ($rss_items as $item) {
				$rss->addItem($item);
			}

			header("Content-type: text/xml");
			echo $rss->createFeed("RSS2.0");
		} else {
			echo "This RSS feed has been disabled.";
		}
} 

?>
