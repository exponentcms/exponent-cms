<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');
global $router;

//expHistory::flowSet(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);
expHistory::set('viewable', $router->params);

$locsql = "(location_data='".serialize($loc)."'";
// look for possible aggregate
$config = $db->selectObject("calendarmodule_config","location_data='".serialize($loc)."'");
if (!$config) {
	$config->enable_categories = 0;
	$config->enable_ical = 1;
}
if (!empty($config->aggregate)) {
	$locations = unserialize($config->aggregate);
	foreach ($locations as $source) {
		$tmploc = null;
		$tmploc->mod = 'calendarmodule';
		$tmploc->src = $source;
		$tmploc->int = '';
		$locsql .= " OR location_data='".serialize($tmploc)."'";
	}
}
$locsql .= ')';

if (isset($config->rss_limit) && ($config->rss_limit > 0)) {
	$rsslimit = " AND date <= " . strtotime('+'.$config->rss_limit.' days',time());
} else {
	$rsslimit = "";
}

$dates = $db->selectObjects("eventdate",$locsql." AND date >= ".expDateTime::startOfDayTimestamp(time()).$rsslimit);
$all_events = calendarmodule::_getEventsForDates($dates);
$viewing_tag = $db->selectObject('tags', "id=".intval($_REQUEST['id']));
$events = array();
for ($i = 0; $i < count($all_events); $i++) {
	$ploc = expCore::makeLocation($loc->mod,$loc->src,$all_events[$i]->id);
	$not_there = true;
	$tags = unserialize($all_events[$i]->tags);
	$selected_tags = $db->selectObjectsInArray('tags', $tags);
	//eDebug($selected_tags);
	for ($j=0; $j < count($tags); $j++){
		if ($tags[$j] == intval($_REQUEST['id'])) $not_there = false;
	}
	if ($not_there == false) {
		$event = $all_events[$i];
		$event->selected_tags = $selected_tags;
		$event->permissions = array(
			"manage"=>(expPermissions::check("manage",$ploc) || expPermissions::check("manage",$loc)),
			"edit"=>(expPermissions::check("edit",$ploc) || expPermissions::check("edit",$loc)),
			"delete"=>(expPermissions::check("delete",$ploc) || expPermissions::check("delete",$loc))
		);
		array_push($events, $event);
	}
}

$template = new template("calendarmodule","_viewtag",$loc,false);
$template->register_permissions(
	array('manage','configure','create','edit','delete'),
	$loc
);
$template->assign("config",$config);
if (!isset($config->enable_ical)) {$config->enable_ical = 1;}
$template->assign("enable_ical", $config->enable_ical);
		
$template->assign("items",$events);

$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
$template->assign('moduletitle',$title." (<em>tagged with '".$viewing_tag->name."'</em>)");
$template->assign('viewing_tag',$viewing_tag);
$template->output();

?>
