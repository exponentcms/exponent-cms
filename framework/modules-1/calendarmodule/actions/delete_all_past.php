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

//$item = $db->selectObject('calendar','id='.intval($_POST['id']));

$dates = $db->selectObjects("eventdate",$locsql." AND date < ".strtotime('-1 months',time()));
$all_events = calendarmodule::_getEventsForDates($dates);


if ($item && $item->is_recurring == 1) {
	$eventdates = $db->selectObjectsIndexedArray('eventdate','event_id='.$item->id);
	foreach (array_keys($_POST['dates']) as $d) {
		if (isset($eventdates[$d])) {
			$db->delete('eventdate','id='.$d);
			unset($eventdates[$d]);
		}
	}
	
	if (!count($eventdates)) {
		$db->delete('calendar','id='.$item->id);
//		$db->delete("calendar_wf_info","real_id=".$_GET['id']);
//		$db->delete("calendar_revision","wf_original=".$_GET['id']);
		//Delete search entries
		$db->delete('search',"ref_module='calendarmodule' AND ref_type='calendar' AND original_id=".$item->id);
	}

	expHistory::back();
} else {
	echo SITE_404_HTML;
}

?>
