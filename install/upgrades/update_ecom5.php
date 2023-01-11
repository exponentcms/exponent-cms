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
 * This is the class update_ecom5
 *
 * @package Installation
 * @subpackage Upgrade
 */
class update_ecom5 extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.4.3';  // we began removing associated records in v2.4.3
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove orphaned product and store category records"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Products and Store Category associated records (child products) were not being removed.  This script removes orphaned ecommerce table records."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        if (ecom_active())
            return true;
        else
            return false;
	}

	/**
	 * removed orphaned records from product and store category associated tables
	 * @return string
	 */
	function upgrade() {
	    global $db;

		$missing_product_count = $db->countObjectsBySql("SELECT COUNT(*) as c FROM " . $db->tableStmt('product_storecategories') . " WHERE product_id != 0 AND product_id NOT IN (SELECT id FROM " . $db->tableStmt('product') . ")");
		if ($missing_product_count)
		    $db->delete("product_storecategories","product_id != 0 AND product_id NOT IN (SELECT id FROM " . $db->tableStmt('product') . ")");
		$missing_category_count = $db->countObjectsBySql("SELECT COUNT(*) as c FROM " . $db->tableStmt('product_storecategories') . " WHERE storecategories_id != 0 AND storecategories_id NOT IN (SELECT id FROM " . $db->tableStmt('storeCategories') . ")");
        if ($missing_category_count)
		    $db->delete("product_storecategories","storecategories_id != 0 AND storecategories_id NOT IN (SELECT id FROM " . $db->tableStmt('storeCategories') . ")");
		$orphan_product_count = $db->countObjectsBySql("SELECT COUNT(*) as c FROM " . $db->tableStmt('product') . " WHERE parent_id != 0 AND parent_id NOT IN (SELECT temp.id FROM (SELECT id FROM " . $db->tableStmt('product') . ") temp)");
        if ($orphan_product_count)
		    $db->delete("product","parent_id != 0 AND parent_id NOT IN (SELECT temp.id FROM (SELECT id FROM " . $db->tableStmt('product') . ") temp)");

		return ($missing_product_count?$missing_product_count:gt('No'))." ".gt("missing products").", ".($missing_category_count?$missing_category_count:gt('No'))." ".gt("missing categories and")." ".($orphan_product_count?$orphan_product_count:gt('No'))." ".gt("orphaned child products")." ".gt("were found and removed from the database.");
	}
}

?>
