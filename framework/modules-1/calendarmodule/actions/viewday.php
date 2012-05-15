<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

global $router;

expHistory::set('viewable', $router->params);

$locsql = "(location_data='".serialize($loc)."'";
// look for possible aggregate
$config = $db->selectObject("calendarmodule_config","location_data='".serialize($loc)."'");
if (!empty($config->aggregate)) {
	$locations = unserialize($config->aggregate);
	foreach ($locations as $source) {
		$tmploc = new stdClass();
		$tmploc->mod = 'calendarmodule';
		$tmploc->src = $source;
		$tmploc->int = '';
		$locsql .= " OR location_data='".serialize($tmploc)."'";
	}
}
$locsql .= ')';

$template = new template("calendarmodule","_viewday",$loc,false);

$time = (isset($_GET['time']) ? $_GET['time'] : time());
$info = getdate(intval($time));
$start = mktime(0,0,0,$info['mon'],$info['mday'],$info['year']);

//$dates = $db->selectObjects("eventdate","location_data='".serialize($loc)."' AND date = '" . $start . "'");
$dates = $db->selectObjects("eventdate",$locsql." AND date = '" . $start . "'");
//FIXME isn't it better to use calendarmodule::_getEventsForDates less permissions
$events = array();
foreach ($dates as $d) {
	$o = $db->selectObject("calendar","id=".$d->event_id);
	if ($o != null) {
		$o->eventdate = $d;
		$o->eventstart += $d->date;
		$o->eventend += $d->date;
		$thisloc = expCore::makeLocation($loc->mod,$loc->src,$o->id);
		$o->permissions = array(
			"manage"=>(expPermissions::check("manage",$thisloc) || expPermissions::check("manage",$loc)),
			"edit"=>(expPermissions::check("edit",$thisloc) || expPermissions::check("edit",$loc)),
			"delete"=>(expPermissions::check("delete",$thisloc) || expPermissions::check("delete",$loc))
		);
		$events[] = $o;
	}
}
//FIXME add external events to $events for date $start, one day
$extitems = calendarmodule::getExternalEvents($loc,$start);
$events = array_merge($extitems,$events);

$template->register_permissions(
	array("create","edit","delete","manage"),
	$loc
);
$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
$template->assign('moduletitle',$title);
$template->assign("config",$config);

$template->assign("events",$events);
$template->assign("now",$time);
$template->assign("prevday3",strtotime('-3 days',$time));
$template->assign("prevday2",strtotime('-2 days',$time));
$template->assign("prevday",strtotime('-1 days',$time));
$template->assign("nextday",strtotime('+1 days',$time));
$template->assign("nextday2",strtotime('+2 days',$time));
$template->assign("nextday3",strtotime('+3 days',$time));

$template->output();

?>
