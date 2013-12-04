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
 * This is the class expDateTime
 * These methods do NOT take timezones into consideration unless documented otherwise
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expDateTime {

	/** exdoc
	 * @state <b>UNDOCUMENTED</b>
	 * @node To Be Deprecated
	 * @param $controlName
	 * @param $default_month
	 * @return string
	 */
	public static function monthsDropdown($controlName,$default_month) {
		$months = array(
			1=>gt("January"),
			2=>gt("February"),
			3=>gt("March"),
			4=>gt("April"),
			5=>gt("May"),
			6=>gt("June"),
			7=>gt("July"),
			8=>gt("August"),
			9=>gt("September"),
			10=>gt("October"),
			11=>gt("November"),
			12=>gt("December")
		);

		$html = '<select name="' . $controlName . '" size="1">';
		foreach ($months as $id=>$month) {
			$html .= '<option value="' . $id . '"';
			if ($id == $default_month) $html .= ' selected';
			$html .= '>' . $month . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

    /** exdoc
     * Looks at a start and end time and figures out
     * how many seconds elapsed since between the earlier
     * timestamp and the later timestamp.  It doesn't matter
     * if the bigger argument is specified first or not. Returns
     * the number of seconds between $time_a and $time_b
     *
     * @param int  $time_a The first timestamp
     * @param int  $time_b The second timestamp
     * @param bool $iso8601 return duration as an iso8601 string
     *
     * @return array
     * @node Subsystems:expDateTime
     */
	public static function duration($time_a,$time_b,$iso8601=false) {
		$d = abs($time_b-$time_a);
        if (!$iso8601) {
            $duration = array();
            if ($d >= 86400) {
                $duration['days'] = floor($d / 86400);
                $d %= 86400;
            }
            if (isset($duration['days']) || $d >= 3600) {
                if ($d) $duration['hours'] = floor($d / 3600);
                else $duration['hours'] = 0;
                $d %= 3600;
            }
            if (isset($duration['hours']) || $d >= 60) {
                if ($d) $duration['minutes'] = floor($d / 60);
                else $duration['minutes'] = 0;
                $d %= 60;
            }
            $duration['seconds'] = $d;
        } else {
           $parts = array();
           $multipliers = array(
               'hours' => 3600,
               'minutes' => 60,
               'seconds' => 1
           );
           foreach ($multipliers as $type => $m) {
               $parts[$type] = (int)($d / $m);
               $d -= ($parts[$type] * $m);
           }
           $default = array(
               'hours' => 0,
               'minutes' => 0,
               'seconds' => 0
           );
           extract(array_merge($default, $parts));
            $duration = "PT{$hours}H{$minutes}M{$seconds}S";
        }
		return $duration;
	}

    /** exdoc
   	 * Given a timestamp, this function will calculate another timestamp
   	 * that represents the beginning of the year that the passed timestamp
   	 * falls into.  For instance, passing a timestamp representing January 25th 1984
   	 * would return a timestamp representing January 1st 1984, at 12:00am.
   	 *
   	 * @param int $timestamp The original timestamp to use when calculating.
   	 * @return int
   	 * @node Subsystems:expDateTime
   	 */
   	public static function startOfYearTimestamp($timestamp) {
   		$info = getdate($timestamp);
   		// Calculate the timestamp at 8am, and then subtract 8 hours, for Daylight Savings
   		// Time.  If we are in those strange edge cases of DST, 12:00am can turn out to be
   		// of the previous day.
   		return mktime(0,0,0,1,1,$info['year']);
   	}

   	/** exdoc
   	 * Given a timestamp, this function will calculate another timestamp
   	 * that represents the end of the year that the passed timestamp
   	 * falls into.  For instance, passing a timestamp representing January 25th 1984
   	 * would return a timestamp representing December 31st 1984, at 11:59pm.
   	 *
   	 * @param int $timestamp The original timestamp to use when calculating.
   	 * @return int
   	 * @node Subsystems:expDateTime
   	 */
   	public static function endOfYearTimestamp($timestamp) {
   		$info = getdate($timestamp);
   		// No month has fewer than 28 days, even in leap year, so start out at 28.
   		// At most, we will loop through the while loop 3 times (29th, 30th, 31st)
   //		$info['mday'] = 28;
   //		// Keep incrementing the mday value until it is not valid, and use last valid value.
   //		// This should get us the last day in the month, and take into account leap years
   //		while (checkdate($info['mon'],$info['mday']+1,$info['year'])) $info['mday']++;
   //		// Calculate the timestamp at 8am, and then subtract 8 hours, for Daylight Savings
   //		// Time.  If we are in those strange edge cases of DST, 12:00am can turn out to be
   //		// of the previous day.
   //		return mktime(23,59,59,$info['mon'],$info['mday'],$info['year']);
           return mktime(23,59,59,12,31,$info['year']);
   	}

	/** exdoc
	 * Given a timestamp, this function will calculate another timestamp
	 * that represents the beginning of the month that the passed timestamp
	 * falls into.  For instance, passing a timestamp representing January 25th 1984
	 * would return a timestamp representing January 1st 1984, at 12:00am.
	 *
	 * @param int $timestamp The original timestamp to use when calculating.
	 * @return int
	 * @node Subsystems:expDateTime
	 */
	public static function startOfMonthTimestamp($timestamp) {
		$info = getdate($timestamp);
		// Calculate the timestamp at 8am, and then subtract 8 hours, for Daylight Savings
		// Time.  If we are in those strange edge cases of DST, 12:00am can turn out to be
		// of the previous day.
		return mktime(0,0,0,$info['mon'],1,$info['year']);
	}

	/** exdoc
	 * Given a timestamp, this function will calculate another timestamp
	 * that represents the end of the month that the passed timestamp
	 * falls into.  For instance, passing a timestamp representing January 25th 1984
	 * would return a timestamp representing January 31st 1984, at 11:59pm.
	 *
	 * @param int $timestamp The original timestamp to use when calculating.
	 * @return int
	 * @node Subsystems:expDateTime
	 */
	public static function endOfMonthTimestamp($timestamp) {
		$info = getdate($timestamp);
		// No month has fewer than 28 days, even in leap year, so start out at 28.
		// At most, we will loop through the while loop 3 times (29th, 30th, 31st)
//		$info['mday'] = 28;
//		// Keep incrementing the mday value until it is not valid, and use last valid value.
//		// This should get us the last day in the month, and take into account leap years
//		while (checkdate($info['mon'],$info['mday']+1,$info['year'])) $info['mday']++;
//		// Calculate the timestamp at 8am, and then subtract 8 hours, for Daylight Savings
//		// Time.  If we are in those strange edge cases of DST, 12:00am can turn out to be
//		// of the previous day.
//		return mktime(23,59,59,$info['mon'],$info['mday'],$info['year']);
        return mktime(23,59,59,$info['mon']+1,0,$info['year']);
	}

	/** exdoc
	 * Looks at a timestamp and returns the date of the last
	 * day.  For instance, if the passed timestamp was in January,
	 * this function would return 31.  Leap year is taken into account.
	 *
	 * @param int $timestamp The timestamp to check.
	 * @return int
	 * @node Subsystems:expDateTime
	 */
	public static function endOfMonthDay($timestamp) {
//		$info = getdate($timestamp);
//		// No month has fewer than 28 days, even in leap year, so start out at 28.
//		// At most, we will loop through the while loop 3 times (29th, 30th, 31st)
//		$last = 28;
//		// Keep incrementing the mday value until it is not valid, and use last valid value.
//		// This should get us the last day in the month, and take into account leap years
//		while (checkdate($info['mon'],$last+1,$info['year'])) $last++;
//		return $last;
//        $info = mktime(23,59,59,$info['mon']+1,0,$info['year']);
//        return $info['day'];
        return date('t', $timestamp);
	}

	/** exdoc
	 * Looks at a timestamp and returns another timestamp representing
	 * 12:00:01 am of the same day.
	 *
	 * @param int $timestamp The timestamp to check.
	 * @return int
	 * @node Subsystems:expDateTime
	 */
	public static function startOfDayTimestamp($timestamp) {
		$info = getdate($timestamp);
		// Calculate the timestamp at 8am, and then subtract 8 hours, for Daylight Savings
		// Time.  If we are in those strange edge cases of DST, 12:00am can turn out to be
		// of the previous day.
		return mktime(0,0,0,$info['mon'],$info['mday'],$info['year']);
	}

    /** exdoc
   	 * Looks at a timestamp and returns another timestamp representing
   	 * 11:59:59 pm of the same day.
   	 *
   	 * @param int $timestamp The timestamp to check.
   	 * @return int
   	 * @node Subsystems:expDateTime
   	 */
   	public static function endOfDayTimestamp($timestamp) {
   		$info = getdate($timestamp);
   		// Calculate the timestamp at 8am, and then subtract 8 hours, for Daylight Savings
   		// Time.  If we are in those strange edge cases of DST, 12:00am can turn out to be
   		// of the previous day.
   		return mktime(23,59,59,$info['mon'],$info['mday'],$info['year']);
   	}

	/** exdoc
	 * Looks at a timestamp and returns another timestamp representing
	 * 12:00:01 am of the DISPLAY_START_OF_WEEK day of the same week.
	 *
	 * @param int $timestamp The timestamp to check.
	 * @return int
	 * @node Subsystems:expDateTime
	 */
	public static function startOfWeekTimestamp($timestamp) {
		$info = getdate($timestamp);
		return self::startOfDayTimestamp($timestamp - (($info['wday'] - DISPLAY_START_OF_WEEK) * 86400));
	}

    /** exdoc
   	 * Looks at a timestamp and returns another timestamp representing
   	 * 12:00:01 am of the DISPLAY_START_OF_WEEK day of the same week.
   	 *
   	 * @param int $timestamp The timestamp to check.
   	 * @return int
   	 * @node Subsystems:expDateTime
   	 */
   	public static function endOfWeekTimestamp($timestamp) {
   		$info = getdate($timestamp);
   		return self::endOfDayTimestamp(($timestamp - (($info['wday'] - DISPLAY_START_OF_WEEK) * 86400) + 7 * 86400));
   	}

	// Recurring Dates

	/** exdoc
	 * Find all of the dates that fall within the daily recurrence criteria.
	 * This is the simplest form of recurrence, events are spaced a given
	 * number of days apart.
	 *
	 * @param int $start The start of the recurrence range
	 * @param int $end The end of the recurrence range
	 * @param integer $freq Frequency of recurrence - 2 means every 2 days, and 1
	 * 	means every day.
	 * @return array
	 * @node Subsystems:expDateTime
	 */
	public static function recurringDailyDates($start,$end,$freq) {
		$dates = array();
		$curdate = $start;
		do {
			$dates[] = $curdate;
			$curdate = self::startOfDayTimestamp($curdate + ((86400) * $freq)+3601);
		} while ($curdate <= $end);
		return $dates;
	}

	/** exdoc
	 * Finds all of the dates that fall within the weekly recurrence criteria
	 * (namely, which weekdays) and within the $start to $end timestamp range.
	 *
	 * For a technical discussion of this function and the mathematics involved,
	 * please see the sdk/analysis/subsystems/datetime.txt file.
	 *
	 * @param int $start The start of the recurrence range
	 * @param int $end The end of the recurrence range
	 * @param integer $freq Weekly frequency - 1 means every week, 2 means every
	 *   other week, etc.
	 * @param array $days The weekdays (in integer notation, 0 = Sunday, etc.) that
	 *   should be matched.  A MWF recurrence rotation would contain the values
	 *  1,3 and 5.
	 * @return array
	 * @node Subsystems:expDateTime
	 */
	public static function recurringWeeklyDates($start,$end,$freq,$days) {
		// Holding array, for keeping the timestamps of applicable dates.
		// This variable will be returned to the calling scope.
		$dates = array();

		// Need to figure out which weekday occurs directly after the specified
		// start date.  This will be our launching point for the recurrence calculations.
		$dateinfo = getdate($start);

		// Finding the Start Date
		//
		// Start at the first weekday in the list ($days[$counter] where $counter is 0)
		// and go until we find a weekday greater than the weekday of the $start date.
		//
		// So, if we start on a Tuesday, and want to recur weekly for a MWF rotation,
		// This would check Monday, then Wednesday and stop, using wednesday for the
		// recalculated start date ($curdate)
		for ($counter = 0; $counter < count($days); $counter++) {
			if ($days[$counter] >= $dateinfo['wday']) {
				// exit loop, we found the weekday to use ($days[$counter])
				break;
			}
		}
		if ($days[$counter] < $dateinfo['wday']) {
			// in case we did MWF and started on a Saturday...
			$counter = 0; // reset to first day of next week
			$start += 86400 * (7-$dateinfo['wday']+$days[$counter]);
		} else if ($days[$counter] > $dateinfo['wday']) {
			// 'Normal' case, in which we started before one of the repeat days.
			$start += 86400 * ($days[$counter] - $dateinfo['wday']);
		}
		// Found start date.  Set curdate to the start date, so it gets picked
		// up in the do ... while loop.
		$curdate = $start;

		// Find all of the dates that match the recurrence criteria, within the
		// specified recurrence range.  Implemented as a do ... while loop because
		// we always need at least the start date, and we have already determined
		// that with the code above (the $curdate variable)
		do {
			// Append $curdate to the array of dates.  $curdate will be changed
			// at the end of the loop, to be equal to the next date meeting the
			// criteria.  If $curdate ever falls outside the recurrence range, the
			// loop will exit.
			$dates[] = $curdate;
			$curdate += 8*3600; // Add 8 hours, to avoid DST problems
			// Grab the date information (weekday, month, day, year, etc.) for
			// the current date, so we can ratchet up to the next date in the series.
			$dateinfo = getdate($curdate);
			// Get the current weekday.
			$day = $days[$counter];
			// Increment the counter so that the next time through we get the next
			// weekday.  If the counter moves off the end of the list, reset it to 0.
			$counter++;
			if ($counter >= count($days)) {
				// Went off the end of the week.  Reset the pointer to the beginning
				$counter = 0;
				// Difference in number of days between the last day in the rotation
				// and the first day (for recalculating the $curdate value)
				#$daydiff = $days[count($days)-1]-$days[0];

				$daydiff = 7 + $days[0] - $days[count($days)-1];

				if ($daydiff == 0) {
					// In case we are in a single day rotation, the difference will be 0.
					// It needs to be 7, so that we skip ahead a full week.
					$daydiff = 7;
				}
				// Increment the current date to go off to the next week, first weekday
				// in the rotation.
				$curdate += 7 * 86400 * ($freq-1) + 86400 * $daydiff; // Increment by number of weeks
			} else {
				// If we haven't gone off the end of the week, we just need to add the number
				// of days between the next weekday in the rotation ($days[$counter] - because
				// $counter was incremented above) and the $curdate weekday (store in the
				// $dateinfo array returned from the PHP call to getdate(), aboce).
				$curdate += 86400 * ($days[$counter] - $dateinfo['wday']);
			}
			// Round down to the start of the day (12:00 am) for $curdate, in case something
			// got a little out of whack thanks to DST.
			$curdate = self::startOfDayTimestamp($curdate);
		} while ($curdate <= $end); // If we go off the end of the recurrence range, ext.

		// We have no fully calculated the dates we need. Return them to the calling scope.
		return $dates;
	}

	/** exdoc
	 * Finds all of the dates that fall within the monthly recurrence criteria
	 * and within the $start to $end timestamp range.  Monthly recurrence can be
	 * done on a specific date (the 14th of the month) or on a specific weekday / offset
	 * pair (the third sunday of the month).
	 *
	 * @param int $start The start of the recurrence range
	 * @param int $end The end of the recurrence range
	 * @param integer $freq Monthly frequency - 1 means every month, 2 means every
	 *   other month, etc.
	 * @param bool $by_day Whether or not to recur by the weekday and week offset
	 * (in case of true), or by the date (in case of false).
	 * @return array
	 * @node Subsystems:expDateTime
	 */
	public static function recurringMonthlyDates($start,$end,$freq,$by_day=false) {
		// Holding array, for keeping all of the matching timestamps
		$dates = array();
		// Date to start on.
		$curdate = $start;

		// Get the date info, including the weekday.
		$dateinfo = getdate($curdate);

		// Store the month day.  If we are not doing by day monthly recurrence,
		// then this will be used unchanged throughout the do .. while loop.
		$mdate = $dateinfo['mday'];

		$week = 0; // Only used for $by_day;
		$wday = 0; // Only used for $by_day;
		if ($by_day) {
			// For by day recurrence, we need to know what week it is, and what weekday.
			// (i.e. the 3rd Thursday of the month)

			// Calculate the Week Offset, as the ceiling value of date / DAYS_PER_WEEK
			$week = ceil($mdate / 7);
			// Store the weekday
			$wday = $dateinfo['wday'];
		}

		// Loop until we exceed the until date.
		do {
			// Append the current date to the list of dates.  $curdate will be updated
			// in the rest of the loop, so that it contains the next date.  This next date will
			// be checked in the while condition, and if it is still before the until date,
			// the loop iterates back here again for another go.
			$dates[] = $curdate;

			// Grab the date information for $curdate.  This gives us the current month
			// information, for the next jump.
			//$dateinfo = getdate($curdate);

			// Make the next month's timestamp, by adding frequency to the month.
			// PHP can pick up on the fact that the 13th month of this year is the 1st
			// month of the next year.
			$curdate = mktime(8,0,0,$dateinfo['mon']+$freq,1,$dateinfo['year']);
			$dateinfo = getdate($curdate);
			//eDebug($dateinfo);

			// Manually update the month and monthday.
			//eDebug($dateinfo);
			//$dateinfo['mon'] += $freq;  	//Bump the month to next month
			//eDebug($freq);
			//eDebug($dateinfo);
			//$dateinfo['mday'] = 1;		//Set the day of the month to the first.
			//eDebug($dateinfo);
			//exit();

			if ($by_day) {
				if ($dateinfo['wday'] > $wday) {
					$mdate = $wday - $dateinfo['wday'] + ( 7 * $week ) + 1;
					//echo "month day: ".$mdate."<br>";
				} elseif ($dateinfo['wday'] <= $wday) {
					$mdate = $wday - $dateinfo['wday'] + ( 7 * ( $week - 1 ) ) + 1;
					//echo "month day: ".$mdate."<br>";
				}

				// For by day recurrence (first tuesday of every month), we need to do a
				// little more fancy footwork to determine the next timestamp, since there
				// is no easy mathematical way to advance a whole month and land on
				// the same week offset and weekday.

				// Calculate the next month date.
				//echo "Weekday is: ".$wday."<br>";
				//if ($dateinfo['wday'] > $wday) {
					// The month starts on a week day that is after the target week day.
					// For more detailed discussion of the following formula, see the
					// analysis docs, sdk/analysis/subsystems/datetime.txt

					// TARGET_WDAY is $wday
					// START_WDAY is $startmonthinfo['wday']
					//eDebug($dateinfo);
					//echo 'mdate = $wday - $dateinfo[\'wday\'] + ( 7 * $week ) + 1;<br>';
					//echo "mdate = ".$wday." - ".$dateinfo['wday']." + ( 7 * ".$week." ) + 1<br>";
					//$mdate = $wday - $dateinfo['wday'] + ( 7 * $week ) + 1;
					//echo "mdate: ".$mdate."<br>";
				//} else {
					// The month starts on a week day that is before or equal to the
					// target week day.  This formula is identical to the one above,
					// except that we subtract one from the week offset
					// For more detailed discussion of the following formula, see the
					// analysis docs, sdk/analysis/subsystems/datetime.txt

					// TARGET_WDAY is $wday
					// START_WDAY is $startmonthinfo['wday']
					//$mdate = $wday - $dateinfo['wday'] + ( 7 * ( $week - 1 ) ) + 1;
				//}

			}

			// Re-assemble the $curdate value, using the correct $mdate.  If not doing by_day
			// recurrence, this value remains essentially unchanged.  Otherwise, it will be
			// set to reflect the new day of the Nth weekday.
			//echo "month: ".$dateinfo['mon']."<br>";
			//echo "mdate: ".$mdate."<br>";
			$curdate = self::startOfDayTimestamp(mktime(8,0,0,$dateinfo['mon'],$mdate,$dateinfo['year']));
		} while ($curdate <= $end);

		//exit();
		return $dates;
	}

	/** exdoc
	 * Finds all of the dates that fall within the yearly recurrence criteria
	 * (similar to monthly) and within the $start to $end timestamp range.
	 * Unlike monthly recurrence, yearly cannot do recurrence like 'the
	 * 17th sunday of the year'.
	 *
	 * @param int $start The start of the recurrence range
	 * @param int $end The end of the recurrence range
	 * @param integer $freq Yearly frequency - 1 means every year, 2 means every
	 *   other year, etc.
	 * @return array
	 * @node Subsystems:expDateTime
	 */
	public static function recurringYearlyDates($start,$end,$freq) {
		$dates = array();

		$freq = '+'.$freq.' year';
		while ($start <= $end) {
			$dates[] = $start;
			$start = strtotime($freq,$start);
		}

		return $dates;
	}

    /** exdoc
     * Adapted from calendar module's minical view to be more modular.
     *
     * @param null $time
     *
     * @return array
     */
	public static function monthlyDaysTimestamp($time=null) {
//		global $db;
		$monthly = array();
        if (empty($time)) $time = time();
        $info = getdate($time);
		// Grab non-day numbers only (before end of month)
        $week = date('W',expDateTime::startOfWeekTimestamp($time));

		$infofirst = getdate(mktime(0,0,0,$info['mon'],1,$info['year']));

		if ($infofirst['wday'] == 0) $monthly[$week] = array(); // initialize for non days
		for ($i = 0 - $infofirst['wday'] + intval(DISPLAY_START_OF_WEEK); $i < 0; $i++) {
//			$monthly[0][$i] = array("ts"=>-1);
            $monthly[$week][$i] = array("ts"=>-1);
		}

//		$weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
//        if ($i) {
//            $weekday -= DISPLAY_START_OF_WEEK;
//        }
        $weekday = count($monthly[$week]);

		$endofmonth = date('t', $time);

		for ($i = 1; $i <= $endofmonth; $i++) {
			$start = mktime(0,0,0,$info['mon'],$i,$info['year']);
//			if ($i == $info['mday']) $currentweek = $week;

			$monthly[$week][$i] = array("ts"=>$start);
			if ($weekday >= 6) {
				$week++;
				$monthly[$week] = array(); // allocate an array for the next week
				$weekday = 0;
			} else $weekday++;
		}

		// Grab non-day numbers only (after end of month)
		for ($i = 1; $weekday && $i <= (7-$weekday); $i++) $monthly[$week][$i+$endofmonth] = array("ts"=>-1);
        if (empty($monthly[$week])) unset($monthly[$week]);

		return $monthly;
	}

    /**
     * Returns date as a relative phrase (2 days ago, etc..)
     *
     * @param $posted_date
     *
     * @return string
     */
    public static function relativeDate($posted_date) {
		/**
			This function returns either a relative date or a formatted date depending
			on the difference between the current datetime and the datetime passed.
				$posted_date should be in the following format: YYYYMMDDHHMMSS
				define('SIMPLEPIE_RELATIVE_DATE', 'YmdHis'); // We'll define this here so we won't have to remember it later.

			Relative dates look something like this:
				3 weeks, 4 days ago
			Formatted dates look like this:
				on 02/18/2004

			The function includes 'ago' or 'on' and assumes you'll properly add a word
			like 'Posted ' before the function output.

			By Garrett Murray, http://graveyard.maniacalrage.net/etc/relative/
		**/
        $diff = time()-$posted_date;
		$fposted_date = date('YmdGis',$posted_date);  // convert to expected format

//		$in_seconds = strtotime(substr($fposted_date,0,8).' '.
//					  substr($fposted_date,8,2).':'.
//					  substr($fposted_date,10,2).':'.
//					  substr($fposted_date,12,2));
//		$diff = time()-$in_seconds;
		$future = $diff < 0 ? true : false;
		$diff = abs($diff);
		$months = floor($diff/2592000);
		$diff -= $months*2419200;
		$weeks = floor($diff/604800);
		$diff -= $weeks*604800;
		$days = floor($diff/86400);
		$diff -= $days*86400;
		$hours = floor($diff/3600);
		$diff -= $hours*3600;
		$minutes = floor($diff/60);
		$diff -= $minutes*60;
		$seconds = $diff;

		$relative_date = '';

		if ($months>0) {
			// over a month old, just show date (mm/dd/yyyy format)
//			return 'on '.substr($fposted_date,4,2).'/'.substr($fposted_date,6,2).'/'.substr($fposted_date,0,4);
            return 'on '.self::format_date($posted_date);
		} else {
			if ($weeks>0) {
				// weeks and days
				$relative_date .= ($relative_date?', ':'').$weeks.' week'.($weeks>1?'s':'');
				$relative_date .= $days>0?($relative_date?', ':'').$days.' day'.($days>1?'s':''):'';
			} elseif ($days>0) {
				// days and hours
				$relative_date .= ($relative_date?', ':'').$days.' day'.($days>1?'s':'');
				$relative_date .= $hours>0?($relative_date?', ':'').$hours.' hour'.($hours>1?'s':''):'';
			} elseif ($hours>0) {
				// hours and minutes
				$relative_date .= ($relative_date?', ':'').$hours.' hour'.($hours>1?'s':'');
				$relative_date .= $minutes>0?($relative_date?', ':'').$minutes.' minute'.($minutes>1?'s':''):'';
			} elseif ($minutes>0) {
				// minutes only
				$relative_date .= ($relative_date?', ':'').$minutes.' minute'.($minutes>1?'s':'');
			} else {
				// seconds only
				$relative_date .= ($relative_date?', ':'').$seconds.' second'.($seconds>1?'s':'');
			}
		}
		// show relative date and add proper verbiage
		if ($future) {
			return 'in '.$relative_date;
		} else {
			return $relative_date.' ago';
		}
	}

    /**
     * Return a date in the preferred format
     *
     * @param        array
     * @param string $format
     *
     * @return array
     */
    public static function format_date($timestamp,$format=DISPLAY_DATE_FORMAT) {
    	// Do some sort of mangling of the format for windows.
    	// reference the PHP_OS constant to figure that one out.
    	if (strtolower(substr(PHP_OS,0,3)) == 'win') {
    		// We are running on a windows platform.  Run the replacements

    		// Preserve the '%%'
    		$toks = explode('%%',$format);
    		for ($i = 0; $i < count($toks); $i++) {
    			$toks[$i] = str_replace(
    				array('%D','%e','%g','%G','%h','%r','%R','%T','%l'),
    				array('%m/%d/%y','%#d','%y','%Y','%b','%I:%M:%S %p','%H:%M','%H:%M:%S','%#I'),
    				$toks[$i]);
    		}
    		$format = implode('%%',$toks);
    	}
    	return strftime($format,$timestamp);
    }

    /**
     * Function to check if dates are same day
     *
     * @param $date1
     * @param $date2
     *
     * @return bool
     */
    public static function sameDay($date1, $date2) {
        return (date("Y-m-d",$date1) == date("Y-m-d",$date2));
    }
}

?>