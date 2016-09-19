<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

/**
 * @subpackage Controllers
 * @package    Modules
 */

class eventController extends expController {
//    public $basemodel_name = 'event';
    public $useractions = array(
        'showall' => 'Show Calendar',
    );
//    public $codequality = 'beta';

    public $remove_configs = array(
        'comments',
        'ealerts',
//        'facebook',
        'files',
        'pagination',
        'rss',
//        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() {
        return "Events";
    }

    static function description() {
        return "Manage events and schedules, and optionally publish them.";
    }

    static function author() {
        return "Dave Leffler";
    }

    static function isSearchable() {
        return true;
    }

    function searchName() {
        return gt("Calendar Event");
    }

    function searchCategory() {
        return gt('Event');
    }

    /**
     * can this module import data?
     *
     * @return bool
     */
    public static function canImportData() {
        return true;
    }

    function showall() {
        global $user;

        expHistory::set('viewable', $this->params);
        $locsql = $this->aggregateWhereClause();
        $time = (isset($this->params['time']) ? $this->params['time'] : time());
        assign_to_template(array(
            'time' => $time,
            'daynames' => event::dayNames(),
        ));

        $regcolor = !empty($this->config['registrations_color']) ? $this->config['registrations_color'] : null;

        $ed = new eventdate();
        $viewtype = 'default';
        $viewrange = 'all';
        $view = !empty($this->params['view']) ? $this->params['view'] : 'showall';
        switch ($view) {
            case 'showall_Administration':
                $viewtype = "administration";
                break;
            case 'showall_Past Events':
                $viewrange = "past";
                break;
            case 'showall_Monthly Summary':
            case 'showall_Mini-Calendar':
            case 'minical':
                $viewtype = "minical";
                break;
            case 'showall_Monthly List':
            case 'showall_List':
            case 'monthlist':
                $viewtype = "byday";
                $viewrange = "month";
                break;
            case 'showall_Week':
            case 'week':
                $viewtype = "byday";
                $viewrange = "week";
                break;
            case 'showall_Day':
            case 'day':
                $viewtype = "byday";
                $viewrange = "day";
                break;
            case 'showall_announcement':
            case 'showall_Upcoming Events':
            case 'showall_Upcoming Events - Headlines':
                $viewrange = "upcoming";
                break;
            case 'showall':
            case 'month':
                $viewtype = "monthly";
                break;
            default :
                $view_params = explode('_',$view);
                if (!empty($view_params[1])) $viewtype = $view_params[1];
                if (!empty($view_params[2])) $viewrange = $view_params[2];
        }  // end switch $view

        switch ($viewtype) {
            case "minical":
                $monthly = expDateTime::monthlyDaysTimestamp($time);
                $info = getdate($time);
                $timefirst = mktime(0, 0, 0, $info['mon'], 1, $info['year']);
                $now = getdate(time());
                $currentday = $now['mday'];
                $endofmonth = date('t', $time);
                foreach ($monthly as $weekNum => $week) {
                    foreach ($week as $dayNum => $day) {
                        if ($dayNum == $now['mday']) {
                            $currentweek = $weekNum;
                        }
                        if ($dayNum <= $endofmonth) {
//                            $monthly[$weekNum][$dayNum]['number'] = ($monthly[$weekNum][$dayNum]['ts'] != -1) ? $db->countObjects("eventdate", $locsql . " AND date >= " . expDateTime::startOfDayTimestamp($day['ts']) . " AND date <= " . expDateTime::endOfDayTimestamp($day['ts'])) : -1;
                            $monthly[$weekNum][$dayNum]['number'] = ($monthly[$weekNum][$dayNum]['ts'] != -1) ? $ed->find("count", $locsql . " AND date >= " . expDateTime::startOfDayTimestamp($day['ts']) . " AND date <= " . expDateTime::endOfDayTimestamp($day['ts'])) : -1;
                        }
                    }
                }
                $prevmonth = mktime(0, 0, 0, date("m", $timefirst) - 1, date("d", $timefirst) + 10, date("Y", $timefirst));
                $nextmonth = mktime(0, 0, 0, date("m", $timefirst) + 1, date("d", $timefirst) + 10, date("Y", $timefirst));
                assign_to_template(array(
                    "monthly"     => $monthly,
                    "currentweek" => $currentweek,
                    "currentday"  => $currentday,
                    "now"         => $timefirst,
                    "prevmonth"   => $prevmonth,
                    "thismonth"   => $timefirst,
                    "nextmonth"   => $nextmonth,
                ));
                break;  // end switch $viewtype minicalendar
            case "byday":  //note aggregates events by groups of days
                // Remember this is the code for weekly view and monthly listview
                // Test your fixes on both views
                //   		$startperiod = 0;
                //			$totaldays = 0;
                switch ($viewrange) {
                    case "day":
                        $startperiod = expDateTime::startOfDayTimestamp($time);
                        $totaldays = 1;
                        $next = expDateTime::endOfDayTimestamp($startperiod);
                        if (!empty($this->config['starttype'])) $startperiod = $time;
                        $this->params['time'] = $time;
                        assign_to_template(array(
                            "prev_timestamp3" => strtotime('-3 days', $startperiod),
                            "prev_timestamp2" => strtotime('-2 days', $startperiod),
                            "prev_timestamp"  => strtotime('-1 days', $startperiod),
                            "next_timestamp"  => strtotime('+1 days', $startperiod),
                            "next_timestamp2" => strtotime('+2 days', $startperiod),
                            "next_timestamp3" => strtotime('+3 days', $startperiod),
                            'params'      => $this->params
                        ));
                        break;
                    case "week":
                        $startperiod = expDateTime::startOfWeekTimestamp($time);
                        $totaldays = 7;
                        $next = strtotime('+7 days', $startperiod);
//                        $next = expDateTime::endOfWeekTimestamp($startperiod);
                        if (!empty($this->config['starttype'])) $startperiod = $time;
                        $this->params['time'] = $time;
                        assign_to_template(array(
                            "prev_timestamp3" => strtotime('-21 days', $startperiod),
                            "prev_timestamp2" => strtotime('-14 days', $startperiod),
                            "prev_timestamp"  => strtotime('-7 days', $startperiod),
                            "next_timestamp"  => $next,
                            "next_timestamp2" => strtotime('+14 days', $startperiod),
                            "next_timestamp3" => strtotime('+21 days', $startperiod),
                            'params'      => $this->params
                        ));
                        break;
                    case "twoweek":
                        $startperiod = expDateTime::startOfWeekTimestamp($time);
                        $totaldays = 14;
                        $next = strtotime('+14 days', $startperiod);
                        if (!empty($this->config['starttype'])) $startperiod = $time;
                        assign_to_template(array(
                            "prev_timestamp3" => strtotime('-42 days', $startperiod),
                            "prev_timestamp2" => strtotime('-28 days', $startperiod),
                            "prev_timestamp"  => strtotime('-14 days', $startperiod),
                            "next_timestamp"  => $next,
                            "next_timestamp2" => strtotime('+28 days', $startperiod),
                            "next_timestamp3" => strtotime('+42 days', $startperiod),
                        ));
                        break;
                    case "month":
                    default: // range = month
                        $startperiod = expDateTime::startOfMonthTimestamp($time);
                        $totaldays = date('t', $time);
                        $next = strtotime('+1 months', $startperiod);
//                        $next = expDateTime::endOfMonthTimestamp($startperiod);
                        $this->params['time'] = $time;
                        assign_to_template(array(
                            "prev_timestamp3" => strtotime('-3 months', $startperiod),
                            "prev_timestamp2" => strtotime('-2 months', $startperiod),
                            "prev_timestamp"  => strtotime('-1 months', $startperiod),
                            "next_timestamp"  => $next,
                            "next_timestamp2" => strtotime('+2 months', $startperiod),
                            "next_timestamp3" => strtotime('+3 months', $startperiod),
                            'params'      => $this->params
                        ));
                        break;
                }  // end switch $viewrange

                //                $days = array();
                // added per Ignacio
                //			$endofmonth = date('t', $time);
                $extitems = $this->getExternalEvents($startperiod, $next);
                if (!empty($this->config['aggregate_registrations'])) 
                    $regitems = eventregistrationController::getRegEventsForDates($startperiod, $next, $regcolor);
                for ($i = 1; $i <= $totaldays; $i++) {
                    //                    $info = getdate($time);
                    //                    switch ($viewrange) {
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
                    $start = expDateTime::startOfDayTimestamp($startperiod + ($i * 86400) - 86400);
                    $edates = $ed->find("all", $locsql . " AND date >= " . expDateTime::startOfDayTimestamp($start) . " AND date <= " . expDateTime::endOfDayTimestamp($start));
//                    $days[$start] = $this->getEventsForDates($edates, true, isset($this->config['only_featured']) ? true : false);
                    $days[$start] = $this->event->getEventsForDates($edates, true, isset($this->config['only_featured']) ? true : false);
                    //                    for ($j = 0; $j < count($days[$start]); $j++) {
                    //                        $thisloc = expCore::makeLocation($this->loc->mod,$this->loc->src,$days[$start][$j]->id);
                    //                        $days[$start][$j]->permissions = array(
                    //                            "manage"=>(expPermissions::check("manage",$thisloc) || expPermissions::check("manage",$this->loc)),
                    //                            "edit"=>(expPermissions::check("edit",$thisloc) || expPermissions::check("edit",$this->loc)),
                    //                            "delete"=>(expPermissions::check("delete",$thisloc) || expPermissions::check("delete",$this->loc))
                    //                        );
                    //                    }
                    if (!empty($extitems[$start]))
                        $days[$start] = array_merge($extitems[$start], $days[$start]);
                    if (!empty($regitems[$start]))
                        $days[$start] = array_merge($regitems[$start], $days[$start]);
                    $days[$start] = expSorter::sort(array('array' => $days[$start], 'sortby' => 'eventstart', 'order' => 'ASC'));
                }
                assign_to_template(array(
                    "time" => $startperiod,
                    'days' => $days,
                    "now"  => $startperiod,
                ));
                break;  // end switch $viewtype byday
            case "monthly":  //note this is a simply array of events for the requested month
                // build a month array of weeks with an array of days
                //                $monthly = array();
                //                $counts = array();
                $info = getdate($time);
                $nowinfo = getdate(time());
                if ($info['mon'] != $nowinfo['mon']) $nowinfo['mday'] = -10;
                // Grab non-day numbers only (before end of month)
//                $week = 0;
                $currentweek = -1;
                $timefirst = mktime(0, 0, 0, $info['mon'], 1, $info['year']);
                $week = (int)date('W',$timefirst);
                if ($week >= 52 && $info['mon'] == 1) $week = 1;
                $infofirst = getdate($timefirst);
                $monthly[$week] = array(); // initialize for non days
                $counts[$week] = array();
                if (($infofirst['wday'] == 0) && (DISPLAY_START_OF_WEEK == 1)) {
                    for ($i = -6; $i < (1 - DISPLAY_START_OF_WEEK); $i++) {
                        $monthly[$week][$i] = array();
                        $counts[$week][$i] = -1;
                    }
                    $weekday = $infofirst['wday'] + 7; // day number in grid.  if 7+, switch weeks
                } else {
                    for ($i = 1 - $infofirst['wday']; $i < (1 - DISPLAY_START_OF_WEEK); $i++) {
                        $monthly[$week][$i] = array();
                        $counts[$week][$i] = -1;
                    }
                    $weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
                }
                // Grab day counts
                $endofmonth = date('t', $time);
                $extitems = $this->getExternalEvents($timefirst, expDateTime::endOfMonthTimestamp($timefirst));
                if (!empty($this->config['aggregate_registrations'])) 
                    $regitems = eventregistrationController::getRegEventsForDates($timefirst, expDateTime::endOfMonthTimestamp($timefirst), $regcolor);
                for ($i = 1; $i <= $endofmonth; $i++) {
                    $start = mktime(0, 0, 0, $info['mon'], $i, $info['year']);
                    if ($i == $nowinfo['mday']) $currentweek = $week;
                    $dates = $ed->find("all", $locsql . " AND (date >= " . expDateTime::startOfDayTimestamp($start) . " AND date <= " . expDateTime::endOfDayTimestamp($start) . ")");
//                    $monthly[$week][$i] = $this->getEventsForDates($dates, true, isset($this->config['only_featured']) ? true : false);
                    $monthly[$week][$i] = $this->event->getEventsForDates($dates, true, isset($this->config['only_featured']) ? true : false);
                    if (!empty($extitems[$start]))
                        $monthly[$week][$i] = array_merge($extitems[$start], $monthly[$week][$i]);
                    if (!empty($regitems[$start]))
                        $monthly[$week][$i] = array_merge($regitems[$start], $monthly[$week][$i]);
                    $monthly[$week][$i] = expSorter::sort(array('array' => $monthly[$week][$i], 'sortby' => 'eventstart', 'order' => 'ASC'));
                    $counts[$week][$i] = count($monthly[$week][$i]);
                    if ($weekday >= (6 + DISPLAY_START_OF_WEEK)) {
                        $week++;
                        $monthly[$week] = array(); // allocate an array for the next week
                        $counts[$week] = array();
                        $weekday = DISPLAY_START_OF_WEEK;
                    } else $weekday++;
                }
                // Grab non-day numbers only (after end of month)
                for ($i = 1; $weekday && $i < (8 + DISPLAY_START_OF_WEEK - $weekday); $i++) {
                    $monthly[$week][$i + $endofmonth] = array();
                    $counts[$week][$i + $endofmonth] = -1;
                }
                $this->params['time'] = $time;
                assign_to_template(array(
                    "currentweek" => $currentweek,
                    "monthly"     => $monthly,
                    "counts"      => $counts,
                    "prevmonth3"  => strtotime('-3 months', $timefirst),
                    "prevmonth2"  => strtotime('-2 months', $timefirst),
                    "prevmonth"   => strtotime('-1 months', $timefirst),
                    "nextmonth"   => strtotime('+1 months', $timefirst),
                    "nextmonth2"  => strtotime('+2 months', $timefirst),
                    "nextmonth3"  => strtotime('+3 months', $timefirst),
                    "now"         => $timefirst,
                    "today"       => expDateTime::startOfDayTimestamp(time()),
                    'params'      => $this->params
                ));
                break;  // end switch $viewtype monthly
            case "administration":  //note a simple list of all upcoming events, except no external nor registration events
                // Check perms and return if cant view
                if (!$user) return;
                $continue = (expPermissions::check("manage", $this->loc) ||
                    expPermissions::check("create", $this->loc) ||
                    expPermissions::check("edit", $this->loc) ||
                    expPermissions::check("delete", $this->loc)
                ) ? 1 : 0;
                $dates = $ed->find("all", $locsql . " AND date >= " . expDateTime::startOfDayTimestamp(time()));
//                $items = $this->getEventsForDates($dates);
                $items = $this->event->getEventsForDates($dates);
                //                if (!$continue) {
                //                    foreach ($items as $i) {
                //                        $iloc = expCore::makeLocation($this->loc->mod,$this->loc->src,$i->id);
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
                //                    $thisloc = expCore::makeLocation($this->loc->mod,$this->loc->src,$items[$i]->id);
                //    //				if ($user && $items[$i]->poster == $user->id) $canviewapproval = 1;
                //                    $items[$i]->permissions = array(
                //                        "manage"=>(expPermissions::check("manage",$thisloc) || expPermissions::check("manage",$this->loc)),
                //                        "edit"=>(expPermissions::check("edit",$thisloc) || expPermissions::check("edit",$this->loc)),
                //                        "delete"=>(expPermissions::check("delete",$thisloc) || expPermissions::check("delete",$this->loc))
                //                    );
                //                }
                $items = expSorter::sort(array('array' => $items, 'sortby' => 'eventstart', 'order' => 'ASC'));
                assign_to_template(array(
                    'items' => $items,
                ));
                break;  // end switch $viewtype administration
            case "default":  //note a simple list of events based on $viewrange
            default;
                //                $items = null;
                //                $dates = null;
                $day = expDateTime::startOfDayTimestamp(time());
                $sort_asc = true; // For the getEventsForDates call
                //                $moreevents = false;
                switch ($viewrange) {
                    case "upcoming":  // events in the future
                        if (!empty($this->config['enable_ical']) && !empty($this->config['rss_limit']) && $this->config['rss_limit'] > 0) {
                            $eventlimit = " AND date <= " . ($day + ($this->config['rss_limit'] * 86400));
                        } else {
                            $eventlimit = "";
                        }
                        $dates = $ed->find("all", $locsql . " AND date >= " . $day . $eventlimit . " ORDER BY date ASC ");
                        $begin = $day;
                        $end = null;
                        //					$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date >= $day");
                        break;
                    case "past":  // events in the past
                        $dates = $ed->find("all", $locsql . " AND date < $day ORDER BY date DESC ");
                        //					$moreevents = count($dates) < $db->countObjects("eventdate",$locsql." AND date < $day");
                        $sort_asc = false;
                        $begin = null;
                        $end = $day;
                        break;
                    case "today":  // events occuring today
                        $dates = $ed->find("all", $locsql . " AND (date >= " . expDateTime::startOfDayTimestamp($day) . " AND date <= " . expDateTime::endOfDayTimestamp($day) . ")");
                        $begin = $day;
                        $end = expDateTime::endOfDayTimestamp($day);
                        break;
                    case "day":  // events for a specific day (same as byday day?)
                        $dates = $ed->find("all", $locsql . " AND (date >= " . expDateTime::startOfDayTimestamp($time) . " AND date <= " . expDateTime::endOfDayTimestamp($time) . ")");
                        $begin = expDateTime::startOfDayTimestamp($time);
                        $end = expDateTime::endOfDayTimestamp($time);
                        break;
                    case "next":  // future events
                        $dates = array($ed->find("all", $locsql . " AND date >= $time"));
                        $begin = expDateTime::startOfDayTimestamp($time);
                        $end = null;
                        break;
                    case "month": // events for a specific month (same as monthly?)
//                        $dates = $ed->find("all", $locsql . " AND (date >= " . expDateTime::startOfMonthTimestamp(time()) . " AND date <= " . expDateTime::endOfMonthTimestamp(time()) . ")");
                        $dates = $ed->find("all", $locsql . " AND (date >= " . expDateTime::startOfMonthTimestamp($time) . " AND date <= " . expDateTime::endOfMonthTimestamp($time) . ")");
                        $begin = expDateTime::startOfMonthTimestamp($time);
                        $end = expDateTime::endOfMonthTimestamp($time);
                        break;
                    case "all":  // all events
                    default;
                        $dates = $ed->find("all", $locsql);
                        $begin = null;
                        $end = null;
                }
//                $items = $this->getEventsForDates($dates, $sort_asc, isset($this->config['only_featured']) ? true : false, true);
                $items = $this->event->getEventsForDates($dates, $sort_asc, isset($this->config['only_featured']) ? true : false, true);
                if ($viewrange != 'past') {
                    $extitems = $this->getExternalEvents($begin, $end);
                    // we need to flatten these down to simple array of events
                    $extitem = array();
                    foreach ($extitems as $days) {
                        foreach ($days as $event) {
                            if (empty($event->eventdate->date) || ($viewrange == 'upcoming' && $event->eventdate->date < time()))
                                break;
                            if (empty($event->eventstart))
                                $event->eventstart = $event->eventdate->date;
                            $extitem[] = $event;
                        }
                    }
                    $items = array_merge($items, $extitem);
                    
                    if (!empty($this->config['aggregate_registrations']))
                        $regitems = eventregistrationController::getRegEventsForDates($begin, $end, $regcolor);
                    // we need to flatten these down to simple array of events
                    $regitem = array();
                    if (!empty($regitems)) foreach ($regitems as $days) {
                        foreach ($days as $value) {
                            $regitem[] = $value;
                        }
                    }
                    $items = array_merge($items, $regitem);

                    // remove today's events that have already ended
                    if ($viewtype == 'default' && $viewrange == 'upcoming') {
                        foreach ($items as $key=>$item) {
                            if (!$item->is_allday && $item->eventend < time()) {
                                //fixme we've left events ending earlier in the day, but already cancelled out tomorrow's event
                                unset($items[$key]);
                            } else {
                                break;  // they are chronological so we can end
                            }
                        }
                    }
                }
                $items = expSorter::sort(array('array' => $items, 'sortby' => 'eventstart', 'order' => 'ASC'));
                // Upcoming events can be configured to show a specific number of events.
                // The previous call gets all events in the future from today
                // If configured, cut the array to the configured number of events
                //			if ($template->viewconfig['num_events']) {
                //				switch ($viewrange) {
                //					case "upcoming":
                //					case "past":
                //						$moreevents = $template->viewconfig['num_events'] < count($items);
                //						break;
                //				}
                //				$items = array_slice($items, 0, $template->viewconfig['num_events']);
                //			}
                //                for ($i = 0; $i < count($items); $i++) {
                //                    $thisloc = expCore::makeLocation($this->loc->mod,$this->loc->src,$items[$i]->id);
                //                    $items[$i]->permissions = array(
                //                        'manage'=>(expPermissions::check('manage',$thisloc) || expPermissions::check('manage',$this->loc)),
                //                        'edit'=>(expPermissions::check('edit',$thisloc) || expPermissions::check('edit',$this->loc)),
                //                        'delete'=>(expPermissions::check('delete',$thisloc) || expPermissions::check('delete',$this->loc))
                //                    );
                //                }
                assign_to_template(array(
                    'items' => $items,
                    "now"   => $day,
                ));
        }
    }

    /**
     * default view for individual item
     */
    function show() {
        expHistory::set('viewable', $this->params);
        if (!empty($this->params['date_id'])) {  // specific event instance
            $eventdate = new eventdate($this->params['date_id']);
            $eventdate->event = new event($eventdate->event_id);
        } else {  // we'll default to the first event of this series
            $event = new event($this->params['id']);
            $eventdate = new eventdate($event->eventdate[0]->id);
        }
        if (empty($eventdate->id))
            redirect_to(array('controller'=>'notfound','action'=>'page_not_found','title'=>'event'));

        if (!empty($eventdate->event->feedback_form) && $eventdate->event->feedback_form != 'Disallow Feedback') {
            assign_to_template(array(
                'feedback_form' => $eventdate->event->feedback_form,
            ));
        }

        assign_to_template(array(
            'event' => $eventdate,
        ));
    }

    function edit() {
        global $template;

        parent::edit();
        $allforms = array();
        $allforms[""] = gt('Disallow Feedback');
        // calculate which event date is the one being edited
        $event_key = 0;
        foreach ($template->tpl->tpl_vars['record']->value->eventdate as $key=>$d) {
       	    if ($d->id == $this->params['date_id']) $event_key = $key;
       	}

        assign_to_template(array(
            'allforms'     => array_merge($allforms, expTemplate::buildNameList("forms", "event/email", "tpl", "[!_]*")),
            'checked_date' => !empty($this->params['date_id']) ? $this->params['date_id'] : null,
            'event_key'    => $event_key,
        ));
    }

    /**
     * Delete a recurring event by asking for which event dates to delete
     *
     */
    function delete_recurring() {
        $item = $this->event->find('first', 'id=' . $this->params['id']);
        if ($item->is_recurring == 1) { // need to give user options
            expHistory::set('editable', $this->params);
            assign_to_template(array(
                'checked_date' => $this->params['date_id'],
                'event'        => $item,
            ));
        } else { // Process a regular delete
            $item->delete();
        }
    }

    /**
     * Delete selected event dates for a recurring event and event if all event dates deleted
     *
     */
    function delete_selected() {
        $item = $this->event->find('first', 'id=' . $this->params['id']);
        if ($item && $item->is_recurring == 1) {
            $event_remaining = false;
            $eventdates = $item->eventdate[0]->find('all', 'event_id=' . $item->id);
            foreach ($eventdates as $ed) {
                if (array_key_exists($ed->id, $this->params['dates'])) {
                    $ed->delete();
                } else {
                    $event_remaining = true;
                }
            }
            if (!$event_remaining) {
                $item->delete(); // model will also ensure we delete all event dates
            }
            expHistory::back();
        } else {
            notfoundController::handle_not_found();
        }
    }

    function delete_all_past() {
        $locsql = $this->aggregateWhereClause();
        $ed = new eventdate();
        $dates = $ed->find("all", $locsql . " AND date < " . strtotime('-1 months', time()));
        foreach ($dates as $date) {
            $date->delete(); // event automatically deleted if all assoc eventdates are deleted
        }
        expHistory::back();
    }

    /**
   	 * get the metainfo for this module
   	 * @return array
   	 */
   	function metainfo() {
       global $router;

        $action = $router->params['action'];
        $metainfo = array('title' => '', 'keywords' => '', 'description' => '', 'canonical'=> '', 'noindex' => false, 'nofollow' => false);
        // look for event date_id which expController::metainfo won't detect
//        if (!empty($router->params['action']) && $router->params['action'] == 'show' && !isset($router->params['id']) && isset($router->params['date_id'])) {
        switch ($action) {
            case 'show':
                if (!isset($router->params['id']) && isset($router->params['date_id'])) {
                    // look up the record.
                    $object = new eventdate((int)$router->params['date_id']);
                    // set the meta info
                    if (!empty($object)) {
                        if (!empty($object->event->body)) {
                            $desc = str_replace('"',"'",expString::summarize($object->event->body,'html','para'));
                        } else {
                            $desc = SITE_DESCRIPTION;
                        }
                        if (!empty($object->expTag)) {
                            $keyw = '';
                            foreach ($object->expTag as $tag) {
                                if (!empty($keyw)) $keyw .= ', ';
                                $keyw .= $tag->title;
                            }
                        } else {
                            $keyw = SITE_KEYWORDS;
                        }
                        $metainfo['title'] = empty($object->event->meta_title) ? $object->event->title : $object->event->meta_title;
                        $metainfo['keywords'] = empty($object->event->meta_keywords) ? $keyw : $object->event->meta_keywords;
                        $metainfo['description'] = empty($object->event->meta_description) ? $desc : $object->event->meta_description;
                        $metainfo['canonical'] = empty($object->event->canonical) ? $router->plainPath() : $object->event->canonical;
                        $metainfo['noindex'] = empty($object->event->meta_noindex) ? false : $object->event->meta_noindex;
                        $metainfo['nofollow'] = empty($object->event->meta_nofollow) ? false : $object->event->meta_nofollow;
                        return $metainfo;
                        break;
                    }
                }
            default:
                return parent::metainfo();
        }
    }

    /**
     * function to build a string to pull in all events within requested date range
     */
    function build_daterange_sql($timestamp, $endtimestamp=null, $field='date', $multiday=false) {
        if (empty($endtimestamp)) {
            $date_sql = "((".$field." >= " . expDateTime::startOfDayTimestamp($timestamp) . " AND ".$field." <= " . expDateTime::endOfDayTimestamp($timestamp) . ")";
        } else {
            $date_sql = "((".$field." >= " . expDateTime::startOfDayTimestamp($timestamp) . " AND ".$field." <= " . expDateTime::endOfDayTimestamp($endtimestamp) . ")";
        }
        if ($multiday)
            $date_sql .= " OR (" . expDateTime::startOfDayTimestamp($timestamp) . " BETWEEN ".$field." AND dateFinished)";
        $date_sql .= ")";
        return $date_sql;
    }

    function send_feedback() {
        $success = false;
        if (isset($this->params['id'])) {
            $ed = new eventdate($this->params['id']);
//            $email_addrs = array();
            if ($ed->event->feedback_email != '') {
                $msgtemplate = expTemplate::get_template_for_action($this, 'email/_' . $this->params['formname'], $this->loc);
                $msgtemplate->assign('params', $this->params);
                $msgtemplate->assign('event', $ed);
                $email_addrs = explode(',', $ed->event->feedback_email);
                //This is an easy way to remove duplicates
                $email_addrs = array_flip(array_flip($email_addrs));
                $email_addrs = array_map('trim', $email_addrs);
                $mail = new expMail();
                $success += $mail->quickSend(array(
                    "text_message" => $msgtemplate->render(),
                    'to'           => $email_addrs,
                    'from'         => !empty($this->params['email']) ? $this->params['email'] : trim(SMTP_FROMADDRESS),
                    'subject'      => $this->params['subject'],
                ));
            }
        }

        if ($success) {
            flashAndFlow('message', gt('Your feedback was successfully sent.'));
        } else {
            flashAndFlow('error', gt('We could not send your feedback.  Please contact your administrator.'));
        }
    }

    function ical() {
        if (isset($this->params['date_id']) || isset($this->params['title']) || isset($this->params['src'])) {
            $cfg = new expConfig();
            $configs = $cfg->find('all', "location_data LIKE '%event%'"); // get all event module configs
            foreach ($configs as $config) {
                $loc = expUnserialize($config->location_data);
                if (!empty($this->params['title'])) {
                    if ($this->params['title'] == $config->config['feed_sef_url']) {
                        $this->config = $config->config;
                        break;
                    }
                } elseif (!empty($this->params['src'])) {
                    if ($this->params['src'] == $loc->src) {
                        $this->config = $config->config;
                        break;
                    }
                }
            }
            $this->loc = $loc;

            if ($this->config['enable_ical']) {
                $ed = new eventdate();
                if (isset($this->params['date_id'])) { // get single specific event only
//                    $dates = array($db->selectObject("eventdate","id=".$this->params['date_id']));
                    $dates = $ed->find('first', "id=" . $this->params['date_id']);
                    $Filename = "Event-" . $this->params['date_id'];
                } else {
                    $locsql = $this->aggregateWhereClause();

                    $day = expDateTime::startOfDayTimestamp(time());
                    if (!empty($this->config['enable_ical']) && isset($this->config['rss_limit']) && ($this->config['rss_limit'] > 0)) {
                        $rsslimit = " AND date <= " . ($day + ($this->config['rss_limit'] * 86400));
                    } else {
                        $rsslimit = "";
                    }

                    if (isset($this->params['time'])) {
                        $time = $this->params['time']; // get current month's events
//                        $dates = $db->selectObjects("eventdate",$locsql." AND (date >= ".expDateTime::startOfMonthTimestamp($time)." AND date <= ".expDateTime::endOfMonthTimestamp($time).")");
                        $dates = $ed->find('all', $locsql . " AND (date >= " . expDateTime::startOfMonthTimestamp($time) . " AND date <= " . expDateTime::endOfMonthTimestamp($time) . ")");
                    } else {
                        $time = date('U', strtotime("midnight -1 month", time())); // previous month also
//                        $dates = $db->selectObjects("eventdate",$locsql." AND date >= ".expDateTime::startOfDayTimestamp($time).$rsslimit);
                        $dates = $ed->find('all', $locsql . " AND date >= " . expDateTime::startOfDayTimestamp($time) . $rsslimit);
                    }
                    //			$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
                    $title = $this->config['feed_title'];
                    $Filename = preg_replace('/\s+/', '', $title); // without whitespace
                }

                if (!function_exists("quoted_printable_encode")) { // function added in php v5.3.0
                    function quoted_printable_encode($input, $line_max = 75) {
                        $hex = array('0', '1', '2', '3', '4', '5', '6', '7',
                            '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
                        $lines = preg_split("/(?:\r\n|\r|\n)/", $input);
                        $linebreak = "=0D=0A=\r\n";
                        /* the linebreak also counts as characters in the mime_qp_long_line
                         * rule of spam-assassin */
                        $line_max = $line_max - strlen($linebreak);
                        $escape = "=";
                        $output = "";
                        $cur_conv_line = "";
                        $length = 0;
                        $whitespace_pos = 0;
                        $addtl_chars = 0;

                        // iterate lines
                        for ($j = 0, $jMax = count($lines); $j < $jMax; $j++) {
                            $line = $lines[$j];
                            $linlen = strlen($line);

                            // iterate chars
                            for ($i = 0; $i < $linlen; $i++) {
                                $c = substr($line, $i, 1);
                                $dec = ord($c);

                                $length++;

                                if ($dec == 32) {
                                    // space occurring at end of line, need to encode
                                    if (($i == ($linlen - 1))) {
                                        $c = "=20";
                                        $length += 2;
                                    }

                                    $addtl_chars = 0;
                                    $whitespace_pos = $i;
                                } elseif (($dec == 61) || ($dec < 32) || ($dec > 126)) {
                                    $h2 = floor($dec / 16);
                                    $h1 = floor($dec % 16);
                                    $c = $escape . $hex["$h2"] . $hex["$h1"];
                                    $length += 2;
                                    $addtl_chars += 2;
                                }

                                // length for wordwrap exceeded, get a newline into the text
                                if ($length >= $line_max) {
                                    $cur_conv_line .= $c;

                                    // read only up to the whitespace for the current line
                                    $whitesp_diff = $i - $whitespace_pos + $addtl_chars;

                                    /* the text after the whitespace will have to be read
                                     * again ( + any additional characters that came into
                                     * existence as a result of the encoding process after the whitespace)
                                     *
                                     * Also, do not start at 0, if there was *no* whitespace in
                                     * the whole line */
                                    if (($i + $addtl_chars) > $whitesp_diff) {
                                        $output .= substr($cur_conv_line, 0, (strlen($cur_conv_line) -
                                            $whitesp_diff)) . $linebreak;
                                        $i = $i - $whitesp_diff + $addtl_chars;
                                    } else {
                                        $output .= $cur_conv_line . $linebreak;
                                    }

                                    $cur_conv_line = "";
                                    $length = 0;
                                    $whitespace_pos = 0;
                                } else {
                                    // length for wordwrap not reached, continue reading
                                    $cur_conv_line .= $c;
                                }
                            } // end of for

                            $length = 0;
                            $whitespace_pos = 0;
                            $output .= $cur_conv_line;
                            $cur_conv_line = "";

                            if ($j <= count($lines) - 1) {
                                $output .= $linebreak;
                            }
                        } // end for

                        return trim($output);
                    } // end quoted_printable_encode
                }

                $tz = DISPLAY_DEFAULT_TIMEZONE;
                $msg = "BEGIN:VCALENDAR\n";
                $msg .= "VERSION:2.0\n"; // version for iCalendar files vs vCalendar files
                $msg .= "CALSCALE:GREGORIAN\n";
                $msg .= "METHOD: PUBLISH\n";
                $msg .= "PRODID:<-//ExponentCMS//EN>\n";
                if (isset($this->config['rss_cachetime']) && ($this->config['rss_cachetime'] > 0)) {
                    $msg .= "X-PUBLISHED-TTL:PT" . $this->config['rss_cachetime'] . "M\n";
                }
                $msg .= "X-WR-CALNAME:$Filename\n";

//                $items = $this->getEventsForDates($dates);
                $items = $this->event->getEventsForDates($dates);

                for ($i = 0, $iMax = count($items); $i < $iMax; $i++) {

                    // Convert events stored in local time to GMT
                    $eventstart = new DateTime(date('r', $items[$i]->eventstart), new DateTimeZone($tz));
                    $eventstart->setTimezone(new DateTimeZone('GMT'));
                    $eventend = new DateTime(date('r', $items[$i]->eventend), new DateTimeZone($tz));
                    $eventend->setTimezone(new DateTimeZone('GMT'));
                    if ($items[$i]->is_allday) {
                        $dtstart = "DTSTART;VALUE=DATE:" . date("Ymd", $items[$i]->eventstart) . "\n";
                        $dtend = "DTEND;VALUE=DATE:" . date("Ymd", strtotime("midnight +1 day", $items[$i]->eventstart)) . "\n";
                    } else {
                        $dtstart = "DTSTART;VALUE=DATE-TIME:" . $eventstart->format("Ymd\THi00") . "Z\n";
                        if ($items[$i]->eventend) {
                            $dtend = "DTEND;VALUE=DATE-TIME:" . $eventend->format("Ymd\THi00") . "Z\n";
                        } else {
                            $dtend = "DTEND;VALUE=DATE-TIME:" . $eventstart->format("Ymd\THi00") . "Z\n";
                        }
                    }

                    $body = trim(strip_tags(str_replace(array("<br />", "<br>", "br/>", "</p>"), "\n", $items[$i]->body)));
                    if ($items[$i]->is_cancelled) $body = gt('This Event Has Been Cancelled') . ' - ' . $body;
                    $body = str_replace(array("\r"), "", $body);
                    $body = str_replace(array("&#160;"), " ", $body);
                    $body = expString::convertSmartQuotes($body);
                    if (!isset($this->params['style'])) {
                        // it's going to Outlook so remove all formatting from body text
                        $body = quoted_printable_encode($body);
                    } elseif ($this->params['style'] == "g") {
                        // It's going to Google (doesn't like quoted-printable, but likes html breaks)
                        $body = str_replace(array("\n"), "<br />", $body);
                    } else {
                        // It's going elsewhere (doesn't like quoted-printable)
                        $body = str_replace(array("\n"), " -- ", $body);
                    }
                    $title = $items[$i]->title;

                    $msg .= "BEGIN:VEVENT\n";
                    $msg .= $dtstart . $dtend;
                    $msg .= "UID:" . $items[$i]->date_id . "\n";
                    $msg .= "DTSTAMP:" . date("Ymd\THis", time()) . "Z\n";
                    if ($title) {
                        $msg .= "SUMMARY:$title\n";
                    }
                    if ($body) {
                        $msg .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:" . $body . "\n";
                    }
                    //	if($link_url) { $msg .= "URL: $link_url\n";}
                    if (!empty($this->config['usecategories'])) {
                        if (!empty($items[$i]->expCat[0]->title)) {
                            $msg .= "CATEGORIES:".$items[$i]->expCat[0]->title."\n";
                        } else {
                            $msg .= "CATEGORIES:".$this->config['uncat']."\n";
                        }
                    }
                    $msg .= "END:VEVENT\n";
                }
                $msg .= "END:VCALENDAR";

                // Kick it out as a file download
                ob_end_clean();

                //	$mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octet-stream;' : "text/x-vCalendar";
                //	$mime_type = "text/x-vCalendar";
                $mime_type = 'text/Calendar';
                header('Content-Type: ' . $mime_type);
                header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Content-length: ' . strlen($msg));
                header('Content-Transfer-Encoding: binary');
                header('Content-Encoding:');
                //	header("Content-Disposition: inline; filename=".$Filename.".ics");
                header('Content-Disposition: attachment; filename="' . $Filename . '.ics"');
                // IE need specific headers
                //	if (EXPONENT_USER_BROWSER == 'IE') {
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: public');
                header('Vary: User-Agent');
                //	} else {
                header('Pragma: no-cache');
                //	}
                echo $msg;
                exit();
            } else {
                notfoundController::handle_not_found();
            }
        } else {
            notfoundController::handle_not_found();
        }
    }

    function send_reminders() {
        if (isset($this->params['title']) || isset($this->params['src'])) {
            $cfg = new expConfig();
            $configs = $cfg->find('all', "location_data LIKE '%event%'"); // get all event module configs
            foreach ($configs as $config) {
                $loc = expUnserialize($config->location_data);
                if (!empty($this->params['title'])) {
                    if ($this->params['title'] == $config->config['feed_sef_url']) {
                        $this->config = $config->config;
                        break;
                    }
                } elseif (!empty($this->params['src'])) {
                    if ($this->params['src'] == $loc->src) {
                        $this->config = $config->config;
                        break;
                    }
                }
            }

            if (empty($this->config['reminder_active'])) {
                notfoundController::handle_not_found();
                return;
            }
            if (!empty($this->config['reminder_code']) && (empty($this->params['code']) || ($this->params['code'] != $this->config['reminder_code']))) {
                notfoundController::handle_not_authorized();
                return;
            }

            $this->loc = $loc;
            $locsql = $this->aggregateWhereClause();

            $view = (isset($this->params['view']) ? $this->params['view'] : '');
            if ($view == "") {
                $view = "send_reminders"; // default reminder view
            }

//            $template = expTemplate::get_template_for_action($this, $view, $this->loc);
            global $template;

            $title = $this->config['feed_title'];
            $template->assign('moduletitle', $title);

            $time = (isset($this->params['time']) ? $this->params['time'] : time());
            $time = (int)$time;

            $template->assign("time", $time);

            $startperiod = expDateTime::startOfDayTimestamp($time);
            if (!empty($this->params['days'])) {
                $totaldays = $this->params['days'];
            } else {
                $totaldays = 7; // default 7 days of events
            }

            $count = 0;
            $info = getdate($startperiod);
            for ($i = 0; $i < $totaldays; $i++) {
                $start = mktime(0, 0, 0, $info['mon'], $info['mday'] + $i, $info['year']);
                $ed = new eventdate();
                $edates = $ed->find('all', $locsql . " AND (date >= " . expDateTime::startOfDayTimestamp($start) . " AND date <= " . expDateTime::endOfDayTimestamp($start) . ")");
                $days[$start] = array();
//                $days[$start] = $this->getEventsForDates($edates);
                $days[$start] = $this->event->getEventsForDates($edates);
                for ($j = 0, $jMax = count($days[$start]); $j < $jMax; $j++) {
                    $thisloc = expCore::makeLocation($loc->mod, $loc->src, $days[$start][$j]->id);
                    $days[$start][$j]->permissions = array(
                        "manage" => (expPermissions::check("manage", $thisloc) || expPermissions::check("manage", $loc)),
                        "edit"   => (expPermissions::check("edit", $thisloc) || expPermissions::check("edit", $loc)),
                        "delete" => (expPermissions::check("delete", $thisloc) || expPermissions::check("delete", $loc))
                    );
                }
                $counts[$start] = count($days[$start]);
                $count += count($days[$start]);
                $days[$start] = expSorter::sort(array('array' => $days[$start], 'sortby' => 'eventstart', 'order' => 'ASC'));
            }
            $template->assign("days", $days);
            $template->assign("counts", $counts);
            $template->assign("start", $startperiod);
            $template->assign("totaldays", $totaldays);

            if ($count == 0) {
                flash('error',gt('No Events to Send!'));
                echo show_msg_queue('error');
                return;
            }

            if (bs3())
                $css = file_get_contents(BASE . "external/bootstrap3/css/bootstrap.css");
            elseif (bs2())
                $css = file_get_contents(BASE . "external/bootstrap/css/bootstrap.css");
            else
                $css = file_get_contents(BASE . "framework/modules/events/assets/css/calendar.css");
            $template->assign("css", $css);
            $template->assign("config", $this->config);
            $template->assign("src", $loc->src);

            // format and send email
            $subject = $this->config['email_title_reminder'] . " - $title";
            $from_addr = $this->config['email_address_reminder'];
            $headers = array(
                "From"     => $from = $this->config['email_from_reminder'],
                "Reply-to" => $reply = $this->config['email_reply_reminder']
            );

            // set up the html message
            $template->assign("showdetail", !empty($this->config['email_showdetail']));
            $htmlmsg = $template->render();

            // now the same thing for the text message
            $msg = preg_replace('/(<script[^>]*>.+?<\/script>|<style[^>]*>.+?<\/style>)/s', '', $htmlmsg); // remove any script or style blocks
            $msg = trim(strip_tags(str_replace(array("<br />", "<br>", "br/>"), "\n", $msg)));

            // Saved.  do notifs
            $emails = array();
            if (!empty($this->config['user_list'])) foreach ($this->config['user_list'] as $c) {
                $u = user::getUserById($c);
                $emails[$u->email] = trim(user::getUserAttribution($u->id));
            }
            if (!empty($this->config['group_list'])) foreach ($this->config['group_list'] as $c) {
                $grpusers = group::getUsersInGroup($c);
                foreach ($grpusers as $u) {
                    $emails[$u->email] = trim(user::getUserAttribution($u->id));
                }
            }
            if (!empty($this->config['address_list'])) foreach ($this->config['address_list'] as $c) {
                $emails[] = $c;
            }
            if (empty($emails)) {
                flash('error',gt('No One to Send Reminders to!'));
                echo show_msg_queue('error');
                return;
            }

            $emails = array_flip(array_flip($emails));
            $emails = array_map('trim', $emails);
//            $headers = array(
//                "MIME-Version" => "1.0",
//                "Content-type" => "text/html; charset=" . LANG_CHARSET
//            );
            $mail = new expMail();
            $mail->quickSend(array(
//                'headers'      => $headers,
                'html_message' => $htmlmsg,
                "text_message" => $msg,
                'to'           => $emails,
                'from'         => array(trim($this->config['email_address_reminder']) => $this->config['email_from_reminder']),
                'subject'      => $subject,
            ));

            flash('message',gt('The following reminder was sent via email'));
            echo show_msg_queue();
//            echo($htmlmsg);
        } else {
            flash('error',gt('No Calendar Selected!'));
            echo show_msg_queue('error');
        }
    }

    /** @deprecated moved to event model
     * @param $edates
     * @param bool $sort_asc
     * @param bool $featuredonly
     * @param bool $condense
     * @return array
     */
    function getEventsForDates($edates, $sort_asc = true, $featuredonly = false, $condense = false) {
        global $eventid;

        $events = array();
        $featuresql = "";
        if ($featuredonly) $featuresql = " AND is_featured=1";
        foreach ($edates as $edate) {
            $evs = $this->event->find('all', "id=" . $edate->event_id . $featuresql);
            foreach ($evs as $key=>$event) {
                if ($condense) {
                    $eventid = $event->id;
                    $multiday_event = array_filter($events, create_function('$event', 'global $eventid; return $event->id === $eventid;'));
                    if (!empty($multiday_event)) {
                        unset($evs[$key]);
                        continue;
                    }
                }
                $evs[$key]->eventstart += $edate->date;
                $evs[$key]->eventend += $edate->date;
                $evs[$key]->date_id = $edate->id;
                if (!empty($event->expCat)) {
                    $catcolor = empty($event->expCat[0]->color) ? null : trim($event->expCat[0]->color);
//                    if (substr($catcolor,0,1)=='#') $catcolor = '" style="color:'.$catcolor.';';
                    $evs[$key]->color = $catcolor;
                }
            }
            if (count($events) < 500) {  // magic number to not crash loop?
                $events = array_merge($events, $evs);
            } else {
//                $evs[$key]->title = gt('Too many events to list').', '.(count($edates)-count($events)).' '.gt('not displayed!');
//                $events = array_merge($events, $evs);
                flash('notice',gt('Too many events to list').', '.(count($edates)-count($events)).' '.gt('not displayed!'));
                break; // keep from breaking system by too much data
            }
        }
        $events = expSorter::sort(array('array' => $events, 'sortby' => 'eventstart', 'order' => $sort_asc ? 'ASC' : 'DESC'));
        return $events;
    }

    function getExternalEvents($startdate, $enddate, $multiday = false) {
        global $db;

        $extevents = array();
        $dy = 0;  // index of events array
        if (!empty($this->config['pull_gcal'])) foreach ($this->config['pull_gcal'] as $key=>$extgcalurl) {
//            $dy = count($extevents);  // index of events array
            $cache_hit = false;
            $gcal_cname = str_replace(array("/",":","&","?"),"_",$extgcalurl);
            $cache_fname = BASE.'tmp/cache/'.$gcal_cname.".cache";
            if (file_exists($cache_fname)) {
                $cache = unserialize(file_get_contents($cache_fname));
                if ($startdate >= $cache['start_date'] || $startdate >= $cache['first_date']) {
                    $events = $db->selectObjects('event_cache','feed="'.$extgcalurl.'" AND ' . self::build_daterange_sql($startdate,$enddate,'eventdate',true));
                    foreach ($events as $event) {
                        if ($multiday) {
                            $extevents[$event->eventdate][$dy] = $event;
                            $extevents[$event->eventdate][$dy]->feedkey = $key;
                            $extevents[$event->eventdate][$dy]->location_data = 'gcalevent' . $key;
                            $extevents[$event->eventdate][$dy]->color = !empty($this->config['pull_gcal_color'][$key]) ? $this->config['pull_gcal_color'][$key] : null;
                            if ($event->is_allday) {
                                $extevents[$event->eventdate][$dy]->eventstart = $event->eventdate;
                            }
                            $dy++;
                        } else {
                            $endit = !empty($event->dateFinished) ? $event->dateFinished : $event->eventdate;
//                            for ($i = $startdate; $i < $enddate; $i += 86400) {
                            for ($i = $event->eventdate; $i <= $endit; $i += 86400) {
                                if ((!empty($event->dateFinished) && $i > $event->dateFinished) || (empty($event->dateFinished) && $i > $event->eventdate)) {
                                    break;
                                } else {
                                    $extevents[$i][$dy] = clone($event);
                                    $extevents[$i][$dy]->eventdate = (int)$i;
                                    $extevents[$i][$dy]->eventstart = ($event->eventstart - $event->eventdate);
                                    $extevents[$i][$dy]->eventend = ($event->eventend - (!empty($event->dateFinished)?$event->dateFinished:$event->eventdate));
                                    $extevents[$i][$dy]->eventstart = ($extevents[$i][$dy]->eventstart) + $i;
                                    $extevents[$i][$dy]->eventend = ($extevents[$i][$dy]->eventend) + $i;
                                    $extevents[$i][$dy]->feedkey = $key;
                                    $extevents[$i][$dy]->location_data = 'gcalevent' . $key;
                                    $extevents[$i][$dy]->color = !empty($this->config['pull_gcal_color'][$key]) ? $this->config['pull_gcal_color'][$key] : null;
                                    $dy++;
                                }
                            }
                        }
                    }
                    $cache_hit = true;
                }
            }
            if (!$cache_hit) { // pull in the external events
                foreach ($this->get_gcal_events($extgcalurl, $startdate, $enddate, $dy, $key, $multiday) as $date=>$events) {
                    foreach ($events as $event) {
                        $extevents[$date][] = $event;
                    }
                }
            }
        }
        if (!empty($this->config['pull_ical'])) foreach ($this->config['pull_ical'] as $key=>$exticalurl) {
//            $dy = count($extevents);  // index of events array
            $cache_hit = false;
            $ical_cname = str_replace(array("/",":","&","?"),"_",$exticalurl);
            $cache_fname = BASE.'tmp/cache/'.$ical_cname.".cache";
            if (file_exists($cache_fname)) {
                $cache = unserialize(file_get_contents($cache_fname));
                if ($startdate >= $cache['start_date'] || $startdate >= $cache['first_date']) {
                    $events = $db->selectObjects('event_cache','feed="'.$exticalurl.'" AND ' . self::build_daterange_sql($startdate,$enddate,'eventdate',true));
                    foreach ($events as $event) {
                        $extevents[$event->eventdate][$dy] = $event;
                        $extevents[$event->eventdate][$dy]->location_data = 'icalevent' . $key;
                        $extevents[$event->eventdate][$dy]->color = !empty($this->config['pull_ical_color'][$key]) ? $this->config['pull_ical_color'][$key] : null;
                        $dy++;
                    }
                    $cache_hit = true;
                }
            }
            if (!$cache_hit) {  // pull in the external events
                foreach ($this->get_ical_events($exticalurl, $startdate, $enddate, $dy, $key, $multiday) as $date=>$events) {
                    foreach ($events as $event) {
                        $extevents[$date][] = $event;
                    }
                }
            }
        }
        return $extevents;
    }

    public function get_gcal_events($extgcalurl, $startdate, $enddate=null, &$dy=0, $key=0, $multiday=false) {
        $extevents = array();
        if (!empty($startdate)) $begin = date("Y-m-d\Th:i:sP", expDateTime::startOfDayTimestamp($startdate));
        if (!empty($enddate)) $end = date("Y-m-d\Th:i:sP", expDateTime::endOfDayTimestamp($enddate));
            else $end = date("Y-m-d\Th:i:sP", (expDateTime::endOfDayTimestamp($startdate + ((3600*24)*30))));

        if (substr($extgcalurl, -5) == 'basic') {
            $extgcalurl = substr($extgcalurl, 0, - 5) . 'full';
        }
        $feed = $extgcalurl . "?orderby=starttime&singleevents=true";
        if (!empty($startdate)) $feed .= "&start-min=" . $begin;
        if (!empty($enddate)) $feed .= "&start-max=" . $end;

        // XML method
//            $s = simplexml_load_file($feed);
//            foreach ($s->entry as $item) {
//                $gd = $item->children('http://schemas.google.com/g/2005');
//                if (!empty($gd->when)) {
//                   $dtstart = $gd->when->attributes()->startTime;
//                } elseif (!empty($gd->recurrence)){
//                   $dtstart = $gd->recurrence->when->attributes()->startTime;
//                } else {
//                    $dtstart = $item->attributes()->When;
//                }
//                //FIXME must convert $dtstart timezone
//                $eventdate = expDateTime::startOfDayTimestamp(strtotime($dtstart));
//                $ourtzoffsets = (int)(date('O',$eventdate)) * 36;
//                $theirtzoffset = -((int)(substr($dtstart,-5,2)) * 3600);
//                $tzoffset = $ourtzoffsets - $theirtzoffset;
//                $extevents[$eventdate][$dy] = new stdClass();
//                $extevents[$eventdate][$dy]->eventdate = $eventdate;
//                $extevents[$eventdate][$dy]->eventstart += strtotime($dtstart) + $tzoffset;
//                if (!empty($gd->when)) {
//                    $dtend = $gd->when->attributes()->endTime;
//                } elseif (!empty($gd->recurrence)) {
//                    $dtend = $gd->recurrence->when->attributes()->endTime;
//                }
//                //FIXME must convert $dtend timezone
//                if (!empty($dtend)) $extevents[$eventdate][$dy]->eventend += strtotime($dtend) + $tzoffset;
//                // dtstart required, one occurrence, (orig. start date)
//                $extevents[$eventdate][$dy]->title = $item->title;
//                $extevents[$eventdate][$dy]->body = $item->content;
        // End XML method

        // DOM method
        $doc = new DOMDocument();
        $doc->load($feed);
        $entries = $doc->getElementsByTagName("entry");
        foreach ($entries as $item) {
            $times = $item->getElementsByTagName("when");
            $dtstart = $times->item(0)->getAttributeNode("startTime")->value;
            $eventdate = expDateTime::startOfDayTimestamp(strtotime($dtstart));
            $extevents[$eventdate][$dy] = new stdClass();
            $extevents[$eventdate][$dy]->eventdate = $eventdate;
            $dtend = @$times->item(0)->getAttributeNode("endTime")->value;
            $ourtzoffsets = (int)date('O',$eventdate) * 36;
            $theirtzoffset = -((int)substr($dtstart,-5,2) * 3600);
            $tzoffset = $ourtzoffsets - $theirtzoffset;
            if (strlen($dtstart) > 10) {
                $extevents[$eventdate][$dy]->eventstart = ((int)substr($dtstart, 11, 2) * 3600) + ((int)substr($dtstart, 14, 2) * 60) + $tzoffset;
//                    if (date("I", $eventdate)) $extevents[$eventdate][$dy]->eventstart += 3600;
                $extevents[$eventdate][$dy]->eventend = ((int)substr($dtend, 11, 2) * 3600) + ((int)substr($dtend, 14, 2) * 60) + $tzoffset;
//                    if (date("I", $eventdate)) $extevents[$eventdate][$dy]->eventend += 3600;
            } else {
                $extevents[$eventdate][$dy]->eventstart = null;
                $extevents[$eventdate][$dy]->is_allday = 1;
            }
            $extevents[$eventdate][$dy]->eventstart += $eventdate;
            $extevents[$eventdate][$dy]->eventend += $eventdate;
            if (empty($dtend)) $extevents[$eventdate][$dy]->eventend = $extevents[$eventdate][$dy]->eventstart;

            $titles = $item->getElementsByTagName("title");
            $extevents[$eventdate][$dy]->title = $titles->item(0)->nodeValue;
            $contents = $item->getElementsByTagName("content");
            $extevents[$eventdate][$dy]->body = $contents->item(0)->nodeValue;
            // End DOM method

//                    $extevents[$eventdate][$dy]->location_data = serialize(expCore::makeLocation('extevent',$extcal->id));
            $extevents[$eventdate][$dy]->location_data = 'gcalevent' . $key;
            $extevents[$eventdate][$dy]->color = !empty($this->config['pull_gcal_color'][$key]) ? $this->config['pull_gcal_color'][$key] : null;
            $dy++;
        }
        return $extevents;
    }

    public function get_ical_events($exticalurl, $startdate=null, $enddate=null, &$dy=0, $key=0, $multiday=false) {
        $extevents = array();
//            require_once BASE . 'external/iCalcreator.class.php';
        require_once BASE . 'external/iCalcreator-2.22/iCalcreator.php';
        $v = new vcalendar(); // initiate new CALENDAR
        if (stripos($exticalurl, 'http') === 0) {
            $v->setConfig('url', $exticalurl);
        } else {
            $v->setConfig('directory', dirname($exticalurl));
            $v->setConfig('filename', basename($exticalurl));
        }
        $v->parse();
        if ($startdate === null) {
            $startYear = false;
            $startMonth = false;
            $startDay = false;
        } else {
            $startYear = date('Y', $startdate);
            $startMonth = date('n', $startdate);
            $startDay = date('j', $startdate);
        }
        if ($enddate === null) {
            $endYear = $startYear+1;
            $endMonth = $startMonth;
            $endDay = $startDay;
        } else {
            $endYear = date('Y', $enddate);
            $endMonth = date('n', $enddate);
            $endDay = date('j', $enddate);
        }
        // get all events within period split out recurring events as single events per each day
        $eventArray = $v->selectComponents($startYear, $startMonth, $startDay, $endYear, $endMonth, $endDay, 'vevent');
        // Set the timezone to GMT
        @date_default_timezone_set('GMT');
        $tzarray = getTimezonesAsDateArrays($v);
        // Set the default timezone
        @date_default_timezone_set(DISPLAY_DEFAULT_TIMEZONE);
        if (!empty($eventArray)) foreach ($eventArray as $year => $yearArray) {
            if (!empty($yearArray)) foreach ($yearArray as $month => $monthArray) {
                if (!empty($monthArray)) foreach ($monthArray as $day => $dailyEventsArray) {
                    if (!empty($dailyEventsArray)) foreach ($dailyEventsArray as $vevent) {
                        // process each event
                        $yesterday = false;
                        $currdate = $vevent->getProperty('x-current-dtstart');
                        $thisday = explode('-', $currdate[1]);
                        $thisday2 = substr($thisday[2], 0, 2);
                        // if member of a recurrence set,
                        // returns array( 'x-current-dtstart', <DATE>)
                        // <DATE> = (string) date("Y-m-d [H:i:s][timezone/UTC offset]")
                        $dtstart = $vevent->getProperty('dtstart', false, true);
                        $dtend = $vevent->getProperty('dtend', false, true);
                        if (empty($dtend))
                            $dtend = $dtstart;

                        // calculate the cumulative timezone offset in seconds to convert to local/system time
                        $tzoffsets = array();
                        $date_tzoffset = 0;
                        if (!empty($tzarray)) {
//                                $ourtzoffsets = -(iCalUtilityFunctions::_tz2offset(date('O',time())));
                            $ourtzoffsets = -(iCalUtilityFunctions::_tz2offset(date('O',self::_date2timestamp($dtstart['value']))));
                            // Set the timezone to GMT
                            @date_default_timezone_set('GMT');
                            if (!empty($dtstart['params']['TZID'])) $tzoffsets = getTzOffsetForDate($tzarray, $dtstart['params']['TZID'], $dtstart['value']);
                            // Set the default timezone
                            @date_default_timezone_set(DISPLAY_DEFAULT_TIMEZONE);
                            if (isset($tzoffsets['offsetSec'])) $date_tzoffset = $ourtzoffsets + $tzoffsets['offsetSec'];
                        }
                        if (empty($tzoffsets)) {
                            $date_tzoffset = -(iCalUtilityFunctions::_tz2offset(date('O',self::_date2timestamp($dtstart['value']))));
                        }
                        //FIXME we must have the real timezone offset for the date by this point

                        //FIXME this is for the google ical feed which is bad!
                        if ($dtstart['value']['day'] != (int)$thisday2 && (isset($dtstart['value']['day']) && isset($dtend['value']['hour']))&&
                            !((int)$dtstart['value']['hour'] == 0 && (int)$dtstart['value']['min'] == 0  && (int)$dtstart['value']['sec'] == 0
                                && (int)$dtend['value']['hour'] == 0 && (int)$dtend['value']['min'] == 0  && (int)$dtend['value']['sec'] == 0
                                && ((((int)$dtstart['value']['day'] - (int)$dtend['value']['day']) == -1) || (((int)$dtstart['value']['month'] - (int)$dtend['value']['month']) == -1) || (((int)$dtstart['value']['month'] - (int)$dtend['value']['month']) == -11)))) {
                            $dtst = strtotime($currdate[1]);
                            $dtst1 = iCalUtilityFunctions::_timestamp2date($dtst);
                            $dtstart['value']['year'] = $dtst1['year'];
                            $dtstart['value']['month'] = $dtst1['month'];
                            $dtstart['value']['day'] = $dtst1['day'];
                            $currenddate = $vevent->getProperty('x-current-dtend');
                            $dtet = strtotime($currenddate[1]);
                            $dtet1 = iCalUtilityFunctions::_timestamp2date($dtet);
                            $dtend['value']['year'] = $dtet1['year'];
                            $dtend['value']['month'] = $dtet1['month'];
                            $dtend['value']['day'] = $dtet1['day'];
//                                $date_tzoffset = 0;
                        }

                        if (!empty($dtstart['value']['hour']) && !((int)$dtstart['value']['hour'] == 0 && (int)$dtstart['value']['min'] == 0  && (int)$dtstart['value']['sec'] == 0
                                                            && (int)$dtend['value']['hour'] == 0 && (int)$dtend['value']['min'] == 0  && (int)$dtend['value']['sec'] == 0
                                                            && ((((int)$dtstart['value']['day'] - (int)$dtend['value']['day']) == -1) || (((int)$dtstart['value']['month'] - (int)$dtend['value']['month']) == -1) || (((int)$dtstart['value']['month'] - (int)$dtend['value']['month']) == -11)))) {
                            $eventdate = expDateTime::startOfDayTimestamp(self::_date2timestamp($dtstart['value']) - $date_tzoffset);
//                                $eventend = expDateTime::startOfDayTimestamp(self::_date2timestamp($dtend['value']) - $date_tzoffset);
                            $extevents[$eventdate][$dy] = new stdClass();
                            $extevents[$eventdate][$dy]->eventdate = new stdClass();
                            $extevents[$eventdate][$dy]->eventdate->date = $eventdate;
//                                if ((int)($dtstart['value']['hour']) == 0 && (int)($dtstart['value']['min']) == 0  && (int)($dtstart['value']['sec']) == 0
//                                    && (int)($dtend['value']['hour']) == 0 && (int)($dtend['value']['min']) == 0  && (int)($dtend['value']['sec']) == 0
//                                    && ((((int)($dtstart['value']['day']) - (int)($dtend['value']['day'])) == -1) || (((int)($dtstart['value']['month']) - (int)($dtend['value']['month'])) == -1) || (((int)($dtstart['value']['month']) - (int)($dtend['value']['month'])) == -11))) {
////                                    if ($dtstart['value']['day'] != (int)($thisday2)) {
//                                    if (date('d',$eventdate) != $thisday2) {
////                                    if (date('d',$eventdate) != date('d',$eventend)) {
//                                        $yesterday = true;
//                                    } else {
//                                        $extevents[$eventdate][$dy]->eventstart = null;
//                                        $extevents[$eventdate][$dy]->is_allday = 1;
//                                    }
//                                } else {
                                if (date('d',$eventdate) != $thisday2) {
//                                    if (date('d',$eventdate) != date('d',$eventend)) {
                                    $yesterday = true;
                                } else {
                                    $extevents[$eventdate][$dy]->eventstart = ($dtstart['value']['hour'] * 3600) + ($dtstart['value']['min'] * 60) - $date_tzoffset;
//                                        if (date("I", $eventdate)) $extevents[$eventdate][$dy]->eventstart += 3600; // adjust for daylight savings time
                                }
//                                }
                        } else {
                            // this is an all day event
                            $eventdate = expDateTime::startOfDayTimestamp(self::_date2timestamp($dtstart['value']));
//                                $eventend = expDateTime::startOfDayTimestamp(self::_date2timestamp($dtend['value']));
                            $extevents[$eventdate][$dy] = new stdClass();
                            $extevents[$eventdate][$dy]->eventdate = new stdClass();
                            $extevents[$eventdate][$dy]->eventdate->date = $eventdate;
//                                if ($dtstart['value']['day'] != (int)($thisday2)) {
                            if (date('d',$eventdate) != $thisday2) {
//                                if (date('d',$eventdate) != date('d',$eventend)) {
                                $yesterday = true;
                            } else {
                                $extevents[$eventdate][$dy]->eventstart = null;
                                $extevents[$eventdate][$dy]->is_allday = 1;
                            }
                        }

                        // set the end time if needed
                        if (!$yesterday && isset($dtend['value']['hour']) && empty($extevents[$eventdate][$dy]->is_allday)) {
//                                if ($dtend['value']['day'] != (int)($thisday2)) {
//                                if ((date('d',$eventend) != $thisday2)) {
//                                    $yesterday = true;
//                                } else {
                            $extevents[$eventdate][$dy]->eventend = ($dtend['value']['hour'] * 3600) + ($dtend['value']['min'] * 60) - $date_tzoffset;
//                                if (date("I", $eventdate)) $extevents[$eventdate][$dy]->eventend += 3600; // adjust for daylight savings time
//                                }
                        }

                        // convert the start and end times to a full date
                        if (isset($extevents[$eventdate][$dy]->eventstart) && $extevents[$eventdate][$dy]->eventstart != null)
                            $extevents[$eventdate][$dy]->eventstart += $eventdate;
                        if (isset($extevents[$eventdate][$dy]->eventend))
                            $extevents[$eventdate][$dy]->eventend += $eventdate;

                        // dtstart required, one occurrence, (orig. start date)
                        $extevents[$eventdate][$dy]->title = $vevent->getProperty('summary');
                        $body = $vevent->getProperty('description');
                        // convert end of lines
                        $body = nl2br(str_replace("\\n"," <br>\n",$body));
                        $body = str_replace("\n"," <br>\n",$body);
                        $body = str_replace(array('==0A','=0A','=C2=A0')," <br>\n",$body);
                        $extevents[$eventdate][$dy]->body = $body;
                        $extevents[$eventdate][$dy]->location_data = 'icalevent' . $key;
                        $extevents[$eventdate][$dy]->color = !empty($this->config['pull_ical_color'][$key]) ? $this->config['pull_ical_color'][$key] : null;
                        if (!$yesterday && $eventdate >= $startdate) {
                            $dy++;
                        } else {
                            unset($extevents[$eventdate][$dy]);
                        }
                    }
                }
            }
        }
        return $extevents;
    }

    public static function _date2timestamp( $datetime, $wtz=null ) {
      if( !isset( $datetime['hour'] )) $datetime['hour'] = 0;
      if( !isset( $datetime['min'] ))  $datetime['min']  = 0;
      if( !isset( $datetime['sec'] ))  $datetime['sec']  = 0;
      if( empty( $wtz ) && ( !isset( $datetime['tz'] ) || empty(  $datetime['tz'] )))
        return mktime( $datetime['hour'], $datetime['min'], $datetime['sec'], $datetime['month'], $datetime['day'], $datetime['year'] );
      $output = $offset = 0;
      if( empty( $wtz )) {
        if( iCalUtilityFunctions::_isOffset( $datetime['tz'] )) {
          $offset = iCalUtilityFunctions::_tz2offset( $datetime['tz'] ) * -1;
          $wtz    = 'UTC';
        }
        else
          $wtz    = $datetime['tz'];
      }
      if(( 'Z' == $wtz ) || ( 'GMT' == strtoupper( $wtz )))
        $wtz      = 'UTC';
      try {
        $strdate  = sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $datetime['year'], $datetime['month'], $datetime['day'], $datetime['hour'], $datetime['min'], $datetime['sec'] );
        $d        = new DateTime( $strdate, new DateTimeZone( $wtz ));
        if( 0    != $offset )  // adjust for offset
          $d->modify( $offset.' seconds' );
        $output   = $d->format( 'U' );
        unset( $d );
      }
      catch( Exception $e ) {
        $output = mktime( $datetime['hour'], $datetime['min'], $datetime['sec'], $datetime['month'], $datetime['day'], $datetime['year'] );
      }
      return $output;
    }

    /**
     * build/update the external event cache
     *
     */
    public function build_cache() {
        global $db;

        // get our requested config
        $cfg = new expConfig();
        $configs = $cfg->find('all', "location_data LIKE '%event%'"); // get all event module configs
        foreach ($configs as $config) {
            $loc = expUnserialize($config->location_data);
            if (!empty($this->params['title'])) {
                if ($this->params['title'] == $config->config['feed_sef_url']) {
                    $this->config = $config->config;
                    break;
                }
            } elseif (!empty($this->params['src'])) {
                if ($this->params['src'] == $loc->src) {
                    $this->config = $config->config;
                    break;
                }
            }
        }

        // next loop through our config pull urls

        // google xml pull
        if (!empty($this->config['pull_gcal'])) foreach ($this->config['pull_gcal'] as $key=>$extgcalurl) {
            $start = expDateTime::startOfMonthTimestamp(time());
            $gcal_cname = str_replace(array("/",":","&","?"),"_",$extgcalurl);
            $cache_fname = BASE.'tmp/cache/'.$gcal_cname.".cache";
            $db->delete('event_cache', "feed='" . $extgcalurl . "' AND eventdate > " . $start);  // replace future events
            // loop through 12 months, 1 month at a time
            for ($i=1; $i < 13; $i++) {
                $end = expDateTime::endOfMonthTimestamp($start);
                $tmp = 0;
                $extevents = $this->get_gcal_events($extgcalurl, $start, $end, $tmp, 0, true);
//                $extevents = $this->get_gcal_events($extgcalurl, null, null, 0, 0, 0, true);
                foreach ($extevents as $day) {
                    foreach ($day as $extevent) {
                        $event_cache = new stdClass();
                        $event_cache->feed = $extgcalurl;
                        $event_cache->event_id = $extevent->event_id;
                        $event_cache->title = $extevent->title;
                        $event_cache->body = $extevent->body;
                        $event_cache->eventdate = $extevent->eventdate->date;
                        if (isset($extevent->dateFinished) && $extevent->dateFinished != -68400)
                            $event_cache->dateFinished = $extevent->dateFinished;
                        if (isset($extevent->eventstart))
                            $event_cache->eventstart = $extevent->eventstart;
                        if (isset($extevent->eventend))
                            $event_cache->eventend = $extevent->eventend;
                        if (isset($extevent->is_allday))
                            $event_cache->is_allday = $extevent->is_allday;
                        $found = false;
                        if ($extevent->eventdate->date < $start)   // prevent duplicating events crossing month boundaries
                            $found = $db->selectObject('event_cache','feed="'.$extgcalurl.'" AND event_id="'.$event_cache->event_id.'" AND eventdate='.$event_cache->eventdate);
                        if (!$found)
                            $db->insertObject($event_cache,'event_cache');
                    }
                }
                $start = expDateTime::startOfMonthTimestamp($end + 1024);
            }
            $cache_contents = serialize(array('start_date'=>$start,'first_date'=>(int)$db->selectValue('event_cache','eventdate','feed="'.$extgcalurl.'" ORDER BY eventdate'),'refresh_date'=>time()));
            file_put_contents($cache_fname, $cache_contents);
        }

        // ical pull
        $start = expDateTime::startOfMonthTimestamp(time());
        if (!empty($this->config['pull_ical'])) foreach ($this->config['pull_ical'] as $key=>$exticalurl) {
            $ical_cname = str_replace(array("/",":","&","?"),"_",$exticalurl);
            $cache_fname = BASE.'tmp/cache/'.$ical_cname.".cache";
            $db->delete('event_cache', "feed='" . $exticalurl . "' AND eventdate > " . $start);
            // get 1 years worth of events
            $extevents = $this->get_ical_events($exticalurl, $start);
            foreach ($extevents as $day) {
                foreach ($day as $extevent) {
                    $event_cache = new stdClass();
                    $event_cache->feed = $exticalurl;
                    $event_cache->title = $extevent->title;
                    $event_cache->body = $extevent->body;
                    $event_cache->eventdate = $extevent->eventdate->date;
                    if (isset($extevent->dateFinished))
                        $event_cache->dateFinished = $extevent->dateFinished;
                    $event_cache->eventstart = $extevent->eventstart;
                    if (isset($extevent->eventend))
                        $event_cache->eventend = $extevent->eventend;
                    if (isset($extevent->is_allday))
                        $event_cache->is_allday = $extevent->is_allday;
                    $db->insertObject($event_cache, 'event_cache');
                }
            }
            $cache_contents = serialize(array('start_date'=>$start,'first_date'=>(int)$db->selectValue('event_cache','eventdate','feed="'.$exticalurl.'" ORDER BY eventdate'),'refresh_date'=>time()));
            file_put_contents($cache_fname, $cache_contents);
        }
        flash('message', gt('External Calendar Event cache updated'));
        echo show_msg_queue();
    }

    function import() {
        $pullable_modules = expModules::listInstalledControllers($this->baseclassname);
        $modules = new expPaginator(array(
            'records' => $pullable_modules,
            'controller' => $this->loc->mod,
            'action' => $this->params['action'],
            'order'   => isset($this->params['order']) ? $this->params['order'] : 'section',
            'dir'     => isset($this->params['dir']) ? $this->params['dir'] : '',
            'page'    => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns' => array(
                gt('Title') => 'title',
                gt('Page')  => 'section'
            ),
        ));

        assign_to_template(array(
            'modules' => $modules,
        ));
    }

    function import_select()
    {
        if (empty($this->params['import_aggregate'])) {
            expValidator::setErrorField('import_aggregate[]');
            expValidator::failAndReturnToForm(gt('You must select a module.'), $this->params);
        }
        $extevents = array();
        unset(
            $this->params['begin'],
            $this->params['end']
        );  // always use date value
        $begin = yuidatetimecontrol::parseData('begin', $this->params);
        $end = yuidatetimecontrol::parseData('end', $this->params);
        if ($this->params['file_type'] == 'file') {
            //Get the temp directory to put the uploaded file
            $directory = "tmp";

            //Get the file save it to the temp directory
            if (!empty($_FILES["import_file"]) && $_FILES["import_file"]["error"] == UPLOAD_ERR_OK) {
                $file = expFile::fileUpload(
                    "import_file",
                    false,
                    false,
                    time() . "_" . $_FILES['import_file']['name'],
                    $directory . '/'
                );
                if ($file === null) {
                    switch ($_FILES["import_file"]["error"]) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $this->params['_formError'] = gt(
                                'The file you attempted to upload is too large.  Contact your system administrator if this is a problem.'
                            );
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $this->params['_formError'] = gt('The file was only partially uploaded.');
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $this->params['_formError'] = gt('No file was uploaded.');
                            break;
                        default:
                            $this->params['_formError'] = gt(
                                'A strange internal error has occurred.  Please contact the Exponent Developers.'
                            );
                            break;
                    }
                    expSession::set("last_POST", $this->params);
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit("");
                } else {
                    $extevents = $this->get_ical_events($directory . "/" . $file->filename, $begin, $end);
                }
            } else {
               expValidator::setErrorField('import_file');
               expValidator::failAndReturnToForm(gt('File failed to upload.'), $this->params);  // file upload error
            }
        } else {
            if (empty($this->params['ext_feed'])) {
                expValidator::setErrorField('ext_feed');
                expValidator::failAndReturnToForm(gt('You must enter a feed url.'), $this->params);
            }
            $extevents = $this->get_ical_events($this->params['ext_feed'], $begin, $end);
        }

        $src = $this->params['import_aggregate'][0];
        $count = 0;
        foreach ($extevents as $day) {
            foreach ($day as $extevent) {
                $event = array();
                $event['title'] = $extevent->title;
                $event['body'] = $extevent->body;
                $event['eventdate'] = $extevent->eventdate->date;
                $event['eventstart'] = $extevent->eventstart;
                $event['eventstart'] -= $event['eventdate'];
                if (isset($extevent->eventend))
                    $event['eventend'] = $extevent->eventend;
                else
                    $event['eventend'] = $extevent->eventstart;
                $event['eventend'] -= $event['eventdate'];
                if (isset($extevent->is_allday))
                    $event['is_allday'] = $extevent->is_allday;
                $event['module'] = 'event';
                $event['src'] = $src;
                $item = new event();  // create new populated record to auto-set things
                $item->update($event);
                $count++;
            }
        }

        unlink($directory . "/" . $file->filename);

        // update search index
        $this->addContentToSearch();

        flashAndFlow('message', $count . ' ' . gt('events were imported.'));
    }

    /** @deprecated
     * function to build a control requested via ajax
     * we the html just like the control smarty function
     * @deprecated
     */
    public function buildControl() {
        $control = new colorcontrol();
        if (!empty($this->params['value'])) $control->value = $this->params['value'];
        if ($this->params['value'][0] != '#') $this->params['value'] = '#' . $this->params['value'];
        $control->default = $this->params['value'];
        if (!empty($this->params['hide'])) $control->hide = $this->params['hide'];
        if (isset($this->params['flip'])) $control->flip = $this->params['flip'];
        $this->params['name'] = !empty($this->params['name']) ? $this->params['name'] : '';
        $control->name  = $this->params['name'];
        $this->params['id'] = !empty($this->params['id']) ? $this->params['id'] : '';
        $control->id  = isset($this->params['id']) && $this->params['id'] != "" ? $this->params['id'] : "";
        //echo $control->id;
        if (empty($control->id)) $control->id = $this->params['name'];
        if (empty($control->name)) $control->name = $this->params['id'];

        // attempt to translate the label
        if (!empty($this->params['label'])) {
            $this->params['label'] = gt($this->params['label']);
        } else {
            $this->params['label'] = null;
        }
        echo $control->toHTML($this->params['label'], $this->params['name']);
//        $ar = new expAjaxReply(200, gt('The control was created'), json_encode(array('data'=>$code)));
//        $ar->send();
    }

}

?>