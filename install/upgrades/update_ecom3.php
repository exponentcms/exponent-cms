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
class update_ecom3 extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.3.2';  // orders table grows extremely large with every user visit to an ecommerce site
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Prune abandoned cart records"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "(CAUTION! Use only with a very recent DB backup!) An orders table record is added as the shopping cart for each site visitor which can make it huge.  This script prunes abandoned orders older than 7 days from the orders table and associated orphan records from other ecommerce tables."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

		return $db->countObjects('orders', "invoice_id = '0' OR purchased = 0");  // only needed if there if orders table is populated
	}

	/**
	 * prunes orphan records from orders and orderitems tables
	 * @return string
	 */
	function upgrade() {
	    global $db;

        // purchased == 0 or invoice_id == 0 on unsubmitted orders
		$orders_count = $db->countObjectsBySql("SELECT COUNT(*) as c FROM " . $db->tableStmt('orders') . " WHERE invoice_id = '0' AND edited_at < UNIX_TIMESTAMP(now()) - 604800 AND sessionticket_ticket NOT IN (SELECT ticket FROM " . $db->tableStmt('sessionticket') . ")");
        if ($orders_count)
    		$db->delete("orders","invoice_id = '0' AND edited_at < UNIX_TIMESTAMP(now()) - 604800 AND sessionticket_ticket NOT IN (SELECT ticket FROM " . $db->tableStmt('sessionticket') . ")");
		$orderitems_count = $db->countObjectsBySql("SELECT COUNT(*) as c FROM " . $db->tableStmt('orderitems') . " WHERE orders_id NOT IN (SELECT id FROM " . $db->tableStmt('orders') . ")");
        if ($orderitems_count)
    		$db->delete("orderitems","'orders_id' NOT IN (SELECT id FROM " . $db->tableStmt('orders') . ")");
		$shippingmethods_count = $db->countObjectsBySql("SELECT COUNT(*) as c FROM " . $db->tableStmt('shippingmethods') . " WHERE id NOT IN (SELECT shippingmethods_id FROM " . $db->tableStmt('orders') . ")");
        if ($shippingmethods_count)
    		$db->delete("shippingmethods","id NOT IN (SELECT shippingmethods_id FROM " . $db->tableStmt('orders') . ")");
		return ($orders_count?$orders_count:gt('No'))." ".gt("orphaned Orders").", ".($orderitems_count?$orderitems_count:gt('No'))." ".gt("orphaned Order Items and")." ".($shippingmethods_count?$shippingmethods_count:gt('No'))." ".gt("orphaned Shipping Methods")." ".gt("were found and removed from the database.");
	}
}

?>
