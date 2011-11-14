<?php
##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "." */

require_once('exponent.php');
require_once(BASE.'external/feedcreator.class.php');

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
	include_once('framework/modules-1/'.$module.'/class.php');
	$obj = new $module();	
	$rss_items = $obj->getRSSContent($location);
	$itemdate = array();
	foreach($rss_items as $item) {
		$itemdate[] = strtotime($item->date); 
	} 
	$pubDate = date('r', max($itemdate)); 
	
	//get the modules config data which should have the feed title & desc
	$config = $db->selectObject($module."_config", "location_data='".serialize($location)."'");		
	$ttl = $config->rss_cachetime;
	if ($ttl == 0) { $ttl = 1440; }
	if ($config->enable_rss == true) {
		$rss = new UniversalFeedCreator();
		$rss->cssStyleSheet = "";
		$rss->useCached();
		$rss->title = $config->feed_title;
		$rss->description = $config->feed_desc;
		$rss->ttl = $ttl;
		$rss->pubDate = $pubDate;
		$rss->link = "http://".HOSTNAME.PATH_RELATIVE;
		$rss->syndicationURL = "http://".HOSTNAME.PATH_RELATIVE.$_SERVER['PHP_SELF'];	
		if ($_REQUEST['module'] == "resourcesmodule") {
			$rss->itunes->summary = $config->feed_desc;
			$rss->itunes->author = ORGANIZATION_NAME;
			$rss->itunes->category = '';
			$rss->itunes->subcategory = '';
			$rss->itunes->image = URL_FULL."framework/modules/filedownloads/assets/images/logo.png";
			$rss->itunes->explicit = 0;
			$rss->itunes->subtitle = 0;
			$rss->itunes->keywords = 0;
			$rss->itunes->owner_email = 0;
		}

		foreach ($rss_items as $item) {
			$rss->addItem($item);
		}

		header("Content-type: text/xml");
		if ($module == "resourcesmodule") {
			echo $rss->createFeed("PODCAST");
		} else {
			echo $rss->createFeed("RSS2.0");
		}
	} else {
		echo gt("This RSS feed has been disabled.");
	}
} 

?>