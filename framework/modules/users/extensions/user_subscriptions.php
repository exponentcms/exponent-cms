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
 * @subpackage Profile-Extensions
 * @package Modules
 */
class user_subscriptions extends expRecord {

	/**
	 * @return string
	 */
	public function name() { return 'Subscriptions'; }

	/**
	 * @return string
	 */
	public function description() { return 'The extension allows users to manage their email alert subscriptions.'; }

	/**
	 * @param array $params
	 * @return bool
	 */
	public function update($params=array()) {
        global $db;

        // if not user id then we should not be doing anything here
        if (empty($params['user_id'])) return false;
        $this->user_id = $params['user_id'];
        
        // all user subscriptions have been deleted, we now must (re)create those selected
        if (!empty($params['expeAlert'])) {
            $subscription = new stdClass();
            $subscription->user_id = $this->user_id;
            foreach($params['expeAlert'] as $ealert) {
                $subscription->expeAlerts_id = $ealert;
                $db->insertObject($subscription,'user_subscriptions');
            }
        }
    }
}

?>