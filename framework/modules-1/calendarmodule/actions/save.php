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

$item = null;
$iloc = null;

if (isset($_POST['id']) && !isset($_POST['submitNew'])) {
	$item = $db->selectObject("calendar","id=".intval($_POST['id']));
	$loc = unserialize($item->location_data);
	$iloc = expCore::makeLocation($loc->mod,$loc->src,$item->id);
}

if (($item == null && expPermissions::check("post",$loc)) ||
	($item != null && expPermissions::check("edit",$loc)) ||
	($iloc != null && expPermissions::check("edit",$iloc))
) {

	$item = calendar::update($_POST,$item);
	$item->location_data = serialize($loc);

	// if (isset($_POST['category'])) $item->category_id = $_POST['category'];
	// else $item->category_id = 0;

	//Get and add the tags selected by the user
    // $item->tags = serialize(listbuildercontrol::parseData($_POST,'tags'));

	//Check to see if the feedback form is enabled and/or being used for this event.
	if (isset($_POST['feedback_form'])) {
		$item->feedback_form = $_POST['feedback_form'];
		$item->feedback_email = $_POST['feedback_email'];
	} else {
		$item->feedback_form = "";
		$item->feedback_email = "";
	}

	//Get and save the image
	/*  Yeah, no. Yeah, yes... Maia 6/23/09 */
	// $file = null;
	// if ($_FILES['file']['name'] != '') {
		// $dir = 'files/calendarmodule/'.$loc->src;
		// $file = file::update('file',$dir,null,time().'_'.$_FILES['file']['name']);
		// if (is_object($file)) {
			// $item->file_id = $db->insertObject($file,'file');
		// } else {
			// // If file::update() returns a non-object, it should be a string.  That string is the error message.
			// $post = $_POST;
			// $post['_formError'] = $file;
			// expSession::set('last_POST',$post);
			// header('Location: ' . $_SERVER['HTTP_REFERER']);
		// }
    // }

	if (isset($item->id)) {
		if ($item->is_recurring == 1) {
			// For recurring events, check some stuff.
			// Were all dates selected?
			$eventdates = $db->selectObjectsIndexedArray("eventdate","event_id=".$item->id);
			if (count($_POST['dates']) == count($eventdates)) {
				// yes.  just update the original
				$db->updateObject($item,"calendar");
				// If the date has changed, modify the current date_id
			} else {
				// No, create new and relink affected dates
				$olditem = $db->selectObject("calendar","id=".$item->id);
				unset($item->id);
				if (count($_POST['dates']) == 1) {
					$item->is_recurring = 0; // Back to a single event.
				}

				$item->id = $db->insertObject($item,"calendar");

				foreach (array_keys($_POST['dates']) as $date_id) {
					if (isset($eventdates[$date_id])) {
						$eventdates[$date_id]->event_id = $item->id;
						$db->updateObject($eventdates[$date_id],"eventdate");
					}
				}
			}
			$eventdate = $db->selectObject('eventdate','id='.intval($_POST['date_id']));
			//$eventdate->date = expDateTime::startOfDayTimestamp(popupdatetimecontrol::parseData("eventdate",$_POST));
			$eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$_POST));
			$db->updateObject($eventdate,'eventdate');
		} else {
			$item->approved = 1;
			$db->updateObject($item,"calendar");
			// There should be only one eventdate
			$eventdate = $db->selectObject('eventdate','event_id = '.$item->id);

			//$eventdate->date = expDateTime::startOfDayTimestamp(popupdatetimecontrol::parseData("eventdate",$_POST));
			$eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$_POST));
			$db->updateObject($eventdate,'eventdate');
		}
//		calendarmodule::spiderContent($item);
	} else {
		//$start_recur = expDateTime::startOfDayTimestamp(popupdatetimecontrol::parseData("eventdate",$_POST));
		$start_recur = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$_POST));
		//$stop_recur  = expDateTime::startOfDayTimestamp(popupdatetimecontrol::parseData("untildate",$_POST));
		$stop_recur  = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("untildate",$_POST));

		if (($_POST['recur'] != "recur_none") && isset($_POST['recur'])) {
			// Do recurrence
			$freq = $_POST['recur_freq_'.$_POST['recur']];

			###echo $_POST['recur'] . "<br />";

			switch ($_POST['recur']) {
				case "recur_daily":
					$dates = expDateTime::recurringDailyDates($start_recur,$stop_recur,$freq);
					break;
				case "recur_weekly":
					$dates = expDateTime::recurringWeeklyDates($start_recur,$stop_recur,$freq,(isset($_POST['day']) ? array_keys($_POST['day']) : array($dateinfo['wday'])));
					break;
				case "recur_monthly":
					$dates = expDateTime::recurringMonthlyDates($start_recur,$stop_recur,$freq,$_POST['month_type']);
					break;
				case "recur_yearly":
					$dates = expDateTime::recurringYearlyDates($start_recur,$stop_recur,$freq);
					break;
				default:
					$dates = array();
					echo "Bad type: " . $_POST['recur'] . "<br />";
					return;
					break;
			}

			$item->is_recurring = 1; // Set the recurrence flag.
		} else {
			$dates = array($start_recur);
		}

		$item->approved = 1; // Bypass workflow.

		$edate = null;
		$item->id = $db->insertObject($item,"calendar");
		$edate->event_id = $item->id;
		$edate->location_data = $item->location_data;
		foreach ($dates as $d) {
			$edate->date = $d;
			$db->insertObject($edate,"eventdate");
		}
		$db->insertObject($item,"calendar");
//		calendarmodule::spiderContent($item);
	}

//	exponent_workflow_post($item,'calendar',$loc);
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
