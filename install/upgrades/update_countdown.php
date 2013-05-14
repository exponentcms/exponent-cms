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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class update_countdown
 */
class update_countdown extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.2.0';  // config date was changed in 2.2.0

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Updates Countdown module dates to new format"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.2.0, the countdown module was revised.  This Script updates those entries."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        $needed = $db->countObjects('container',"internal LIKE '%countdown%'");
        if ($needed) {
            return true;
        } else return false;
	}

	/**
	 * updates new ecom header/footer properties/fields
	 * @return bool
	 */
	function upgrade() {
        global $db;

        $fixed = 0;
        foreach ($db->selectObjects('expConfigs',"location_data LIKE '%countdown%'") as $config) {
            $cfg = expUnserialize($config->config);
            $date = explode(' ',$cfg['count']);
            $cfg['date-count'] = $date[0];
            $time = explode(':',$date[1]);
            $cfg['time-h-count'] = $time[0];
            $cfg['time-m-count'] = $time[1];
            $cfg['ampm-count'] = $date[2];
            unset ($cfg['count']);
            $config->config = serialize($cfg);
            $db->updateObject($config,'expConfigs');
            $fixed++;
        }
        return ($fixed?$fixed:gt('No')).' '.gt('Countdown modules settings were corrected');
	}

}

?>
