<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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
 
if (!defined("EXPONENT")) exit("");

exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);

$time = (isset($_GET['time']) ? $_GET['time'] : time());
$info = getdate(intval($time));
$start = mktime(0,0,0,$info['mon'],$info['mday'],$info['year']);

$template = new template("calendarmodule","_viewday",$loc,false,$loc);

$dates = $db->selectObjects("eventdate","location_data='".serialize($loc)."' AND date = '" . $start . "'");
$events = array();
foreach ($dates as $d) {
	$o = $db->selectObject("calendar","id=".$d->event_id);
	$o->eventstart += $d->date;
	$o->eventend += $d->date;
	$o->eventdate = $d;
	$thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$o->id);
	$o->permissions = array(
		"administrate"=>(exponent_permissions_check("administrate",$thisloc) || exponent_permissions_check("administrate",$loc)),
		"edit"=>(exponent_permissions_check("edit",$thisloc) || exponent_permissions_check("edit",$loc)),
		"delete"=>(exponent_permissions_check("delete",$thisloc) || exponent_permissions_check("delete",$loc))
	);
	$events[] = $o;
}

$template->register_permissions(
	array("manage_approval"),
	$loc);

$template->assign("events",$events);
$template->assign("now",$time);
$template->assign("nextday",$time+86400);
$template->assign("prevday",$time-86400);

$template->output();

?>
