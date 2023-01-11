<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * This is the class update_useragent
 *
 * @package Installation
 * @subpackage Upgrade
 */
class update_useragent extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.7.0';  // issue was fixed in v2.7.0
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Scrub HTTP_USER_AGENT records"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Users can hack a site using their HTTP_USER_AGENT.  This script scrubs any old bad entries in our database."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // always needed on previous versions
	}

	/**
	 * prunes orphan records from orders and orderitems tables
	 * @return string
	 */
	function upgrade() {
	    global $db;

        $ticketcount = 0;
        foreach ($db->selectObjects('sessionticket') as $ticket) {
            $test = expString::sanitize($ticket->browser);
            if ($test != $ticket->browser) {
                $ticket->browser = $test;
                $db->updateObject($ticket,'sessionticket');
                $ticketcount++;
            }
	    }

        $trackingcount = 0;
        foreach ($db->selectObjects('tracking_rawdata') as $ticket) {
            $test = expString::sanitize($ticket->user_agent);
            if ($test != $ticket->user_agent) {
                $ticket->user_agent = $test;
                $db->updateObject($ticket,'tracking_rawdata');
                $trackingcount++;
            }
	    }

        $redirectcount = 0;
        foreach ($db->selectObjects('redirect') as $ticket) {
            $test = expString::sanitize($ticket->user_agent);
            if ($test != $ticket->user_agent) {
                $ticket->user_agent = $test;
                $db->updateObject($ticket,'redirect');
                $redirectcount++;
            }
	    }

		return ($ticketcount?$ticketcount:gt('No'))." ".gt("Bad Session Tickets").", ".
            ($trackingcount?$trackingcount:gt('No'))." ".gt("Bad Raw Trackers and")." ".
            ($redirectcount?$redirectcount:gt('No'))." ".gt("Bad Redirects")." ".
            gt("were found and removed from the database.");
	}
}

?>
