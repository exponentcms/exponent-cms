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
/** @define "BASE" "../.." */

class calendarmodule {
	function name() { return exponent_lang_loadKey('modules/calendarmodule/class.php','module_name'); }
	function author() { return 'OIC Group, Inc'; }
	function description() { return exponent_lang_loadKey('modules/calendarmodule/class.php','module_description'); }

	function hasContent() { return true; }
	function hasSources() { return true; }
	function hasViews()   { return true; }

	function supportsWorkflow() { return true; }

	function getRSSContent($loc) {
		global $db;

		//Get this modules configuration data
		$config = $db->selectObject('calendarmodule_config',"location_data='".serialize($loc)."'");
	
		//If this module was configured as an aggregator, then turn off check for the location_data
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
							
		if (!function_exists("exponent_datetime_startOfDayTimestamp")) {
			if (!defined("SYS_DATETIME")) include_once(BASE."subsystems/datetime.php");               
		}
		$day = exponent_datetime_startOfDayTimestamp(time());
		
		if ($config->rss_limit > 0) {
			$rsslimit = " AND date <= " . ($day + ($config->rss_limit * 86400));
		} else {
			$rsslimit = "";
		}
		
		$cats = $db->selectObjectsIndexedArray("category");
		$cats[0] = null;
		$cats[0]->name = 'None';
		
		//Get this modules items
		$items = array();
		$dates = null;
		$sort_asc = true; // For the getEventsForDates call 
		$dates = $db->selectObjects("eventdate", $locsql." AND date >= ".$day.$rsslimit." ORDER BY date ASC ");                   
		$items = calendarmodule::_getEventsForDates($dates, $sort_asc);           

		//Convert the events to rss items
		$rssitems = array();
		foreach ($items as $key => $item) {
			$rss_item = new FeedItem();
			$rss_item->title = $item->title;
			$rss_item->description = $item->body;
			$rss_item->date = date('r', $item->eventstart);
//          $rss_item->date = date('r', $item->posted);
//			$rss_item->link = "http://".HOSTNAME.PATH_RELATIVE."index.php?module=calendarmodule&action=view&id=".$item->id."&src=".$loc->src;
			$rss_item->link = exponent_core_makeLink(array('module'=>'calendarmodule', 'action'=>'view', 'id'=>$item->id, 'date_id'=>$item->eventdate->id));
			if ($config->enable_categories == 1) {
				$rss_item->category = array($cats[$item->category_id]->name);
			}
			$rssitems[$key] = $rss_item;
		}
		return $rssitems;
	}

	function copyContent($oloc,$nloc) {

	}

	function permissions($internal = '') {
		$i18n = exponent_lang_loadFile('modules/calendarmodule/class.php');

		if ($internal == '') {
			return array(
				'administrate'=>$i18n['perm_administrate'],
				'configure'=>$i18n['perm_configure'],
				'post'=>$i18n['perm_post'],
				'edit'=>$i18n['perm_edit'],
				'delete'=>$i18n['perm_delete'],
				'approve'=>$i18n['perm_approve'],
				'manage_approval'=>$i18n['perm_manage_approval'],
				'manage_categories'=>$i18n['perm_manage_categories']
			);
		} else {
			return array(
				'administrate'=>$i18n['perm_administrate'],
				'edit'=>$i18n['perm_edit'],
				'delete'=>$i18n['perm_delete']
			);
		}
	}

	function getLocationHierarchy($loc) {
		if ($loc->int == '') return array($loc);
		else return array($loc,exponent_core_makelocation($loc->mod,$loc->src));
	}

	function show($view,$loc = null, $title = '') {
		global $user;
		global $db;

		$i18n = exponent_lang_loadFile('modules/calendarmodule/class.php');

		$locsql = "(location_data='".serialize($loc)."'";
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

		$template = new template('calendarmodule',$view,$loc);
		if ($title == '') {
			$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
		}
		$template->assign('moduletitle',$title);

		$canviewapproval = false;
		$inapproval = false;

		global $user;
		if ($user) $canviewapproval = (exponent_permissions_check("approve",$loc) || exponent_permissions_check("manage_approval",$loc));
		if ($db->countObjects("calendar","location_data='".serialize($loc)."' AND approved!=1")) {
			foreach ($db->selectObjects("calendar","location_data='".serialize($loc)."' AND approved!=1") as $c) {
				if ($c->poster == $user->id) $canviewapproval = true;
			}
			$inapproval = true;
		}

		$time = (isset($_GET['time']) ? $_GET['time'] : time());

		$template->assign("time",$time);

		$viewparams = $template->viewparams;
		if ($viewparams === null) {
			$viewparams = array("type"=>"default");
		}

		if (!defined("SYS_DATETIME")) include_once(BASE."subsystems/datetime.php");
		if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');

		if (!function_exists("exponent_sorting_byEventStartAscending")) {
			function exponent_sorting_byEventStartAscending($a,$b) {
				return ($a->eventstart < $b->eventstart ? -1 : 1);
			}
		}
		if ($viewparams['type'] == "minical") {
			$monthly = exponent_datetime_monthlyDaysTimestamp($time);
			$info = getdate($time);
			$timefirst = mktime(12,0,0,$info['mon'],1,$info['year']);
			$now = getdate(time());
			$currentday = $now['mday'];
			$endofmonth = date('t', $time);
			foreach ($monthly as $weekNum=>$week) {
				foreach ($week as $dayNum=>$day) {
					if ($dayNum == $now['mday']) {
						$currentweek = $weekNum;
					}
					if ($dayNum <= $endofmonth) {
						$monthly[$weekNum][$dayNum]['number'] = ($monthly[$weekNum][$dayNum]['ts'] != -1) ? $db->countObjects("eventdate",$locsql." AND date = ".$day['ts']) : -1;
					}
				}
			}
//eDebug($monthly);			
			$template->assign("monthly",$monthly);
			$template->assign("currentweek",$currentweek);
			$template->assign("currentday",$currentday);
			$template->assign("now",$timefirst);
			$prevmonth = mktime(0, 0, 0, date("m",$timefirst)-1, date("d",$timefirst)+10,   date("Y",$timefirst));
			$nextmonth = mktime(0, 0, 0, date("m",$timefirst)+1, date("d",$timefirst)+10,   date("Y",$timefirst));
			$template->assign("prevmonth",$prevmonth);
			$template->assign("thismonth",$timefirst);
			$template->assign("nextmonth",$nextmonth);
		} else if ($viewparams['type'] == "byday") {
		  // Remember this is the code for weekly view and monthly listview
		  // Test your fixes on both views before submitting your changes to cvs
   			$startperiod = 0;
			$totaldays = 0;
			if ($viewparams['range'] == "week") {
				$startperiod = exponent_datetime_startOfWeekTimestamp($time);
				$totaldays = 7;
				$template->assign("prev_timestamp3",strtotime('-21 days',$startperiod));
				$template->assign("prev_timestamp2",strtotime('-14 days',$startperiod));
				$template->assign("prev_timestamp",strtotime('-7 days',$startperiod));
				$template->assign("next_timestamp",strtotime('+7 days',$startperiod));
				$template->assign("next_timestamp2",strtotime('+14 days',$startperiod));
				$template->assign("next_timestamp3",strtotime('+21 days',$startperiod));
			} else if ($viewparams['range'] == "twoweek") {
				$time= time();
				$startperiod = exponent_datetime_startOfWeekTimestamp($time);
				$totaldays = 14;				
				$template->assign("prev_timestamp3",strtotime('-42 days',$startperiod));
				$template->assign("prev_timestamp2",strtotime('-28 days',$startperiod));
				$template->assign("prev_timestamp",strtotime('-14 days',$startperiod));
				$template->assign("next_timestamp",strtotime('+14 days',$startperiod));
				$template->assign("next_timestamp2",strtotime('+28 days',$startperiod));
				$template->assign("next_timestamp3",strtotime('+42 days',$startperiod));
			} else {  // range = month
				$startperiod = exponent_datetime_startOfMonthTimestamp($time);
				$totaldays  = date('t', $time);
				$template->assign("prev_timestamp3",strtotime('-3 months',$startperiod));
				$template->assign("prev_timestamp2",strtotime('-2 months',$startperiod));
				$template->assign("prev_timestamp",strtotime('-1 months',$startperiod));
				$template->assign("next_timestamp",strtotime('+1 months',$startperiod));
				$template->assign("next_timestamp2",strtotime('+2 months',$startperiod));
				$template->assign("next_timestamp3",strtotime('+3 months',$startperiod));
			}

			$days = array();
			// added per Ignacio
			$endofmonth = date('t', $time);
			for ($i = 1; $i <= $totaldays; $i++) {
				$info = getdate($time);
				if ( $viewparams['range'] == "week" ) {
					$start = mktime(12,0,0,$info['mon'],$i,$info['year']);
				} else if ( $viewparams['range'] == "twoweek" ) {
       				$start = mktime(12,0,0,$info['mon'],$info['mday']+($i-1),$info['year']);
//          		$start = $startperiod + ($i*86400);
				} else {  // range = month
					$start = mktime(0,0,0,$info['mon'],$i,$info['year']);
				}
				$edates = $db->selectObjects("eventdate",$locsql." AND date = '".$start."'");
				$days[$start] = calendarmodule::_getEventsForDates($edates);
				for ($j = 0; $j < count($days[$start]); $j++) {
					$thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$days[$start][$j]->id);
					$days[$start][$j]->permissions = array(
						"administrate"=>(exponent_permissions_check("administrate",$thisloc) || exponent_permissions_check("administrate",$loc)),
						"edit"=>(exponent_permissions_check("edit",$thisloc) || exponent_permissions_check("edit",$loc)),
						"delete"=>(exponent_permissions_check("delete",$thisloc) || exponent_permissions_check("delete",$loc))
					);
				}
				usort($days[$start],"exponent_sorting_byEventStartAscending");
			}
			$template->assign("days",$days);
		} else if ($viewparams['type'] == "monthly") {
			$monthly = array();
			$counts = array();
			$info = getdate($time);
			$nowinfo = getdate(time());
			if ($info['mon'] != $nowinfo['mon']) $nowinfo['mday'] = -10;
			// Grab non-day numbers only (before end of month)
			$week = 0;
			$currentweek = -1;
			$timefirst = mktime(12,0,0,$info['mon'],1,$info['year']);
			$infofirst = getdate($timefirst);
			$monthly[$week] = array(); // initialize for non days
			$counts[$week] = array();
			if ( ($infofirst['wday'] == 0) && (DISPLAY_START_OF_WEEK == 1) ) {
				for ($i = -6; $i < (1-DISPLAY_START_OF_WEEK); $i++) {
					$monthly[$week][$i] = array();
					$counts[$week][$i] = -1;
				}
				$weekday = $infofirst['wday']+7; // day number in grid.  if 7+, switch weeks
			} else {
				for ($i = 1 - $infofirst['wday']; $i < (1-DISPLAY_START_OF_WEEK); $i++) {
					$monthly[$week][$i] = array();
					$counts[$week][$i] = -1;
				}
				$weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
			}
			// Grab day counts (deprecated, handled by the date function)
			// $endofmonth = exponent_datetime_endOfMonthDay($time);
			$endofmonth = date('t', $time);
			for ($i = 1; $i <= $endofmonth; $i++) {
				$start = mktime(0,0,0,$info['mon'],$i,$info['year']);
				if ($i == $nowinfo['mday']) $currentweek = $week;
				#$monthly[$week][$i] = $db->selectObjects("calendar","location_data='".serialize($loc)."' AND (eventstart >= $start AND eventend <= " . ($start+86399) . ") AND approved!=0");
				//$dates = $db->selectObjects("eventdate",$locsql." AND date = $start");
				$dates = $db->selectObjects("eventdate",$locsql." AND date = '".$start."'");
				$monthly[$week][$i] = calendarmodule::_getEventsForDates($dates);
				$counts[$week][$i] = count($monthly[$week][$i]);
				if ($weekday >= (6+DISPLAY_START_OF_WEEK)) {
					$week++;
					$monthly[$week] = array(); // allocate an array for the next week
					$counts[$week] = array();
					$weekday = DISPLAY_START_OF_WEEK;
				} else $weekday++;
			}
			// Grab non-day numbers only (after end of month)
			for ($i = 1; $weekday && $i < (8+DISPLAY_START_OF_WEEK-$weekday); $i++) {
				$monthly[$week][$i+$endofmonth] = array();
				$counts[$week][$i+$endofmonth] = -1;
			}
//eDebug($monthly);			
			$template->assign("currentweek",$currentweek);
			$template->assign("monthly",$monthly);
			$template->assign("counts",$counts);
			$template->assign("prevmonth3",strtotime('-3 months',$timefirst));
			$template->assign("prevmonth2",strtotime('-2 months',$timefirst));
			$template->assign("prevmonth",strtotime('-1 months',$timefirst));
			$template->assign("nextmonth",strtotime('+1 months',$timefirst));
			$template->assign("nextmonth2",strtotime('+2 months',$timefirst));
			$template->assign("nextmonth3",strtotime('+3 months',$timefirst));
			$template->assign("now",$timefirst);
			$template->assign("today",strtotime('today')-43200);
		} else if ($viewparams['type'] == "administration") {
			// Check perms and return if cant view
			if ($viewparams['type'] == "administration" && !$user) return;
			$continue = (exponent_permissions_check("administrate",$loc) ||
						exponent_permissions_check("post",$loc) ||
						exponent_permissions_check("edit",$loc) ||
						exponent_permissions_check("delete",$loc) ||
						exponent_permissions_check("approve",$loc) ||
						exponent_permissions_check("manage_approval",$loc)
				) ? 1 : 0;
			$dates = $db->selectObjects("eventdate",$locsql." AND date >= '".exponent_datetime_startOfDayTimestamp(time())."'");
			$items = calendarmodule::_getEventsForDates($dates);
			if (!$continue) {
				foreach ($items as $i) {
					$iloc = exponent_core_makeLocation($loc->mod,$loc->src,$i->id);
					if (exponent_permissions_check("edit",$iloc) ||
						exponent_permissions_check("delete",$iloc) ||
						exponent_permissions_check("administrate",$iloc)
					) {
						$continue = true;
					}
				}
			}
			if (!$continue) return;
			for ($i = 0; $i < count($items); $i++) {
				$thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$items[$i]->id);
				if ($user && $items[$i]->poster == $user->id) $canviewapproval = 1;
				$items[$i]->permissions = array(
					"administrate"=>(exponent_permissions_check("administrate",$thisloc) || exponent_permissions_check("administrate",$loc)),
					"edit"=>(exponent_permissions_check("edit",$thisloc) || exponent_permissions_check("edit",$loc)),
					"delete"=>(exponent_permissions_check("delete",$thisloc) || exponent_permissions_check("delete",$loc))
				);
			}
			usort($items,"exponent_sorting_byEventStartAscending");
			$template->assign("items",$items);
		} else if ($viewparams['type'] == "default") {
			if (!isset($viewparams['range'])) $viewparams['range'] = "all";
			$items = null;
			$dates = null;
			$day = exponent_datetime_startOfDayTimestamp(time());
			$sort_asc = true; // For the getEventsForDates call
			$moreevents = false;
			switch ($viewparams['range']) {
				case "all":
					$dates = $db->selectObjects("eventdate",$locsql);
					break;
				case "upcoming":
					if (!empty($config->rss_limit) && $config->rss_limit > 0) {
						$eventlimit = " AND date <= " . ($day + ($config->rss_limit * 86400));
					} else {
						$eventlimit = "";
					}				
					$dates = $db->selectObjects("eventdate",$locsql." AND date >= ".$day.$eventlimit." ORDER BY date ASC ");
//					$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date >= $day");					
					break;
				case "past":
					$dates = $db->selectObjects("eventdate",$locsql." AND date < $day ORDER BY date DESC ");
//					$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date < $day");					
					$sort_asc = false;
					break;
				case "today":
					$dates = $db->selectObjects("eventdate",$locsql." AND date = $day");
					break;
				case "next":
					$dates = array($db->selectObject("eventdate",$locsql." AND date >= $day"));
					break;
				case "month":
					$dates = $db->selectObjects("eventdate",$locsql." AND date >= ".exponent_datetime_startOfMonthTimestamp(time()) . " AND date <= " . exponent_datetime_endOfMonthTimestamp(time()));
					break;
			}
			$items = calendarmodule::_getEventsForDates($dates,$sort_asc,isset($template->viewconfig['featured_only']) ? true : false);
			// Upcoming events can be configured to show a specific number of events.
			// The previous call gets all events in the future from today
			// If configured, cut the array to the configured number of events
//			if ($template->viewconfig['num_events']) {
//				switch ($viewparams['range']) {
//					case "upcoming":
//					case "past":
//						$moreevents = $template->viewconfig['num_events'] < count($items);	
//						break;
//				}
//				$items = array_slice($items, 0, $template->viewconfig['num_events']);
//eDebug($items);
//			}			
			for ($i = 0; $i < count($items); $i++) {
				$thisloc = exponent_core_makeLocation($loc->mod,$loc->src,$items[$i]->id);
				if ($user && $items[$i]->poster == $user->id) $canviewapproval = 1;
				$items[$i]->permissions = array(
					'administrate'=>(exponent_permissions_check('administrate',$thisloc) || exponent_permissions_check('administrate',$loc)),
					'edit'=>(exponent_permissions_check('edit',$thisloc) || exponent_permissions_check('edit',$loc)),
					'delete'=>(exponent_permissions_check('delete',$thisloc) || exponent_permissions_check('delete',$loc))
				);
			}
			//Get the image file if there is one.
			// for ($i = 0; $i < count($items); $i++) {
				// if (isset($items[$i]->file_id) && $items[$i]->file_id > 0) {
					// $file = $db->selectObject('file', 'id='.$items[$i]->file_id);
					// $items[$i]->image_path = $file->directory.'/'.$file->filename;
				// }
			// }
			//eDebug($items);
			$template->assign('items',$items);
			$template->assign('moreevents',$moreevents);
		}
		$template->assign('in_approval',$inapproval);
		$template->assign('canview_approval_link',$canviewapproval);
		$template->register_permissions(
			array('administrate','configure','post','edit','delete','manage_approval','manage_categories'),
			$loc
		);

//		$cats = $db->selectObjectsIndexedArray("category","location_data='".serialize($loc)."'");
		// $cats = $db->selectObjectsIndexedArray("category");
		// $cats[0] = null;
		// $cats[0]->name = '<i>'.$i18n['no_category'].'</i>';
		// $cats[0]->color = "#000000";
		// $template->assign("categories",$cats);

		if (!$config) {
			// $config->enable_categories = 0;
			$config->enable_ical = 1;
		}

		$template->assign("config",$config);
		if (!isset($config->enable_ical)) {$config->enable_ical = 1;}
		$template->assign("enable_ical", $config->enable_ical);

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
				// if(is_array($tag_ids)) {$selected_tags = $db->selectObjectsInArray('tags', $tag_ids, 'name');}
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
		$template->output();
	}

	function deleteIn($loc) {
		global $db;
		$refcount = $db->selectValue('sectionref', 'refcount', "source='".$loc->src."'");
                if ($refcount <= 0) {
			$items = $db->selectObjects("calendar","location_data='".serialize($loc)."'");
			foreach ($items as $i) {
				$db->delete("calendar_wf_revision","wf_original=".$i->id);
				$db->delete("calendar_wf_info","real_id=".$i->id);
			}
			$db->delete("calendar","location_data='".serialize($loc)."'");
		}
	}

	function searchName() {
		return "Calendar Events";
	}

	function spiderContent($item = null) {
		global $db;

		$i18n = exponent_lang_loadFile('modules/calendarmodule/class.php');

		if (!defined('SYS_SEARCH')) include_once(BASE.'subsystems/search.php');

		$search = null;
		$search->category = $i18n['search_category'];
		$search->view_link = ''; // FIXME : need a view action
		$search->ref_module = 'calendarmodule';
		$search->ref_type = 'calendar';

		if ($item) {
			$db->delete('search',"ref_module='calendarmodule' AND ref_type='calendar' AND original_id=" . $item->id);
			$search->original_id = $item->id;
			$search->body = ' ' . exponent_search_removeHTML($item->body) . ' ';
			$search->title = ' ' . $item->title . ' ';
			$search->location_data = $item->location_data;
			$db->insertObject($search,'search');
		} else {
			$db->delete('search',"ref_module='calendarmodule' AND ref_type='calendar'");
			foreach ($db->selectObjects('calendar') as $item) {
				$search->original_id = $item->id;
				$search->body = ' ' . exponent_search_removeHTML($item->body) . ' ';
				$search->title = ' ' . $item->title . ' ';
				$search->location_data = $item->location_data;
				$db->insertObject($search,'search');
			}
		}
		return true;
	}

	// The following functions are internal helper functions

	function _getEventsForDates($edates,$sort_asc = true,$featuredonly = false) {
		if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
		if ($sort_asc && !function_exists('exponent_sorting_byEventStartAscending')) {
			function exponent_sorting_byEventStartAscending($a,$b) {
				return ($a->eventstart < $b->eventstart ? -1 : 1);
			}
		}
		if (!$sort_asc && !function_exists('exponent_sorting_byEventStartDescending')) {
			function exponent_sorting_byEventStartDescending($a,$b) {
				return ($a->eventstart > $b->eventstart ? -1 : 1);
			}
		}

		global $db;
		$events = array();
		$featuresql = "";
		if ($featuredonly) $featuresql = " AND is_featured=1";
		foreach ($edates as $edate) {
			$o = $db->selectObject("calendar","id=".$edate->event_id.$featuresql);
			if ($o != null) {
				$o->eventdate = $edate;
				$o->eventstart += $edate->date;
				$o->eventend += $edate->date;
				$events[] = $o;
			}
		}
		if ($sort_asc == true) {
			usort($events,'exponent_sorting_byEventStartAscending');
		} else {
			usort($events,'exponent_sorting_byEventStartDescending');
		}
		return $events;
	}
}
?>