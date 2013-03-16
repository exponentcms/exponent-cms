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

    public function update($params = array()) {
        $params['eventstart'] = datetimecontrol::parseData('eventstart',$params);
        $params['eventend'] = datetimecontrol::parseData('eventend',$params);
        $this->checkForAttachableItems($params);
        $this->build($params);
//        $id = !empty($params['id']) ? $params['id'] : null;
//        $calevent = new event($id);
//        $calevent->update($params);  // prime the record with the parameters

        if (!empty($params['id'])) {  // update existing event
       		if (!empty($params['is_recurring'])) {
       			// For recurring events, check some stuff.
       			// Were all dates selected?
                $calevent = new eventdate();
                $eventdates = $calevent->find('all',"event_id=".$this->id);
       			if (count($params['dates']) != count($eventdates)) {  // only part of list changed
       				// yes.  just update the original
//                    $calevent->update();
       				// If the date has changed, modify the current date_id
//       			} else {  // we've split out dates from original
       				// No, create new and relink affected dates
       				unset($this->id);
//                    $calevent = new event($params);  // create a new event based on parameters
       				if (count($params['dates']) == 1) {
                        $this->is_recurring = 0; // Back to a single event.
       				}

                    $this->save(true);  // save new event to get an event id

       				foreach (array_keys($params['dates']) as $date_id) {  // update all the date occurances being changed
                        $eventdate = $calevent->find('first',"id=".$date_id);
                        $eventdate->event_id = $this->id;
                        unset($params['id']);
                        $eventdate->update($params);
       				}
       			}  // all existing event occurrences have changed
//       			$eventdate = $db->selectObject('eventdate','id='.intval($params['date_id']));
                $eventdate = $calevent->find('first','id='.intval($params['date_id']));
                $eventdate->date = expDateTime::startOfDayTimestamp(yuicalendarcontrol::parseData("eventdate",$params));
                $eventdate->update();
       		} else {  // not recurring
//                $calevent->update();
       			// There should be only one eventdate
//                $eventdate = $calevent->eventdate[0]->find('first','event_id = '.$calevent->id);
                $calevent = new eventdate();
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

}

?>