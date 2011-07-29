<?php 
##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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
/** @define "BASE" ".." */

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','index.php');  // we need to force the links to build correctly
// Initialize the Exponent Framework
require_once('../exponent.php');
global $user;
global $db;
$i18n = exponent_lang_loadFile('modules/calendarmodule/class.php');

// let's select a calendar by its source to make it easier to find and harder to spoof	
$src = $_GET['src'];
if (!$src) {
	print_r("<br><b><i>Exponent - No Calendar Selected!</i></b><br>");	
	exit();
}	

$loc = null;
$loc->mod = 'calendarmodule';
$loc->src = $src;
$loc->int = '';	
$config = $db->selectObject("calendarmodule_config","location_data='".serialize($loc)."'");
if (!$config) {
	print_r("<br><b><i>Exponent - Calendar Not Found!</i></b><br>");	
	exit();
}

$loc = unserialize($config->location_data);
$locsql = "(location_data='".serialize($loc)."'";
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

$view = $_GET['view'];
if ($view == "") {
	$view = "_reminder";	// default reminder view
}

$template = new template('calendarmodule',$view,$loc);
if ($title == '') {
	$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
}
$template->assign('moduletitle',$title);

// $canviewapproval = false;
// $inapproval = false;
// if ($user) $canviewapproval = (exponent_permissions_check("approve",$loc) || exponent_permissions_check("manage_approval",$loc));
// if ($db->countObjects("calendar","location_data='".serialize($loc)."' AND approved!=1")) {
	// foreach ($db->selectObjects("calendar","location_data='".serialize($loc)."' AND approved!=1") as $c) {
		// if ($c->poster == $user->id) $canviewapproval = true;
	// }
	// $inapproval = true;
// }

$time = (isset($_GET['time']) ? $_GET['time'] : time());
$time = intval($time);

$template->assign("time",$time);

// $viewparams = $template->viewparams;
// if ($viewparams === null) {
	// $viewparams = array("type"=>"byday", "range"=>"week");
// }

if (!defined("SYS_DATETIME")) include_once(BASE . "subsystems/datetime.php");
if (!defined('SYS_SORTING')) include_once(BASE . 'subsystems/sorting.php');

if (!function_exists("exponent_sorting_byEventStartAscending")) {
	function exponent_sorting_byEventStartAscending($a,$b) {
		return ($a->eventstart < $b->eventstart ? -1 : 1);
	}
}



// if ($viewparams['type'] == "minical") {
	// $monthly = exponent_datetime_monthlyDaysTimestamp($time);
	// $info = getdate($time);
	// $timefirst = mktime(12,0,0,$info['mon'],1,$info['year']);
	// $now = getdate(time());
	// $currentday = $now['mday'];
	// $endofmonth = date('t', $time);
	// foreach ($monthly as $weekNum=>$week) {
		// foreach ($week as $dayNum=>$day) {
			// if ($dayNum == $now['mday']) {
				// $currentweek = $weekNum;
			// }
			// if ($dayNum <= $endofmonth) {
				// $monthly[$weekNum][$dayNum]['number'] = ($monthly[$weekNum][$dayNum]['ts'] != -1) ? $db->countObjects("eventdate",$locsql." AND date = ".$day['ts']) : -1;
			// }
		// }
	// }
// //eDebug($monthly);			
	// $template->assign("monthly",$monthly);
	// $template->assign("currentweek",$currentweek);
	// $template->assign("currentday",$currentday);
	// $template->assign("now",$timefirst);
	// $prevmonth = mktime(0, 0, 0, date("m",$timefirst)-1, date("d",$timefirst)+10,   date("Y",$timefirst));
	// $nextmonth = mktime(0, 0, 0, date("m",$timefirst)+1, date("d",$timefirst)+10,   date("Y",$timefirst));
	// $template->assign("prevmonth",$prevmonth);
	// $template->assign("thismonth",$timefirst);
	// $template->assign("nextmonth",$nextmonth);
	
	
	
// } else if ($viewparams['type'] == "byday") {
// Remember this is the code for weekly view and monthly listview
// Test your fixes on both views before submitting your changes to cvs
$startperiod = exponent_datetime_startOfDayTimestamp($time);
$totaldays = $_GET['days'];
if ($totaldays == "") {
	$totaldays = 7;	// default 7 days of events
}

//	if ($viewparams['range'] == "week") {
//		$startperiod = exponent_datetime_startOfWeekTimestamp($time);
//		$totaldays = 7;
//	} else if ($viewparams['range'] == "twoweek") {
//		$startperiod = exponent_datetime_startOfWeekTimestamp($time);
//		$totaldays = 14;				
//	} else {  // range = month
//		$startperiod = exponent_datetime_startOfMonthTimestamp($time);
//		$totaldays  = date('t', $time);
//	}
//	$template->assign("prev_timestamp",$startperiod - 3600);
//	$template->assign("next_timestamp",$startperiod+(($totaldays * 86400) + 3600));

//	$days = array();
$count = 0;
$info = getdate($startperiod);
for ($i = 0; $i < $totaldays; $i++) {
	$start = mktime(0,0,0,$info['mon'],$info['mday']+$i,$info['year']);
	// if ( $viewparams['range'] == "week" ) {
		// $start = mktime(0,0,0,$info['mon'],$info['mday']+$i,$info['year']);
	// } else if ( $viewparams['range'] == "twoweek" ) {
		// $start = mktime(0,0,0,$info['mon'],$info['mday']+$i,$info['year']);
// //          $start = $startperiod + ($i*86400);
	// } else {  // range = month
		// $start = mktime(0,0,0,$info['mon'],$i,$info['year']);
	// }
	//$edates = $db->selectObjects("eventdate",$locsql." AND date = '".$start."'");
	$edates = $db->selectObjects("eventdate",$locsql." AND date = $start");	
	$days[$start] = array();			
	$days[$start] = calendarmodule::_getEventsForDates($edates);
	for ($j = 0; $j < count($days[$start]); $j++) {
		$thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$days[$start][$j]->id);
		$days[$start][$j]->permissions = array(
			"administrate"=>(exponent_permissions_check("administrate",$thisloc) || exponent_permissions_check("administrate",$loc)),
			"edit"=>(exponent_permissions_check("edit",$thisloc) || exponent_permissions_check("edit",$loc)),
			"delete"=>(exponent_permissions_check("delete",$thisloc) || exponent_permissions_check("delete",$loc))
		);
	}
	$counts[$start] = count($days[$start]);
	$count += count($days[$start]);
	usort($days[$start],"exponent_sorting_byEventStartAscending");
}
$template->assign("days",$days);
$template->assign("counts",$counts);
$template->assign("start",$startperiod);
$template->assign("totaldays",$totaldays);
	
	
	
// } else if ($viewparams['type'] == "monthly") {
	// $monthly = array();
	// $counts = array();
	// $info = getdate($time);
	// $nowinfo = getdate(time());
	// if ($info['mon'] != $nowinfo['mon']) $nowinfo['mday'] = -10;
	// // Grab non-day numbers only (before end of month)
	// $week = 0;
	// $currentweek = -1;
	// $timefirst = mktime(12,0,0,$info['mon'],1,$info['year']);
	// $infofirst = getdate($timefirst);
	// $monthly[$week] = array(); // initialize for non days
	// $counts[$week] = array();
	// if ( ($infofirst['wday'] == 0) && (DISPLAY_START_OF_WEEK == 1) ) {
		// for ($i = -6; $i < (1-DISPLAY_START_OF_WEEK); $i++) {
			// $monthly[$week][$i] = array();
			// $counts[$week][$i] = -1;
		// }
		// $weekday = $infofirst['wday']+7; // day number in grid.  if 7+, switch weeks
	// } else {
		// for ($i = 1 - $infofirst['wday']; $i < (1-DISPLAY_START_OF_WEEK); $i++) {
			// $monthly[$week][$i] = array();
			// $counts[$week][$i] = -1;
		// }
		// $weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
	// }
	// // Grab day counts (deprecated, handled by the date function)
	// // $endofmonth = exponent_datetime_endOfMonthDay($time);
	// $endofmonth = date('t', $time);
	// for ($i = 1; $i <= $endofmonth; $i++) {
		// $start = mktime(0,0,0,$info['mon'],$i,$info['year']);
		// if ($i == $nowinfo['mday']) $currentweek = $week;
		// #$monthly[$week][$i] = $db->selectObjects("calendar","location_data='".serialize($loc)."' AND (eventstart >= $start AND eventend <= " . ($start+86399) . ") AND approved!=0");
		// //$dates = $db->selectObjects("eventdate",$locsql." AND date = $start");
		// $dates = $db->selectObjects("eventdate",$locsql." AND date = '".$start."'");
		// $monthly[$week][$i] = calendarmodule::_getEventsForDates($dates);
		// $counts[$week][$i] = count($monthly[$week][$i]);
		// if ($weekday >= (6+DISPLAY_START_OF_WEEK)) {
			// $week++;
			// $monthly[$week] = array(); // allocate an array for the next week
			// $counts[$week] = array();
			// $weekday = DISPLAY_START_OF_WEEK;
		// } else $weekday++;
	// }
	// // Grab non-day numbers only (after end of month)
	// for ($i = 1; $weekday && $i < (8+DISPLAY_START_OF_WEEK-$weekday); $i++) {
		// $monthly[$week][$i+$endofmonth] = array();
		// $counts[$week][$i+$endofmonth] = -1;
	// }
// //eDebug($monthly);			
	// $template->assign("currentweek",$currentweek);
	// $template->assign("monthly",$monthly);
	// $template->assign("counts",$counts);
	// $template->assign("nextmonth",$timefirst+(86400*45));
	// $template->assign("prevmonth",$timefirst-(86400*15));
	// $template->assign("now",$timefirst);
	// $template->assign("today",strtotime('today')-39600);
// } else if ($viewparams['type'] == "administration") {
	// // Check perms and return if cant view
	// if ($viewparams['type'] == "administration" && !$user) return;
	// $continue = (
		// exponent_permissions_check("administrate",$loc) ||
		// exponent_permissions_check("post",$loc) ||
		// exponent_permissions_check("edit",$loc) ||
		// exponent_permissions_check("delete",$loc) ||
		// exponent_permissions_check("approve",$loc) ||
		// exponent_permissions_check("manage_approval",$loc)
		// ) ? 1 : 0;
	// $dates = $db->selectObjects("eventdate",$locsql);
	// $items = calendarmodule::_getEventsForDates($dates);
	// if (!$continue) {
		// foreach ($items as $i) {
			// $iloc = exponent_core_makeLocation($loc->mod,$loc->src,$i->id);
			// if (exponent_permissions_check("edit",$iloc) ||
				// exponent_permissions_check("delete",$iloc) ||
				// exponent_permissions_check("administrate",$iloc)
			// ) {
				// $continue = true;
			// }
		// }
	// }
	// if (!$continue) return;
	// for ($i = 0; $i < count($items); $i++) {
		// $thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$items[$i]->id);
		// if ($user && $items[$i]->poster == $user->id) $canviewapproval = 1;
		// $items[$i]->permissions = array(
			// "administrate"=>(exponent_permissions_check("administrate",$thisloc) || exponent_permissions_check("administrate",$loc)),
			// "edit"=>(exponent_permissions_check("edit",$thisloc) || exponent_permissions_check("edit",$loc)),
			// "delete"=>(exponent_permissions_check("delete",$thisloc) || exponent_permissions_check("delete",$loc))
		// );
	// }
	// usort($items,"exponent_sorting_byEventStartAscending");
	// $template->assign("items",$items);
// } else if ($viewparams['type'] == "default") {
	// if (!isset($viewparams['range'])) $viewparams['range'] = "all";
	// $items = null;
	// $dates = null;
	// $day = exponent_datetime_startOfDayTimestamp(time());
	// $sort_asc = true; // For the getEventsForDates call
	// $moreevents = false;
	// switch ($viewparams['range']) {
		// case "all":
			// $dates = $db->selectObjects("eventdate",$locsql);
			// break;
		// case "upcoming":
			// $dates = $db->selectObjects("eventdate",$locsql." AND date >= $day ORDER BY date ASC ");
// //			$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date >= $day");					
			// break;
		// case "past":
			// $dates = $db->selectObjects("eventdate",$locsql." AND date < $day ORDER BY date DESC ");
// //			$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date < $day");					
			// $sort_asc = false;
			// break;
		// case "today":
			// $dates = $db->selectObjects("eventdate",$locsql." AND date = $day");
			// break;
		// case "next":
			// $dates = array($db->selectObject("eventdate",$locsql." AND date >= $day"));
			// break;
		// case "month":
			// $dates = $db->selectObjects("eventdate",$locsql." AND date >= ".exponent_datetime_startOfMonthTimestamp(time()) . " AND date <= " . exponent_datetime_endOfMonthTimestamp(time()));
			// break;
	// }
	// $items = calendarmodule::_getEventsForDates($dates,$sort_asc,$template->viewconfig['featured_only'] ? true : false);
// // Upcoming events can be configured to show a specific number of events.
// // The previous call gets all events in the future from today
// // If configured, cut the array to the configured number of events
// //			if ($template->viewconfig['num_events']) {
// //				switch ($viewparams['range']) {
// //					case "upcoming":
// //					case "past":
// //						$moreevents = $template->viewconfig['num_events'] < count($items);	
// //						break;
// //				}
// //				$items = array_slice($items, 0, $template->viewconfig['num_events']);
// //eDebug($items);
// //			}			
	// for ($i = 0; $i < count($items); $i++) {
		// $thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$items[$i]->id);
		// if ($user && $items[$i]->poster == $user->id) $canviewapproval = 1;
		// $items[$i]->permissions = array(
			// 'administrate'=>(exponent_permissions_check('administrate',$thisloc) || exponent_permissions_check('administrate',$loc)),
			// 'edit'=>(exponent_permissions_check('edit',$thisloc) || exponent_permissions_check('edit',$loc)),
			// 'delete'=>(exponent_permissions_check('delete',$thisloc) || exponent_permissions_check('delete',$loc))
		// );
	// }
	// //Get the image file if there is one.
	// for ($i = 0; $i < count($items); $i++) {
		// if (isset($items[$i]->file_id) && $items[$i]->file_id > 0) {
			// $file = $db->selectObject('file', 'id='.$items[$i]->file_id);
			// $items[$i]->image_path = $file->directory.'/'.$file->filename;
		// }
	// }
// //eDebug($items);
	// $template->assign('items',$items);
	// $template->assign('moreevents',$moreevents);
// }


if ($count == 0) {
	print_r("<br><b><i>Exponent - No Events to Send!</i></b><br>");	
	exit();
}

// $template->assign('in_approval',$inapproval);
// $template->assign('canview_approval_link',$canviewapproval);
// $template->register_permissions(
	// array('administrate','configure','post','edit','delete','manage_approval','manage_categories'),
	// $loc
// );

//$cats = $db->selectObjectsIndexedArray("category","location_data='".serialize($loc)."'");
$cats = $db->selectObjectsIndexedArray("category");
$cats[0] = null;
$cats[0]->name = '<i>'.$i18n['no_category'].'</i>';
$cats[0]->color = "#000000";
$template->assign("categories",$cats);

$template->assign("config",$config);

//Get the tags that have been selected to be shown in the grouped by tag views
// if (isset($config->show_tags)) {
	// $available_tags = unserialize($config->show_tags);
// } else {
	// $available_tags = array();
// }

// if (isset($items) && is_array($items)) {
	// for ($i = 0; $i < count($items); $i++) {
	// //Get the tags for this calendar event
		// $selected_tags = array();
		// $tag_ids = unserialize($items[$i]->tags);
		// if(!empty($tag_ids)) {$selected_tags = $db->selectObjectsInArray('tags', $tag_ids, 'name');}
		// $items[$i]->tags = $selected_tags;

	// //If this module was configured to group the newsitems by tags, then we need to change the data array a bit
		// if (isset($config->group_by_tags) && $config->group_by_tags == true) {
			// $grouped_news = array();
			// foreach($items[$i]->tags as $tag) {
				// if (in_array($tag->id, $available_tags) || count($available_tags) == 0) {
					// if (!isset($grouped_news[$tag->name])) { $grouped_news[$tag->name] = array();}
					// array_push($grouped_news[$tag->name],$items[$i]);
				// }
			// }
		// }
	// }
// }


// format and send email

//$subject = $config->email_title_thread;
$subject = $config->email_title_reminder." - $title";
$from_addr = $config->email_address_reminder;
$headers = array(
	"From"=>$from = $config->email_from_reminder,
	"Reply-to"=>$reply = $config->email_reply_reminder
	);
if (!defined("SYS_USERS")) require_once(BASE . "subsystems/users.php");

// set up the html message
$template->assign("showdetail",$config->email_showdetail);
$htmlmsg = $template->render();

// now the same thing for the text message
$msg = chop(strip_tags(str_replace(array("<br />","<br>","br/>"),"\n",$htmlmsg)));

// Saved.  do notifs
$notifs = unserialize($config->reminder_notify);

$emails = array();
//foreach ($notifs as $n) {
//	if ($n->user_id != $user->id) {
//		$u = exponent_users_getUserById($n);
//		if ($u->email != "" && !in_array($u->email,$emails)) $emails[] = $u->email;
//	}
//}
foreach ($db->selectObjects('calendar_reminder_address',"calendar_id='".$config->id."'") as $c) {
	if ($c->user_id != 0) {
		$u = exponent_users_getUserById($c->user_id);
		$emails[] = $u->email;
	} else if ($c->group_id != 0) {
		$grpusers = exponent_users_getUsersInGroup($c->group_id);
		foreach ($grpusers as $u) {
			$emails[] = $u->email;
		}
	} else if ($c->email != '') {
		$emails[] = $c->email;
	}
}

if (empty($emails)) {
	print_r("<br><b><i>Exponent - No One to Send Reminders to!</i></b><br>");	
	exit();
}

$emails = array_flip(array_flip($emails));
$emails = array_map('trim', $emails);

// old mail method
//require_once(BASE . "subsystems/mail.php");
//$mail = new exponentMail();
//$mail->subject($subject);
//$mail->addText($msg);
//$mail->addHTML($htmlmsg);
//$mail->addFrom($config->email_address_reminder,$config->email_from_reminder);
//foreach($emails as $recip) {	// to keep other recepients hidden
//	try {
//		$mail->addTo($recip);
//		$mail->send();
//	} catch (Exception $e) {
//	}
//	$mail->flushRecipients();
//}

// new mail method
$headers = array(
	"MIME-Version"=>"1.0",
	"Content-type"=>"text/html; charset=".LANG_CHARSET
);
//foreach ($emails as $recip) {
	$mail = new expMail();
	$mail->quickSend(array(
			'headers'=>$headers,
			'html_message'=>$htmlmsg,
			"text_message"=>$msg,
//			'to'=>trim($recip),
			'to'=>trim($emails),
			'from'=>trim($config->email_address_reminder),
			'from_name'=>$config->email_from_reminder,
			'subject'=>$subject,
	));
//}

print_r("<p>The following reminder was sent via email:</p><br>");	
print_r($htmlmsg);

?>
