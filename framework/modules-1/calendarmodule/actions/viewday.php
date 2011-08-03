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
 
if (!defined('EXPONENT')) exit('');

exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);

$time = (isset($_GET['time']) ? $_GET['time'] : time());
$info = getdate(intval($time));
$start = mktime(0,0,0,$info['mon'],$info['mday'],$info['year']);
$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");

$template = new template("calendarmodule","_viewday",$loc,false);

$locsql = "(location_data='".serialize($loc)."'";
// look for possible aggregate
$config = $db->selectObject("calendarmodule_config","location_data='".serialize($loc)."'");
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
//$dates = $db->selectObjects("eventdate","location_data='".serialize($loc)."' AND date = '" . $start . "'");
$dates = $db->selectObjects("eventdate",$locsql." AND date = '" . $start . "'");
$events = array();
foreach ($dates as $d) {
	$o = $db->selectObject("calendar","id=".$d->event_id);
	if ($o != null) {
		$o->eventdate = $d;
		$o->eventstart += $d->date;
		$o->eventend += $d->date;
		$thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$o->id);
		$o->permissions = array(
			"administrate"=>(exponent_permissions_check("administrate",$thisloc) || exponent_permissions_check("administrate",$loc)),
			"edit"=>(exponent_permissions_check("edit",$thisloc) || exponent_permissions_check("edit",$loc)),
			"delete"=>(exponent_permissions_check("delete",$thisloc) || exponent_permissions_check("delete",$loc))
		);
		//Get the image file if there is one.
		if (isset($o->file_id) && $o->file_id > 0) {
			$file = $db->selectObject('file', 'id='.$o->file_id);
			$o->image_path = $file->directory.'/'.$file->filename;
		}
		
		$events[] = $o;
	}
}

$template->register_permissions(
	array("post","edit","delete","administrate","manage_approval"),
	$loc
);

if (!$config) {
	$config->enable_categories = 0;
	$config->enable_ical = 1;
}

$template->assign("config",$config);
if (!isset($config->enable_ical)) {$config->enable_ical = 1;}
$template->assign("enable_ical", $config->enable_ical);

$template->assign("events",$events);
$template->assign("now",$time);
$template->assign("prevday3",strtotime('-3 days',$time));
$template->assign("prevday2",strtotime('-2 days',$time));
$template->assign("prevday",strtotime('-1 days',$time));
$template->assign("nextday",strtotime('+1 days',$time));
$template->assign("nextday2",strtotime('+2 days',$time));
$template->assign("nextday3",strtotime('+3 days',$time));

$template->assign('moduletitle',$title);

$template->output();

?>
