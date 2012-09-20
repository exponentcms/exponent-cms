<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * This is the class remove_oldfiles
 */
class remove_oldfiles extends upgradescript {
	protected $from_version = '1.99.0';
	protected $to_version = '2.0.9';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove old definition and model files"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Several model and definition files were moved from the core folder into their specific module folder in v2.0.8.  This Script removes the leftover files from previous versions."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
   		return true;  // subclasses MUST return true to be run
   	}

	/**
	 * removes old files from a previous installation that now in a new location
	 * @return bool
	 */
	function upgrade() {

        $oldfiles = array (
            // obsolete definitions/models
            'framework/core/definitions/bots.php',
            'framework/core/definitions/locationref.php',
            'framework/core/definitions/toolbar_FCKeditor.php',
            'framework/core/models-1/database_importer.php',
            'framework/core/models-1/file_collection.php',
            // moved definitions/models
            'framework/core/definitions/expFiles.php',
            'framework/core/definitions/content_expFiles.php',
            'framework/core/models/expFile.php',
            'framework/core/definitions/expRss.php',
            'framework/core/models/expRss.php',
            'framework/core/definitions/subscribers.php',
            'framework/core/models/subscribers.php',
            'framework/core/models/expTwitter.php',
            'framework/core/definitions/search_extension.php',
            'framework/core/definitions/search_queries.php',
            // ecommerce definitions
            'framework/core/definitions/billingcalculator.php',
            'framework/core/definitions/billingmethods.php',
            'framework/core/definitions/billingtransactions.php',
            'framework/core/definitions/bing_product_types.php',
            'framework/core/definitions/bing_product_types_storeCategories.php',
            'framework/core/definitions/crosssellItem_product.php',
            'framework/core/definitions/discounts.php',
            'framework/core/definitions/eventregistration.php',
            'framework/core/definitions/eventregistration_registrants.php',
            'framework/core/definitions/external_addresses.php',
            'framework/core/definitions/google_product_types.php',
            'framework/core/definitions/google_product_types_storeCategories.php',
            'framework/core/definitions/groupdiscounts.php',
            'framework/core/definitions/model_aliases.php',
            'framework/core/definitions/model_aliases_tmp.php',
            'framework/core/definitions/nextag_product_types.php',
            'framework/core/definitions/nextag_product_types_storeCategories.php',
            'framework/core/definitions/option.php',
            'framework/core/definitions/option_master.php',
            'framework/core/definitions/optiongroup.php',
            'framework/core/definitions/optiongroup_master.php',
            'framework/core/definitions/order_discounts.php',
            'framework/core/definitions/order_payments.php',
            'framework/core/definitions/order_status.php',
            'framework/core/definitions/order_status_changes.php',
            'framework/core/definitions/order_status_messages.php',
            'framework/core/definitions/order_type.php',
            'framework/core/definitions/orderitems.php',
            'framework/core/definitions/orders.php',
            'framework/core/definitions/orders_next_invoice_id.php',
            'framework/core/definitions/pricegrabber_product_types.php',
            'framework/core/definitions/pricegrabber_product_types_storeCategories.php',
            'framework/core/definitions/product.php',
            'framework/core/definitions/product_notes.php',
            'framework/core/definitions/product_status.php',
            'framework/core/definitions/product_storeCategories.php',
            'framework/core/definitions/promocodes.php',
            'framework/core/definitions/purchase_order.php',
            'framework/core/definitions/sales_reps.php',
            'framework/core/definitions/shippingcalculator.php',
            'framework/core/definitions/shippingmethods.php',
            'framework/core/definitions/shippingspeeds.php',
            'framework/core/definitions/shopping_product_types.php',
            'framework/core/definitions/shopping_product_types_storeCategories.php',
            'framework/core/definitions/shopzilla_product_types.php',
            'framework/core/definitions/shopzilla_product_types_storeCategories.php',
            'framework/core/definitions/storeCategories.php',
            'framework/core/definitions/tax_class.php',
            'framework/core/definitions/tax_geo.php',
            'framework/core/definitions/tax_rate.php',
            'framework/core/definitions/tax_zone.php',
            'framework/core/definitions/vendor.php',
            // ecommerce models
            'framework/core/models/billing.php',
            'framework/core/models/billingcalculator.php',
            'framework/core/models/billingmethod.php',
            'framework/core/models/billingtransaction.php',
            'framework/core/models/bing_product_types.php',
            'framework/core/models/childProduct.php',
            'framework/core/models/crosssellItem.php',
            'framework/core/models/discounts.php',
            'framework/core/models/ecomconfig.php',
            'framework/core/models/external_address.php',
            'framework/core/models/google_product_types.php',
            'framework/core/models/groupdiscounts.php',
            'framework/core/models/model_alias.php',
            'framework/core/models/nextag_product_types.php',
            'framework/core/models/option.php',
            'framework/core/models/option_master.php',
            'framework/core/models/optiongroup.php',
            'framework/core/models/optiongroup_master.php',
            'framework/core/models/order.php',
            'framework/core/models/order_discounts.php',
            'framework/core/models/order_status.php',
            'framework/core/models/order_status_changes.php',
            'framework/core/models/order_status_messages.php',
            'framework/core/models/order_type.php',
            'framework/core/models/orderitem.php',
            'framework/core/models/pricegrabber_product_types.php',
            'framework/core/models/product_notes.php',
            'framework/core/models/product_status.php',
            'framework/core/models/product_type.php',
            'framework/core/models/promocodes.php',
            'framework/core/models/purchase_order.php',
            'framework/core/models/sales_rep.php',
            'framework/core/models/shipping.php',
            'framework/core/models/shippingcalculator.php',
            'framework/core/models/shippingmethod.php',
            'framework/core/models/shippingspeeds.php',
            'framework/core/models/shopping_product_types.php',
            'framework/core/models/shopzilla_product_types.php',
            'framework/core/models/storeCategory.php',
            'framework/core/models/storeCategoryFeeds.php',
            'framework/core/models/taxclass.php',
            'framework/core/models/vendor.php',
        );
		// check if the old file exists and remove it
        $files_removed = 0;
        foreach ($oldfiles as $file) {
            if (file_exists(BASE.$file)) {
                if (unlink(BASE.$file)) $files_removed++;
            }
        }
        // while we're at it, check if the old subsystems-1 folder still exists
        if (expUtil::isReallyWritable(BASE."framework/core/subsystems-1/")) {
            expFile::removeDirectory(BASE."framework/core/subsystems-1/");
            $files_removed++;
        }
        // while we're at it, check if the old administrationmodule folder still exists
        if (expUtil::isReallyWritable(BASE."framework/modules-1/administrationmodule/")) {
            expFile::removeDirectory(BASE."framework/modules-1/administrationmodule/");
            $files_removed++;
        }
        // while we're at it, check if the old bots folder still exists
        if (expUtil::isReallyWritable(BASE."framework/modules-1/bots/")) {
            expFile::removeDirectory(BASE."framework/modules-1/bots/");
            $files_removed++;
        }
        // while we're at it, check if the old loginmodule folder still exists
        if (expUtil::isReallyWritable(BASE."framework/modules-1/loginmodule/")) {
            expFile::removeDirectory(BASE."framework/modules-1/loginmodule/");
            $files_removed++;
        }
        // while we're at it, check if the old photos folder still exists
        if (expUtil::isReallyWritable(BASE."framework/modules/photoalbum/views/photos/")) {
            expFile::removeDirectory(BASE."framework/modules/photoalbum/views/photos/");
            $files_removed++;
        }

		return ($files_removed?$files_removed:gt('No'))." ".gt("obsolete files and folders were removed.");
		
	}
}

?>
