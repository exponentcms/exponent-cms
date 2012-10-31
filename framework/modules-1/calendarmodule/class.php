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
/** @define "BASE" "../../.." */

class calendarmodule {
    function name() { return $this->displayname(); }
    static function displayname() { return 'Calendar (Deprecated)'; }
    static function author() { return 'OIC Group, Inc'; }
    static function description() { return 'Allows posting of content to a calendar.'; }
    static function hasContent() { return true; }
	static function hasSources() { return true; }
    static function hasViews()   { return true; }
    static function supportsWorkflow() { return false; }

//	function getRSSContent($loc) {
//		global $db;
//
//		//Get this modules configuration data
//		$config = $db->selectObject('calendarmodule_config',"location_data='".serialize($loc)."'");
//
//		//If this module was configured as an aggregator, then turn off check for the location_data
//		$locsql = "(location_data='".serialize($loc)."'";
//		if (!empty($config->aggregate)) {
//			$locations = unserialize($config->aggregate);
//			foreach ($locations as $source) {
//				$tmploc = null;
//				$tmploc->mod = 'calendarmodule';
//				$tmploc->src = $source;
//				$tmploc->int = '';
//				$locsql .= " OR location_data='".serialize($tmploc)."'";
//			}
//		}
//		$locsql .= ')';
//
//		$day = expDateTime::startOfDayTimestamp(time());
//
//		if ($config->rss_limit > 0) {
//			$rsslimit = " AND date <= " . ($day + ($config->rss_limit * 86400));
//		} else {
//			$rsslimit = "";
//		}
//
//		$cats = $db->selectObjectsIndexedArray("category");
//		$cats[0] = null;
//		$cats[0]->name = 'None';
//
//		//Get this modules items
//		$items = array();
//		$dates = null;
//		$sort_asc = true; // For the getEventsForDates call
//		$dates = $db->selectObjects("eventdate", $locsql." AND date >= ".$day.$rsslimit." ORDER BY date ASC ");
//		$items = self::_getEventsForDates($dates, $sort_asc);
//
//		//Convert the events to rss items
//		$rssitems = array();
//		foreach ($items as $key => $item) {
//			$rss_item = new FeedItem();
//			$rss_item->title = $item->title;
//			$rss_item->description = $item->body;
//			$rss_item->date = date('r', $item->eventstart);
////          $rss_item->date = date('r', $item->posted);
////			$rss_item->link = "http://".HOSTNAME.PATH_RELATIVE."index.php?module=calendarmodule&action=view&id=".$item->id."&src=".$loc->src;
//			$rss_item->link = expCore::makeLink(array('module'=>'calendarmodule', 'action'=>'view', 'id'=>$item->id, 'date_id'=>$item->eventdate->id));
//			if ($config->enable_categories == 1) {
//				$rss_item->category = array($cats[$item->category_id]->name);
//			}
//			$rssitems[$key] = $rss_item;
//		}
//		return $rssitems;
//	}

	static function copyContent($oloc,$nloc) {

	}

	function permissions($internal = '') {
		if ($internal == '') {
			return array(
				'manage'=>gt('Manage'),
				'configure'=>gt('Configure'),
				'create'=>gt('Create'),
				'edit'=>gt('Edit'),
				'delete'=>gt('Delete'),
			);
		} else {
			return array(
				'manage'=>gt('Manage'),
				'edit'=>gt('Edit'),
				'delete'=>gt('Delete')
			);
		}
	}

	static function getLocationHierarchy($loc) {
		if ($loc->int == '') return array($loc);
		else return array($loc,expCore::makeLocation($loc->mod,$loc->src));  // array of
	}

	static function show($view,$loc = null, $title = '') {
		global $db, $user;

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

		$template = new template('calendarmodule',$view,$loc);

		$time = (isset($_GET['time']) ? $_GET['time'] : time());
//        if (isset($_POST['newtime'])) {
//            $time = strtotime($_POST['newtime']);
//        }
		$template->assign("time",$time);

		$viewparams = $template->viewparams;
		if ($viewparams === null) $viewparams = array("type"=>"default");
        if (!isset($viewparams['range'])) $viewparams['range'] = "all";

        switch ($viewparams['type']) {
            case "minical":
                $monthly = expDateTime::monthlyDaysTimestamp($time);
                $info = getdate($time);
                $timefirst = mktime(0,0,0,$info['mon'],1,$info['year']);
                $now = getdate(time());
                $currentday = $now['mday'];
                $endofmonth = date('t', $time);
                foreach ($monthly as $weekNum=>$week) {
                    foreach ($week as $dayNum=>$day) {
                        if ($dayNum == $now['mday']) {
                            $currentweek = $weekNum;
                        }
                        if ($dayNum <= $endofmonth) {
                            $monthly[$weekNum][$dayNum]['number'] = ($monthly[$weekNum][$dayNum]['ts'] != -1) ? $db->countObjects("eventdate",$locsql." AND date >= ".expDateTime::startOfDayTimestamp($day['ts'])." AND date <= ".expDateTime::endOfDayTimestamp($day['ts'])) : -1;
                        }
                    }
                }
                $template->assign("monthly",$monthly);
                $template->assign("currentweek",$currentweek);
                $template->assign("currentday",$currentday);
                $template->assign("now",$timefirst);
                $prevmonth = mktime(0, 0, 0, date("m",$timefirst)-1, date("d",$timefirst)+10,   date("Y",$timefirst));
                $nextmonth = mktime(0, 0, 0, date("m",$timefirst)+1, date("d",$timefirst)+10,   date("Y",$timefirst));
                $template->assign("prevmonth",$prevmonth);
                $template->assign("thismonth",$timefirst);
                $template->assign("nextmonth",$nextmonth);
                break;
            case "byday":
              // Remember this is the code for weekly view and monthly listview
              // Test your fixes on both views
    //   		$startperiod = 0;
    //			$totaldays = 0;
                switch ($viewparams['range']) {
                    case "week":
                        $startperiod = expDateTime::startOfWeekTimestamp($time);
                        $totaldays = 7;
                        $template->assign("prev_timestamp3",strtotime('-21 days',$startperiod));
                        $template->assign("prev_timestamp2",strtotime('-14 days',$startperiod));
                        $template->assign("prev_timestamp",strtotime('-7 days',$startperiod));
                        $next = strtotime('+7 days',$startperiod);
                        $template->assign("next_timestamp",$next);
                        $template->assign("next_timestamp2",strtotime('+14 days',$startperiod));
                        $template->assign("next_timestamp3",strtotime('+21 days',$startperiod));
                        if (!empty($template->viewconfig['starttype'])) $startperiod = $time;
                        $template->assign("time",$startperiod);
                        break;
                    case "twoweek":
                        $startperiod = expDateTime::startOfWeekTimestamp($time);
                        $totaldays = 14;
                        $template->assign("prev_timestamp3",strtotime('-42 days',$startperiod));
                        $template->assign("prev_timestamp2",strtotime('-28 days',$startperiod));
                        $template->assign("prev_timestamp",strtotime('-14 days',$startperiod));
                        $next = strtotime('+14 days',$startperiod);
                        $template->assign("next_timestamp",$next);
                        $template->assign("next_timestamp2",strtotime('+28 days',$startperiod));
                        $template->assign("next_timestamp3",strtotime('+42 days',$startperiod));
                        if (!empty($template->viewconfig['starttype'])) $startperiod = $time;
                        $template->assign("time",$startperiod);
                        break;
                    default:  // range = month
                        $startperiod = expDateTime::startOfMonthTimestamp($time);
                        $totaldays  = date('t', $time);
                        $template->assign("prev_timestamp3",strtotime('-3 months',$startperiod));
                        $template->assign("prev_timestamp2",strtotime('-2 months',$startperiod));
                        $template->assign("prev_timestamp",strtotime('-1 months',$startperiod));
                        $next = strtotime('+1 months',$startperiod);
                        $template->assign("next_timestamp",$next);
                        $template->assign("next_timestamp2",strtotime('+2 months',$startperiod));
                        $template->assign("next_timestamp3",strtotime('+3 months',$startperiod));
                }

//                $days = array();
                // added per Ignacio
    //			$endofmonth = date('t', $time);
                //FIXME add external events to $days[$start] for date $start, one day at a time
                $extitems = self::getExternalEvents($loc,$startperiod,$next);
                for ($i = 1; $i <= $totaldays; $i++) {
//                    $info = getdate($time);
//                    switch ($viewparams['range']) {
//                        case "week":
//                            $start = mktime(0,0,0,$info['mon'],$i,$info['year']);  //FIXME this can't be right?
//                            break;
//                        case "twoweek":
////                            $start = mktime(0,0,0,$info['mon'],$info['mday']+($i-1),$info['year']);  //FIXME this can't be right?
//                  		    $start = $startperiod + ($i*86400);
//                            break;
//                        default:  // range = month
//                            $start = mktime(0,0,0,$info['mon'],$i,$info['year']);
//                    }
                    $start = $startperiod + ($i*86400) - 86400;
                    $edates = $db->selectObjects("eventdate",$locsql." AND date >= ".expDateTime::startOfDayTimestamp($start)." AND date <= ".expDateTime::endOfDayTimestamp($start));
                    $days[$start] = self::_getEventsForDates($edates,true,isset($template->viewconfig['featured_only']) ? true : false);
//                    for ($j = 0; $j < count($days[$start]); $j++) {
//                        $thisloc = expCore::makeLocation($loc->mod,$loc->src,$days[$start][$j]->id);
//                        $days[$start][$j]->permissions = array(
//                            "manage"=>(expPermissions::check("manage",$thisloc) || expPermissions::check("manage",$loc)),
//                            "edit"=>(expPermissions::check("edit",$thisloc) || expPermissions::check("edit",$loc)),
//                            "delete"=>(expPermissions::check("delete",$thisloc) || expPermissions::check("delete",$loc))
//                        );
//                    }
                    if (!empty($extitems[$start])) $days[$start] = array_merge($extitems[$start],$days[$start]);
                    $days[$start] = expSorter::sort(array('array'=>$days[$start],'sortby'=>'eventstart', 'order'=>'ASC'));
                }
                $template->assign("days",$days);
                break;
            case "monthly":
//                $monthly = array();
//                $counts = array();
                $info = getdate($time);
                $nowinfo = getdate(time());
                if ($info['mon'] != $nowinfo['mon']) $nowinfo['mday'] = -10;
                // Grab non-day numbers only (before end of month)
                $week = 0;
                $currentweek = -1;
                $timefirst = mktime(0,0,0,$info['mon'],1,$info['year']);
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
                // $endofmonth = expDateTime::endOfMonthDay($time);
                $endofmonth = date('t', $time);
                //FIXME add external events to $monthly[$week][$i] for date $start, one day at a time
                $extitems = self::getExternalEvents($loc,$timefirst,expDateTime::endOfMonthTimestamp($timefirst));
                for ($i = 1; $i <= $endofmonth; $i++) {
                    $start = mktime(0,0,0,$info['mon'],$i,$info['year']);
                    if ($i == $nowinfo['mday']) $currentweek = $week;
                    #$monthly[$week][$i] = $db->selectObjects("calendar","location_data='".serialize($loc)."' AND (eventstart >= $start AND eventend <= " . ($start+86399) . ") AND approved!=0");
                    //$dates = $db->selectObjects("eventdate",$locsql." AND date = $start");
                    $dates = $db->selectObjects("eventdate",$locsql." AND (date >= ".expDateTime::startOfDayTimestamp($start)." AND date <= ".expDateTime::endOfDayTimestamp($start).")");
                    $monthly[$week][$i] = self::_getEventsForDates($dates,true,isset($template->viewconfig['featured_only']) ? true : false);
                    if (!empty($extitems[$start])) $monthly[$week][$i] = array_merge($extitems[$start],$monthly[$week][$i]);
                    $monthly[$week][$i] = expSorter::sort(array('array'=>$monthly[$week][$i],'sortby'=>'eventstart', 'order'=>'ASC'));
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
                $template->assign("today",expDateTime::startOfDayTimestamp(time()));
                break;
            case "administration":
                // Check perms and return if cant view
                if (!$user) return;
                $continue = (expPermissions::check("manage",$loc) ||
                            expPermissions::check("create",$loc) ||
                            expPermissions::check("edit",$loc) ||
                            expPermissions::check("delete",$loc)
                    ) ? 1 : 0;
                $dates = $db->selectObjects("eventdate",$locsql." AND date >= ".expDateTime::startOfDayTimestamp(time()));
                $items = self::_getEventsForDates($dates);
//                if (!$continue) {
//                    foreach ($items as $i) {
//                        $iloc = expCore::makeLocation($loc->mod,$loc->src,$i->id);
//                        if (expPermissions::check("edit",$iloc) ||
//                            expPermissions::check("delete",$iloc) ||
//                            expPermissions::check("manage",$iloc)
//                        ) {
//                            $continue = true;
//                        }
//                    }
//                }
                if (!$continue) return;
//                for ($i = 0; $i < count($items); $i++) {
//                    $thisloc = expCore::makeLocation($loc->mod,$loc->src,$items[$i]->id);
//    //				if ($user && $items[$i]->poster == $user->id) $canviewapproval = 1;
//                    $items[$i]->permissions = array(
//                        "manage"=>(expPermissions::check("manage",$thisloc) || expPermissions::check("manage",$loc)),
//                        "edit"=>(expPermissions::check("edit",$thisloc) || expPermissions::check("edit",$loc)),
//                        "delete"=>(expPermissions::check("delete",$thisloc) || expPermissions::check("delete",$loc))
//                    );
//                }
                $items = expSorter::sort(array('array'=>$items,'sortby'=>'eventstart', 'order'=>'ASC'));
                $template->assign("items",$items);
                break;
            case "default":
            default;
//                $items = null;
//                $dates = null;
                $day = expDateTime::startOfDayTimestamp(time());
                $sort_asc = true; // For the getEventsForDates call
//                $moreevents = false;
                switch ($viewparams['range']) {
                    case "upcoming":
                        if (!empty($config->rss_limit) && $config->rss_limit > 0) {
                            $eventlimit = " AND date <= " . ($day + ($config->rss_limit * 86400));
                        } else {
                            $eventlimit = "";
                        }
                        $dates = $db->selectObjects("eventdate",$locsql." AND date >= ".$day.$eventlimit." ORDER BY date ASC ");
                        $begin = $day;
                        $end = null;
    //					$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date >= $day");
                        break;
                    case "past":
                        $dates = $db->selectObjects("eventdate",$locsql." AND date < $day ORDER BY date DESC ");
    //					$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date < $day");
                        $sort_asc = false;
                        $begin = null;
                        $end = $day;
                        break;
                    case "today":
                        $dates = $db->selectObjects("eventdate",$locsql." AND (date >= ".expDateTime::startOfDayTimestamp($day)." AND date <= ".expDateTime::endOfDayTimestamp($day).")");
                        $begin = $day;
                        $end = expDateTime::endOfDayTimestamp($day);
                        break;
                    case "next":
                        $dates = array($db->selectObject("eventdate",$locsql." AND date >= $day"));
                        break;
                    case "month":
                        $dates = $db->selectObjects("eventdate",$locsql." AND (date >= ".expDateTime::startOfMonthTimestamp(time()) . " AND date <= " . expDateTime::endOfMonthTimestamp(time()).")");
                        $begin = expDateTime::startOfMonthTimestamp($day);
                        $end = expDateTime::endOfMonthTimestamp($day);
                        break;
                    case "all":
                    default;
                        $dates = $db->selectObjects("eventdate",$locsql);
                        $begin = null;
                        $end = null;
                }
                $items = self::_getEventsForDates($dates,$sort_asc,isset($template->viewconfig['featured_only']) ? true : false);
                //FIXME add external events to $items for date >= ".expDateTime::startOfMonthTimestamp(time()) . " AND date <= " . expDateTime::endOfMonthTimestamp(time())
                $extitems = self::getExternalEvents($loc,$begin,$end);
                // we need to crunch these down
                $extitem = array();
                foreach ($extitems as $key=>$value) {
                    $extitem[] = $value;
                }
                $items = array_merge($items,$extitem);
                $items = expSorter::sort(array('array'=>$items,'sortby'=>'eventstart', 'order'=>'ASC'));
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
    //			}
//                for ($i = 0; $i < count($items); $i++) {
//                    $thisloc = expCore::makeLocation($loc->mod,$loc->src,$items[$i]->id);
//                    $items[$i]->permissions = array(
//                        'manage'=>(expPermissions::check('manage',$thisloc) || expPermissions::check('manage',$loc)),
//                        'edit'=>(expPermissions::check('edit',$thisloc) || expPermissions::check('edit',$loc)),
//                        'delete'=>(expPermissions::check('delete',$thisloc) || expPermissions::check('delete',$loc))
//                    );
//                }
                $template->assign('items',$items);
//                $template->assign('moreevents',$moreevents);
		}

		$template->register_permissions(
			array('manage','configure','create','edit','delete'),
			$loc
		);
        if (empty($title)) $title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
        $template->assign('moduletitle',$title);
		$template->assign("config",$config);

		$template->output();
	}

	function deleteIn($loc) {
		global $db;
		$refcount = $db->selectValue('sectionref', 'refcount', "source='".$loc->src."'");
        if ($refcount <= 0) {
//			$items = $db->selectObjects("calendar","location_data='".serialize($loc)."'");
			$db->delete("calendar","location_data='".serialize($loc)."'");
		}
	}

	static function searchName() {
		return gt("Calendar Event");
	}

    static function searchCategory() {
  		return gt('Event');
  	}

	static function spiderContent($item = null) {
		global $db;

		$search = new stdClass();
		$search->category = self::searchCategory();
		$search->ref_module = 'calendarmodule';
		$search->ref_type = 'calendar';

		if ($item) {
			$db->delete('search',"ref_module='calendarmodule' AND ref_type='calendar' AND original_id=" . $item->id);
			$search->original_id = $item->id;
			$search->body = ' ' . search::removeHTML($item->body) . ' ';
			$search->title = ' ' . $item->title . ' ';
			$search->view_link = str_replace(URL_FULL,'', makeLink(array('module'=>'calendarmodule','action'=>'view','id'=>$item->id)));
			$search->location_data = $item->location_data;
			$db->insertObject($search,'search');
            $items = array($item);
		} else {
			$db->delete('search',"ref_module='calendarmodule' AND ref_type='calendar'");
            $items = $db->selectObjects('calendar');
			foreach ($items as $item) {
                $db->delete('search',"ref_module='calendarmodule' AND ref_type='calendar' AND original_id=" . $item->id);
				$search->original_id = $item->id;
				$search->body = ' ' . search::removeHTML($item->body) . ' ';
				$search->title = ' ' . $item->title . ' ';
				$search->view_link = str_replace(URL_FULL,'', makeLink(array('module'=>'calendarmodule','action'=>'view','id'=>$item->id)));
				$search->location_data = $item->location_data;
				$db->insertObject($search,'search');
			}
		}
		return count($items);
	}

	// The following functions are internal helper functions

	static function _getEventsForDates($edates,$sort_asc = true,$featuredonly = false) {
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
		$events = expSorter::sort(array('array'=>$events,'sortby'=>'eventstart', 'order'=>$sort_asc ? 'ASC' : 'DESC'));
		return $events;
	}

    static function getExternalEvents($loc,$startdate,$enddate) {
        global $db;

        $extevents = array();
        $dy = 0;
        $config = $db->selectObject("calendarmodule_config","location_data='".serialize($loc)."'");
        if (!empty($config)) foreach ($db->selectObjects('calendar_external',"calendar_id='".$config->id."'") as $extcal) {
        	if ($extcal->type == GOOGLE_TYPE) {
                if (!empty($startdate)) $begin = date("Y-m-d\Th:i:sP", expDateTime::startOfDayTimestamp($startdate));
                if (!empty($enddate)) $end = date("Y-m-d\Th:i:sP", expDateTime::endOfDayTimestamp($enddate));

                if (substr($extcal->url,-5) == 'basic') {
                    $extcal->url = substr($extcal->url,0,strlen($extcal->url)-5).'full';
                }
                $feed = $extcal->url."?orderby=starttime&singleevents=true";
                if (!empty($startdate)) $feed .= "&start-min=" . $begin;
                if (!empty($enddate)) $feed .= "&start-max=" . $end;

                // XML method
//                $s = simplexml_load_file($feed);
//               	foreach ($s->entry as $item) {
//               		$gd = $item->children('http://schemas.google.com/g/2005');
//                    if (!empty($gd->when)) {
//                       $dtstart = $gd->when->attributes()->startTime;
//                    } elseif (!empty($gd->recurrence)){
//                       $dtstart = $gd->recurrence->when->attributes()->startTime;
//                    } else {
//                        $dtstart = $item->attributes()->When;
//                    }
//                    //FIXME must convert $dtstart timezone
//                    $eventdate = expDateTime::startOfDayTimestamp(strtotime($dtstart));
//                    $extevents[$eventdate][$dy] = new stdClass();
//                    $extevents[$eventdate][$dy]->eventdate = $eventdate;
//                    $extevents[$eventdate][$dy]->eventstart += strtotime($dtstart);
//                    if (!empty($gd->when)) {
//                        $dtend = $gd->when->attributes()->endTime;
//                    } elseif (!empty($gd->recurrence)) {
//                        $dtend = $gd->recurrence->when->attributes()->endTime;
//                    }
//                    //FIXME must convert $dtend timezone
//                    if (!empty($dtend)) $extevents[$eventdate][$dy]->eventend += strtotime($dtend);
//                    // dtstart required, one occurrence, (orig. start date)
//                    $extevents[$eventdate][$dy]->title = $item->title;
//                    $extevents[$eventdate][$dy]->body = $item->content;
                    // End XML method

                  // DOM method
                $doc = new DOMDocument();
                $doc->load($feed);
                $entries = $doc->getElementsByTagName( "entry" );
                foreach ($entries as $item) {
                    $times = $item->getElementsByTagName("when");
                    $dtstart = $times->item(0)->getAttributeNode("startTime")->value;
//                  //FIXME must convert $dtstart & $dtend timezone
                    $eventdate = expDateTime::startOfDayTimestamp(strtotime($dtstart));
                    $extevents[$eventdate][$dy] = new stdClass();
                    $extevents[$eventdate][$dy]->eventdate = $eventdate;
                    $dtend = $times->item(0)->getAttributeNode("endTime")->value;
                    if (strlen($dtstart) > 10) {
                        $extevents[$eventdate][$dy]->eventstart = (intval(substr($dtstart,11,2))*3600)+(intval(substr($dtstart,14,2))*60);
                        if (date("I",$eventdate)) $extevents[$eventdate][$dy]->eventstart += 3600;
                        $extevents[$eventdate][$dy]->eventend = (intval(substr($dtend,11,2))*3600)+(intval(substr($dtend,14,2))*60);
                        if (date("I",$eventdate)) $extevents[$eventdate][$dy]->eventend += 3600;
                    } else {
                        $extevents[$eventdate][$dy]->eventstart = null;
                        $extevents[$eventdate][$dy]->is_allday = 1;
                    }
                    $titles = $item->getElementsByTagName("title");
                    $extevents[$eventdate][$dy]->title = $titles->item(0)->nodeValue;
                    $contents = $item->getElementsByTagName("content");
                    $extevents[$eventdate][$dy]->body = $contents->item(0)->nodeValue;
                    // End DOM method

                    $extevents[$eventdate][$dy]->location_data = null;
                    $dy++;
                }
        	} else if ($extcal->type == ICAL_TYPE) {
                require_once BASE.'external/iCalcreator.class.php';
                $v = new vcalendar(); // initiate new CALENDAR
                $v->setConfig('url',$extcal->url);
                $v->setProperty( "X-WR-TIMEZONE", DISPLAY_DEFAULT_TIMEZONE );
                $v->parse();
                if ($enddate == null) {
                    $startYear = null;
                    $startMonth = null;
                    $startDay = null;
                } else {
                    $startYear = date('Y',$startdate);
                    $startMonth = date('n',$startdate);
                    $startDay = date('j',$startdate);
                }
                if ($enddate == null) {
                    $endYear = null;
                    $endMonth = null;
                    $endDay = null;
                } else {
                    $endYear = date('Y',$enddate);
                    $endMonth = date('n',$enddate);
                    $endDay = date('j',$enddate);
                }
                $eventArray = $v->selectComponents($startYear,$startMonth,$startDay,$endYear,$endMonth,$endDay,'vevent');
                if (!empty($eventArray)) foreach ($eventArray as $year => $yearArray) {
                    if (!empty($yearArray)) foreach ($yearArray as $month => $monthArray) {
                        if (!empty($monthArray)) foreach ($monthArray as $day => $dailyEventsArray) {
                            if (!empty($dailyEventsArray)) foreach ($dailyEventsArray as $vevent) {
                                $yesterday = false;
                                $currddate = $vevent->getProperty('x-current-dtstart');
                                $thisday = explode('-',$currddate[1]);
                                // if member of a recurrence set,
                                // returns array( 'x-current-dtstart', <DATE>)
                                // <DATE> = (string) date("Y-m-d [H:i:s][timezone/UTC offset]")
                                $dtstart = $vevent->getProperty('dtstart',false,true);
                                //FIXME must convert $dtstart['TZID'] timezone
                                $dtend = $vevent->getProperty('dtend',false,true);
                                //FIXME must convert $dtend['TZID'] timezone
                                $eventdate = expDateTime::startOfDayTimestamp(iCalUtilityFunctions::_date2timestamp($dtstart['value']));
                                $extevents[$eventdate][$dy] = new stdClass();
                                $extevents[$eventdate][$dy]->eventdate = $eventdate;
                                if (!empty($dtstart['value']['hour'])) {
                                    $extevents[$eventdate][$dy]->eventstart = ($dtstart['value']['hour']*3600)+($dtstart['value']['min']*60);
                                    if (date("I",$eventdate)) $extevents[$eventdate][$dy]->eventstart += 3600;
                                } else {
                                    if ($dtstart['value']['day'] != $thisday[2]) $yesterday = true;
                                    $extevents[$eventdate][$dy]->eventstart = null;
                                    $extevents[$eventdate][$dy]->is_allday = 1;
                                }
                                if (!empty($dtend['value']['hour'])) {
                                    $extevents[$eventdate][$dy]->eventend = ($dtend['value']['hour']*3600)+($dtend['value']['min']*60);
                                    if (date("I",$eventdate)) $extevents[$eventdate][$dy]->eventend += 3600;
                                }
                                // dtstart required, one occurrence, (orig. start date)
                                $extevents[$eventdate][$dy]->title = $vevent->getProperty('summary');
                                $extevents[$eventdate][$dy]->body = $vevent->getProperty('description');

                                $extevents[$eventdate][$dy]->location_data = null;
                                if (!$yesterday) {
                                    $dy++;
                                } else {
                                    unset($extevents[$eventdate][$dy]);
                                }
                            }
                        }
                    }
                }
        	}
        }
        return $extevents;
    }

}

?>
