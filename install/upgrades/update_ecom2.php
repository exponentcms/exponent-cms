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
 * This is the class update_ecom2
 *
 * @package Installation
 * @subpackage Upgrade
 */
class update_ecom2 extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.2.0';  // code was changed in 2.2.0

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update e-Commerce settings"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.2.0, some e-Commerce settings were revised.  This Script updates those entries."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
//        $config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
//        if (!empty($config)) {
//            return true;
//        } else return false;
        if (ecom_active()) return true;
        else return false;
	}

	/**
	 * updates new ecom header/footer properties/fields
	 * @return string
	 */
	function upgrade() {
//        global $db;

        $fixed = 0;
        // move cart settings into store settings
        $cartconfig = new expConfig(expCore::makeLocation("cart","@globalcartsettings",""));
        $config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
        if (!empty($cartconfig->config['min_order']) && empty($config->config['min_order'])) {
            $config->config['min_order'] = $cartconfig->config['min_order'];
            $fixed++;
        }
        if (!empty($cartconfig->config['policy']) && empty($config->config['policy'])) {
            $config->config['policy'] = $cartconfig->config['policy'];
            $fixed++;
        }
        if ($fixed) $config->update(array('config'=>$config->config));
        if (!empty($cartconfig)) $cartconfig->delete();

        // update the billing calculator details in the db
        $bcalc = new billingcalculator();
        $calcs = $bcalc->find('all',1);
        foreach ($calcs as $calc) {
            $calcobj = new $calc->calculator_name();
            if ($calcobj->isSelectable() == true) {
//                if ($calcobj->name() != $calc->title || $calcobj->title != $calc->user_title || $calcobj->description() != $calc->body) $fixed++;
                if ($calcobj->name() != $calc->title || $calcobj->description() != $calc->body) $fixed++;
                $calc->update(array(
                                    'title'=>$calcobj->name(),
//                                    'user_title'=>$calcobj->title,
                                    'body'=>$calcobj->description(),
                                ));
            }
        }

        // copy product summary into body if no body exists, summary deprecated
        $prod = new product();
        $prods = $prod->find('all','body IS NULL AND summary IS NOT NULL');
        foreach ($prods as $product) {
            $product->body = $product->summary;
            $product->update();
            $fixed++;
        }

        return ($fixed?$fixed:gt('No')).' '.gt('e-Commerce settings were corrected');
	}

}

?>
