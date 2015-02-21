<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
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
 * This is the class fix_sef_urls
 */
class fix_sef_urls extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.2.1';
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Validate and fix module item sef urls"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In some instances, the sef urls may be duplicated within a module.  This script searches for duplicate sef urls and renames them.
	This script can take a long time for a site with many items (eCommerce)!"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true; // we will always run
	}

	/**
	 * Reranks pages/form controls with index start of 1
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        // first get all the system models
        $models = expModules::initializeModels();
        $fixed = 0;

        foreach ($models as $modelname=>$modelpath) {
            $model = new $modelname();
            if (property_exists($model, 'sef_url')) {
               // adjust forms control ranks
                $items = $model->find();
                foreach ($items as $item) {
                    if (!is_bool(expValidator::uniqueness_of('sef_url', $item, array()))) {
                        $item->makeSefUrl();
                        $item->update();
                        $fixed++;
                    }
                }
            }
        }

        return ($fixed?$fixed:gt('No')).' '.gt('duplicate sef urls were found and corrected');
	}

}

?>
