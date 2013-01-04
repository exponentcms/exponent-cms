<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
//$iloc = new stdClass();

if (isset($_POST['id']) && !isset($_POST['submitNew'])) {
	$item = $db->selectObject("calendar","id=".intval($_POST['id']));
	$loc = unserialize($item->location_data);
//	$iloc = expCore::makeLocation($loc->mod,$loc->src,$item->id);
}

if (($item == null && expPermissions::check("create",$loc)) ||
	($item != null && expPermissions::check("edit",$loc))
//    || ($iloc != null && expPermissions::check("edit",$iloc))
) {

	$item = calendar::update($_POST,$item);
	$item->location_data = serialize($loc);

	//Check to see if the feedback form is enabled and/or being used for this event.
	if (isset($_POST['feedback_form'])) {
		$item->feedback_form = $_POST['feedback_form'];
		$item->feedback_email = $_POST['feedback_email'];
	} else {
		$item->feedback_form = "";
		$item->feedback_email = "";
	}

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
			$eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$_POST));
			$db->updateObject($eventdate,'eventdate');
		} else {
			$item->approved = 1;
			$db->updateObject($item,"calendar");
			// There should be only one eventdate
			$eventdate = $db->selectObject('eventdate','event_id = '.$item->id);

			$eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$_POST));
			$db->updateObject($eventdate,'eventdate');
		}
	} else {
		$start_recur = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$_POST));
		$stop_recur  = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("untildate",$_POST));

		if (($_POST['recur'] != "recur_none") && isset($_POST['recur'])) {
			// Do recurrence
			$freq = $_POST['recur_freq_'.$_POST['recur']];
			switch ($_POST['recur']) {
				case "recur_daily":
					$dates = expDateTime::recurringDailyDates($start_recur,$stop_recur,$freq);
					break;
				case "recur_weekly":
                    $dateinfo = getdate($start_recur);  //FIXME hack in case the day of week wasn't checked off
					$dates = expDateTime::recurringWeeklyDates($start_recur,$stop_recur,$freq,(isset($_POST['day']) ? array_keys($_POST['day']) : array($dateinfo['wday'])));
					break;
				case "recur_monthly":
					$dates = expDateTime::recurringMonthlyDates($start_recur,$stop_recur,$freq,(!empty($_POST['month_type'])?$_POST['month_type']:true));
					break;
				case "recur_yearly":
					$dates = expDateTime::recurringYearlyDates($start_recur,$stop_recur,$freq);
					break;
				default:
//					$dates = array();
					echo "Bad type: " . $_POST['recur'] . "<br />";
					return;
					break;
			}

			$item->is_recurring = 1; // Set the recurrence flag.
		} else {
			$dates = array($start_recur);
		}

		$item->approved = 1; // Bypass workflow.

		$edate = new stdClass();
		$item->id = $db->insertObject($item,"calendar");
		$edate->event_id = $item->id;
		$edate->location_data = $item->location_data;
		foreach ($dates as $d) {
			$edate->date = $d;
			$db->insertObject($edate,"eventdate");
		}
		$db->insertObject($item,"calendar");
	}
    calendarmodule::spiderContent($item);

	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
