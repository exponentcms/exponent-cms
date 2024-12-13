<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * This is the class update_ecom3
 *
 * @package Installation
 * @subpackage Upgrade
 */
class update_ecom4 extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.3.4';  // donations and event registrations do not have the 'no_shipping' property set
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Mark donations and event registrations for No Shipping"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.3.4, we didn't properly set the 'no_shipping' property on donations and event registrations which might increase the shipping cost for an order.  This script corrects that."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

		return $db->countObjects('product', '(product_type="donation" OR product_type="eventregistration") AND no_shipping=0');  // only needed if there are donations/event registrations without set 'no_shipping'
	}

	/**
	 * prunes orphan records from orders and orderitems tables
	 * @return string
	 */
	function upgrade() {
	    global $db;

        $product_count = $db->countObjects('product', '(product_type="donation" OR product_type="eventregistration") AND no_shipping=0');
        if ($product_count)
            $db->columnUpdate('product', 'no_shipping', 1, '(product_type="donation" OR product_type="eventregistration") AND no_shipping=0');
		return ($product_count?$product_count:gt('No'))." ".gt("Donations or Event Registrations are now marked for no shipping in the database.");
	}
}

?>
