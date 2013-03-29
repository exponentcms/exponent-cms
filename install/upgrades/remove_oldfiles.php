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
 * This is the class remove_oldfiles
 */
class remove_oldfiles extends upgradescript {
	protected $from_version = '0.0.0';
//	protected $to_version = '2.0.9';
    public $priority = 90; // set this to a very low priority, since some scripts run based on existing files

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove old files"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Several files have been moved or are no longer needed.  This Script removes those leftover files from previous versions."; }

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
            // misc old files
            'captcha.php',
            'captcha_why.php',
            'compat.php',
            'content_selector.php',
            'core_podcast.php',
            'core_rss.php',
            'db_recover.php',
            'deps.php',
            'edit_page.php',
            'exponent_compat.php',
            'exponent_setup.php',
            'exponent_variables.php',
            'iconspopup.php',
            'login_redirect.php',
            'manifest.php',
            'mod_preview.php',
            'module_preview.php',
            'orphan_content_selector.php',
            'orphan_source_selector.php',
            'podcast.php',
            'rss.php',
            'Makefile',
            'ABOUT',
            'CHANGELOG',
            'CREDITS',
            'INSTALLATION',
            'README',
            'RELEASE',
            'TODO',
            'readme-reminders.txt',
            'external/adminer/adminer-3.6.1*',
            'external/editors/_header.tpl',
            'external/editors/Default.tpl',
            'external/editors/FCKeditor.tpl',
            'external/editors/fcktoolbarconfig.js.php',
            'external/editors/fcktemplates.xml',
            'external/editors/fckstyles.xml',
            'external/editors/wysiwyg-styles.css',
            'external/editors/ckeditor/ckeditor_basic.js',
            'external/editors/ckeditor/ckeditor_basic_source.js',
            'external/editors/ckeditor/ckeditor_php5.php',
            'external/editors/ckeditor/ckeditor_source.js',
            'external/editors/connector/FCKeditor_link.php',
            'external/editors/connector/insert_image.php',
            'external/editors/connector/link.php',
            'external/editors/connector/popup.js',
            'external/editors/connector/section_linked.php',
            'external/jquery/js/jquery-1.8.3.js',
            'external/jquery/js/jquery-1.8.3.min.js',
            'external/jquery/js/jquery-1.9.0.js',
            'external/jquery/js/jquery-1.9.0.min.js',
            'external/jquery/js/jquery-ui-1.9.1.custom.js',
            'external/jquery/js/jquery-ui-1.9.1.custom.min.js',
            'external/jquery/js/jquery-ui-1.10.0.custom.js',
            'external/jquery/js/jquery-ui-1.10.0.custom.min.js',
            'external/jquery/js/jquery-ui-1.10.1.custom.js',
            'external/jquery/js/jquery-ui-1.10.1.custom.min.js',
            'framework/modules/text//views//text/showall_merge.tpl',
            'framework/modules/filedownloads/views/filedownload/showall_oneclickdownload.tpl',
            'framework/modules/common/views/configure/module_title.tpl',
            'framework/plugins/function.get_distance.php',
            'framework/plugins/function.get_favorites.php',
            'framework/plugins/function.getfilename.php',
            'framework/plugins/function.popupdatetime.php',
            'framework/plugins/modifier.html_substr.php',
            'framework/plugins/postfilter.includemiscfiles.php',
            'themes/basetheme/sample.eql',
            'themes/basetheme/sample.tar.gz',
            'themes/coolwatertheme/sample.eql',
            'themes/coolwatertheme/sample.tar.gz',
            'themes/multioptiontheme/sample.eql',
            'themes/multioptiontheme/sample.tar.gz',
            'themes/retrotheme/sample.eql',
            'themes/retrotheme/sample.tar.gz',
            'themes/simpletheme/sample.eql',
            'themes/simpletheme/sample.tar.gz',
            'install/pages/admin_user.php',
            'install/pages/dbcheck.php',
            'install/pages/dbconfig.php',
            'install/pages/sanity.php',
            'install/pages/save_admin.php',
            'install/pages/setlang.php',
            'install/pages/upgrade.php',
            'install/pages/upgrade_version.php',
            'install/upgrades/install_tables.php',
            'install/upgrades/convert_db_trim.php',
            'install/upgrades/remove_exp1_faqmodule.php',
            'install/upgrades/remove_locationref.php',
            'install/upgrades/upgrade_attachableitem_tables.php',
            // obsolete definitions/models
            'framework/core/definitions/bots.php',
            'framework/core/definitions/locationref.php',
            'framework/core/definitions/toolbar_FCKeditor.php',
            'framework/core/definitions/calendar.php',
            'framework/core/definitions/calendar_external.php',
            'framework/core/definitions/calendar_reminder_address.php',
            'framework/core/definitions/calendarmodule_config.php',
            'framework/core/definitions/eventdate.php',
            'framework/modules/news/models/rssfeed.php',
            // moved definitions/models
            'framework/core/definitions/expFiles.php',
            'framework/core/definitions/content_expFiles.php',
            'framework/core/models/expFile.php',
            'framework/core/definitions/expRss.php',
            'framework/core/models/expRss.php',
            'framework/core/definitions/subscribers.php',
            'framework/core/models/subscribers.php',
            'framework/core/models/expTwitter.php',
            'framework/modules/twitter/models/expTwitter.php',
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
             // ecommerce renamed views
            'framework/modules/ecommerce/views/store/showall_featured_products.tpl',
            'framework/modules/ecommerce/views/store/events_calendar.tpl',
            'framework/modules/ecommerce/views/store/search_by_model_old.tpl',
            'framework/modules/ecommerce/views/store/showall_category_featured_products.tpl',
            'framework/modules/ecommerce/views/store/upcoming_events.tpl',
            // core controllers/models/definitions moved to /framework/modules folder
            'framework/core/controllers/expCatController.php',
            'framework/core/controllers/expCommentController.php',
            'framework/core/controllers/expDefinableFieldController.php',
            'framework/core/controllers/expHTMLEditorController.php',
            'framework/core/controllers/expModuleController.php',
            'framework/core/controllers/expSimpleNoteController.php',
            'framework/core/controllers/expRatingController.php',
            'framework/core/controllers/expTagController.php',
            'framework/core/definitions/content_expCats.php',
            'framework/core/definitions/content_expComments.php',
            'framework/core/definitions/content_expDefinableFields.php',
            'framework/core/definitions/content_expDefinableFields_value.php',
            'framework/core/definitions/content_expSimpleNote.php',
            'framework/core/definitions/content_expRatings.php',
            'framework/core/definitions/content_expTags.php',
            'framework/core/definitions/expCats.php',
            'framework/core/definitions/expComments.php',
            'framework/core/definitions/expDefinableFields.php',
            'framework/core/definitions/expSimpleNote.php',
            'framework/core/definitions/expRatings.php',
            'framework/core/definitions/expTags.php',
            'framework/core/definitions/htmleditor_ckeditor.php',
            'framework/core/models/expCat.php',
            'framework/core/models/expComment.php',
            'framework/core/models/expDefinableField.php',
            'framework/core/models/expSimpleNote.php',
            'framework/core/models/expRating.php',
            'framework/core/models/expTag.php',
        );
		// check if the old file exists and remove it
        $files_removed = 0;
        foreach ($oldfiles as $file) {
            if (file_exists(BASE.$file)) {
                if (unlink(BASE.$file)) $files_removed++;
            }
        }
        // delete old directories
        $olddirs = array(
            "framework/subsystems/",
            "framework/core/compat/",
            "framework/core/database/",
            "framework/core/datatypes/",
            "framework/core/models-1/",
            "framework/core/js/",
            "framework/core/subsystems-1/",
            "framework/core/subsystems/forms/",
            "framework/modules-1/",
            "framework/modules/photoalbum/views/photos/",
            "framework/modules/expEvent/",
            "framework/datatypes/",
            "framework/views/",
            "plugins/",
            "modules/",
            "js/",
            "forms/",
            "extensionuploads/",
            "datatypes/",
            "compat/",
            "views/",
            "subsystems/",
            "conf/",
            "install/sitetypes/",
            "themes/common/",
            "themes/bootstraptheme/controls/",
            "themes/bootstraptheme/plugins/",
            "tmp/js/",
            "tmp/mail/",
            "tmp/pods/",
            "external/editors/connector/lang/",
            "external/editors/FCKeditor/",
            "external/editors/ckeditor/adapters",
            "external/editors/ckeditor/images",
            "external/editors/ckeditor/skins/office2003",
            "external/editors/ckeditor/skins/v2",
            "external/editors/images/",
            "external/ckeditor/",
            "external/fedex-php/",
            "external/flowplayer3/",
            "external/flowplayer-3.2.12/",
            "external/flowplayer-3.2.15/",
            "external/magpierss/",
            "external/yui3/",
            "external/lissa/",
            "external/yui/3.4.0/",
            "external/yui/3.7.2/",
            "external/yui/3.7.3/",
            "external/yui/3.8.0/",
            "external/yui/3.9.0/",
            "external/Smarty/",
            "external/Smarty-2/",
            "external/Smarty-3.1.4/",
            "external/Smarty-3.1.7/",
            "external/Smarty-3.1.8/",
            "external/Smarty-3.1.11/",
            "external/Smarty-3.1.12/",
            "external/Swift/",
            "external/Swift-4/",
            "external/Swift-4.0.5/",
            "external/Swift-4.1.1/",
            "external/Swift-4.1.3/",
            "external/Swift-4.1.4/",
            "external/Swift-4.1.5/",
            "external/Swift-4.1.6/",
            "external/Swift-4.1.7/",
            "external/Swift-4.2.1/",
            "external/Swift-4.2.2/",
            "external/Swift-4.2.3/",
        );
        foreach ($olddirs as $dir) {
            if (expUtil::isReallyWritable(BASE.$dir)) {
                expFile::removeDirectory(BASE.$dir);
            }
        }

		return ($files_removed?$files_removed:gt('No'))." ".gt("obsolete files and folders were removed.");
		
	}
}

?>
