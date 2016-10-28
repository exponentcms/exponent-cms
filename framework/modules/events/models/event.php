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
 * @subpackage Models
 * @package Modules
 */

class event extends expRecord {
    public $has_many = array('eventdate');
    protected $attachable_item_types = array(
        'content_expCats'=>'expCat',
        'content_expFiles'=>'expFile',
        'content_expTags'=>'expTag'
    );

    /**
     * Events have special circumstances since they are based on dates
     *   'upcoming', 'month', 'week', 'day', etc...
     *
     * @param string $range
     * @param null $where
     * @param null $order
     * @param null $limit
     * @param int $limitstart
     * @param bool $get_assoc
     * @param bool $get_attached
     * @param array $except
     * @param bool $cascade_except
     * @return array
     */
    public function find($range = 'all', $where = null, $order = null, $limit = null, $limitstart = 0, $get_assoc = true, $get_attached = true, $except = array(), $cascade_except = false)
    {
        if (is_numeric($range) || in_array($range, array('all', 'first', 'bytitle', 'count', 'in', 'bytag', 'bycat'))) {
            return parent::find($range, $where, $order, $limit, $limitstart, $get_assoc, $get_attached, $except, $cascade_except);
        } else {  // 'upcoming', 'month', 'week', 'day', etc...
            //note $order is boolean for 'featured'
            //note $limit is number of days, NOT number of records
            //note $limitstart is a unixtimestamp in this instance
            $order = expString::escape($order);
            if ($limit !== null)
                $limit = intval($limit);
            if ($limitstart !== null)
                $limitstart = intval($limitstart);
            $ed = new eventdate();
            $day = expDateTime::startOfDayTimestamp(time());
            $sort_asc = true; // For the getEventsForDates call
            if (strcasecmp($range, 'upcoming') == 0) {
                if (!empty($limit)) {
                    $eventlimit = " AND date <= " . ($day + ($limit * 86400));
                } else {
                    $eventlimit = "";
                }
                $dates = $ed->find("all", $where . " AND date >= " . $day . $eventlimit . " ORDER BY date ASC ");
//                $begin = $day;
//                $end = null;
                $items = $this->getEventsForDates($dates, $sort_asc, $order ? true : false, true);

                // external events
//                $extitems = $this->getExternalEvents($begin, $end);
                // we need to crunch these down
//                $extitem = array();
//                foreach ($extitems as $days) {
//                    foreach ($days as $event) {
//                        if (empty($event->eventdate->date) || ($viewrange == 'upcoming' && $event->eventdate->date < time())) break;
//                        if (empty($event->eventstart)) $event->eventstart = $event->eventdate->date;
//                        $extitem[] = $event;
//                    }
//                }
//                $items = array_merge($items, $extitem);

                // event registration events
//                if (!empty($this->config['aggregate_registrations'])) $regitems = eventregistrationController::getRegEventsForDates($begin, $end, $regcolor);
                // we need to crunch these down
//                $regitem = array();
//                if (!empty($regitems)) foreach ($regitems as $days) {
//                    foreach ($days as $value) {
//                        $regitem[] = $value;
//                    }
//                }
//                $items = array_merge($items, $regitem);

                $items = expSorter::sort(array('array' => $items, 'sortby' => 'eventstart', 'order' => 'ASC'));
                return $items;
            }
        }
    }

    function getEventsForDates($edates, $sort_asc = true, $featuredonly = false, $condense = false) {
        global $eventid;

        $events = array();
        $featuresql = "";
        if ($featuredonly)
            $featuresql = " AND is_featured=1";
        foreach ($edates as $edate) {
            $evs = $this->find('all', "id=" . $edate->event_id . $featuresql);
            foreach ($evs as $key=>$event) {
                if ($condense) {
                    //fixme we're leaving events which ended earlier in the day which won't be displayed, which therefore cancels out tomorrow's event
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

    public function update($params = array()) {
        $params['eventstart'] = datetimecontrol::parseData('eventstart',$params);
        $params['eventend'] = datetimecontrol::parseData('eventend',$params);
        $this->checkForAttachableItems($params);
        $this->build($params);
//        $id = !empty($params['id']) ? $params['id'] : null;
//        $calevent = new event($id);
//        $calevent->update($params);  // prime the record with the parameters

        if (!empty($params['id'])) {  // update existing event
            $calevent = new eventdate();
       		if (!empty($params['is_recurring'])) {
       			// For recurring events, check some stuff.
       			// Were all dates selected?
                $eventdates = $calevent->find('all',"event_id=".$this->id);
       			if (count($params['dates']) != count($eventdates)) {  // only part of list changed
       				// yes.  just update the original
//                    $calevent->update();
       				// If the date has changed, modify the current date_id
//       			} else {  // we've split out dates from original
       				// No, create new and relink affected dates
       				unset($this->id);
//                    $calevent = new event($params);  // create a new event based on parameters
       				if (count($params['dates']) == 1) $this->is_recurring = 0; // Back to a single event.

                    $this->save(true);  // save new event to get an event id

                    unset($params['id']);
       				foreach (array_keys($params['dates']) as $date_id) {  // update all the date occurrences being changed
                        $eventdate = $calevent->find('first',"id=".$date_id);
                        $eventdate->event_id = $this->id;
                        if (count($params['dates']) == 1) $eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$params));
                        $eventdate->update($params);
       				}
       			} else { // all existing event occurrences have changed
//        			  $eventdate = $db->selectObject('eventdate','id='.intval($params['date_id']));
                    $eventdate = $calevent->find('first','id='.intval($params['date_id']));
                    $eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$params));
                    $eventdate->update();
                }
       		} else {  // not recurring
//                $calevent->update();
       			// There should be only one eventdate
//                $eventdate = $calevent->eventdate[0]->find('first','event_id = '.$calevent->id);
                $eventdate = $calevent->find('first','event_id = '.$this->id);
       			$eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$params));
                $eventdate->update();
       		}
       	} else {  // new event
       		$start_recur = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$params));
       		$stop_recur  = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("untildate",$params));

       		if (!empty($params['recur']) && $params['recur'] != "recur_none") {  // recurring event
       			// Do recurrence
                $freq = $params['recur_freq_'.$params['recur']];

       			switch ($params['recur']) {
       				case "recur_daily":
       					$dates = expDateTime::recurringDailyDates($start_recur,$stop_recur,$freq);
       					break;
       				case "recur_weekly":
                        $dateinfo = getdate($start_recur);  //FIXME hack in case the day of week wasn't checked off
       					$dates = expDateTime::recurringWeeklyDates($start_recur,$stop_recur,$freq,(!empty($params['day']) ? array_keys($params['day']) : array($dateinfo['wday'])));
       					break;
       				case "recur_monthly":
       					$dates = expDateTime::recurringMonthlyDates($start_recur,$stop_recur,$freq,(!empty($params['month_type'])?$params['month_type']:true));
       					break;
       				case "recur_yearly":
       					$dates = expDateTime::recurringYearlyDates($start_recur,$stop_recur,$freq);
       					break;
       				default:
       					echo "Bad type: " . $params['recur'] . "<br />";
       					return;
       					break;
       			}

                $this->is_recurring = 1; // Set the recurrence flag.
       		} else {  // not recurring
       			$dates = array($start_recur);
       		}
//            $calevent->update($params);  // prime the record with the parameters
            $this->save(true);
       		foreach ($dates as $d) {
                $edate = new eventdate(array('event_id'=>$this->id,'location_data'=>$this->location_data,'date'=>$d));
                $edate->update();
            }
       	}
//        $calevent->update($params);
        // call expController->update() to save the image, is it necessary?
        $this->save(true);
    }

    public function afterDelete() {
        $ed = new eventdate();
        $dates = $ed->find('all','event_id='.$this->id);
        foreach ($dates as $date) {
            $date->delete();
        }
    }

    public static function dayNames() {
        $days = array();
        for ($i=0; $i < 7; $i++) {
            $days['short'][$i] = substr(strftime("%a", mktime(0, 0, 0, 6, $i+2, 2013)), 0, 1);
            $days['med'][$i] = strftime("%a", mktime(0, 0, 0, 6, $i+2, 2013));
            $days['long'][$i] = strftime('%A ', mktime(0, 0, 0, 6, $i+2, 2013));
        }
        return $days;
    }

}

?>