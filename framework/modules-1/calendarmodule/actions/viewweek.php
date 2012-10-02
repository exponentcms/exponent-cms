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
/** @define "BASE" "../../../.." */

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

$template = new template("calendarmodule","_viewweek",$loc,false);

$time = intval(isset($_GET['time']) ? $_GET['time'] : time());

$days = array();
$counts = array();
$startperiod = expDateTime::startOfWeekTimestamp($time);
$startinfo = getdate($startperiod);

//FIXME add external events to $days[$start][] for date $start, one day at a time
$extitems = calendarmodule::getExternalEvents($loc,$startperiod,expDateTime::endOfWeekTimestamp($startperiod));
for ($i = 0; $i < 7; $i++) {
	$start = mktime(0,0,0,$startinfo['mon'],$startinfo['mday']+$i,$startinfo['year']);
//	$dates = $db->selectObjects("eventdate","location_data='".serialize($loc)."' AND date = $start");
	$dates = $db->selectObjects("eventdate",$locsql." AND (date >= ".expDateTime::startOfDayTimestamp($start)." AND date <= ".expDateTime::endOfDayTimestamp($start).")");
    //FIXME isn't it better to use calendarmodule::_getEventsForDates less permissions
    $days[$start] = array();
	for ($j = 0; $j < count($dates); $j++) {
		$o = $db->selectObject("calendar","id=".$dates[$j]->event_id);
		if ($o != null) {
			$o->eventdate = $dates[$j];
			$o->eventstart += $o->eventdate->date;
			$o->eventend += $o->eventdate->date;
//			$thisloc = expCore::makeLocation($loc->mod,$loc->src,$o->id);
//			$o->permissions = array(
//				"manage"=>(expPermissions::check("manage",$thisloc) || expPermissions::check("manage",$loc)),
//				"edit"=>(expPermissions::check("edit",$thisloc) || expPermissions::check("edit",$loc)),
//				"delete"=>(expPermissions::check("delete",$thisloc) || expPermissions::check("delete",$loc))
//			);
			$days[$start][] = $o;
		}
	}
    if (!empty($extitems[$start])) $days[$start] = array_merge($extitems[$start],$days[$start]);
    $days[$start] = expSorter::sort(array('array'=>$days[$start],'sortby'=>'eventstart', 'order'=>'ASC'));
	$counts[$start] = count($days[$start]);
}

$template->register_permissions(
	array("create","edit","delete","manage"),
	$loc
);
$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
$template->assign('moduletitle',$title);
$template->assign("config",$config);

$template->assign("days",$days);
$template->assign("counts",$counts);
$template->assign("time",$startperiod);
$template->assign("startprevweek3",(strtotime('-3 weeks',$startperiod)));
$template->assign("startprevweek2",(strtotime('-2 weeks',$startperiod)));
$template->assign("startprevweek",(strtotime('-1 weeks',$startperiod)));
$template->assign("startnextweek",(strtotime('+1 weeks',$startperiod)));
$template->assign("startnextweek2",(strtotime('+2 weeks',$startperiod)));
$template->assign("startnextweek3",(strtotime('+3 weeks',$startperiod)));

$template->output();

?>
