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

$item = $db->selectObject("calendar","id=" . intval($_GET['id']));
if ($item) {
	$loc = unserialize($item->location_data);
	$iloc = exponent_core_makeLocation($loc->mod,$loc->src,$item->id);
	$item->permissions = array(
		"edit"=>(exponent_permissions_check("edit",$iloc) || exponent_permissions_check("edit",$loc)),
		"delete"=>(exponent_permissions_check("delete",$iloc) || exponent_permissions_check("delete",$loc)),
		"administrate"=>(exponent_permissions_check("administrate",$iloc) || exponent_permissions_check("administrate",$loc)),
	);
	// Debugger test
	$item->permissions = array(
		"edit"=>exponent_permissions_check("edit",$iloc),
		"delete"=>exponent_permissions_check("delete",$iloc),
		"administrate"=>exponent_permissions_check("administrate",$iloc)
	);

	$eventdate = $db->selectObject("eventdate","id=".intval($_GET['date_id']));
	$item->eventdate = $eventdate;
	
	//FJD - Goofy-ass daylight savings time hack.  Should be improved at some point.
	//need to do some comparisons on the timestamp and value returned from strftime and adjust accordingly up or down 
	//to correct output.  This will still cause one display bug: if your times are within an hour of the change in one
	//direction, it will display incorrectly.  
	//US does the switch at 2AM, European union at 1AM.
	
	//get interger for hours from eventstart and end divided by 3600, then
	//get interger for hour of time returned from strtime, which should take DST from locale into consideration, 
	//(so our data should be portable).  If they are off, then create the adjustment +/- and correct
	//eventstart and eventend
	$timeHourStart =  intval($item->eventstart / 3600);
	$strHourStart = intval(strftime("%H", $eventdate->date + $item->eventstart));	
	$timeHourEnd =  intval($item->eventend / 3600);
	$strHourEnd = intval(strftime("%H", $eventdate->date + $item->eventend));
	
	$adjustStart = (($timeHourStart - $strHourStart) * 3600); //could be + or - or 0 (most of the time);
	$adjustEnd = (($timeHourEnd - $strHourEnd) * 3600); //could be + or - or 0 (most of the time);
	
	$item->eventstart += ($eventdate->date + $adjustStart); 
	$item->eventend += ($eventdate->date + $adjustEnd); 
		
	$template = new template("calendarmodule","_view",$loc);

	if ($item->feedback_form != "") {
		$formtemplate = new formtemplate('forms/email', $item->feedback_form);
		$formtemplate->assign('formname', $item->feedback_form);
		$formtemplate->assign('module','calendarmodule');
		$formtemplate->assign('loc', $loc);
		$formtemplate->assign('action', 'send_feedback');
		$formtemplate->assign('id', $item->id);
		$form = $formtemplate->render();
		$template->assign('form', $form);
	}

	$template->assign("item",$item);
	$template->assign("directory","files/calendarmodule/".$loc->src);
	$template->register_permissions(
		array("post","edit","delete","administrate","manage_approval"),
		$loc
	);

	$template->output();
} else {
	echo SITE_404_HTML;
}

?>
