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
class update_shipping_billing_calcs extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.3.1';  // shipping/billing caclculator text was changed in 2.3.1

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update stored e-Commerce shipping/billing calculator text"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Some e-Commerce calculators were revised.  This Script updates those entries."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        if (ecom_active()) {
            return true;
        } else {
            return false;
        }

	}

	/**
	 * updates stored ecom calculator fields
	 * @return string
	 */
	function upgrade() {
        global $db;

        $calculators = array();
        $calc_dirs = array(
            THEME_ABSOLUTE . "modules/ecommerce/billingcalculators",
            BASE . "framework/modules/ecommerce/billingcalculators",
        );
        foreach ($calc_dirs as $dir) {
            if (is_readable($dir)) {
                $dh = opendir($dir);
                while (($file = readdir($dh)) !== false) {
                    if (is_file("$dir/$file") && substr("$dir/$file", -4) == ".php") {
                        if (array_key_exists(substr($file, 0, -4), $calculators)) {
                            continue;
                        }
                        include_once("$dir/$file");
                        $classname = substr($file, 0, -4);
                        $id = $db->selectValue('billingcalculator', 'id', 'calculator_name=\'' . $classname . '\'');
                        $calcobj = new $classname($id);
                        if ((method_exists($calcobj, 'isSelectable')) && $calcobj->isSelectable() == true) {
                            $calcobj->update(
                                array(
                                    'title' => $calcobj->name(),
                                    'body' => $calcobj->description(),
                                    'calculator_name' => $classname,
                                )
                            );
                        }
                        $calculators[$calcobj->classname] = $calcobj;
                    }
                }
            }
        }

        $calculators = array();
        $calc_dirs = array(
            THEME_ABSOLUTE . "modules/ecommerce/shippingcalculators",
            BASE . "framework/modules/ecommerce/shippingcalculators",
        );
        foreach ($calc_dirs as $dir) {
            if (is_readable($dir)) {
                $dh = opendir($dir);
                while (($file = readdir($dh)) !== false) {
                    if (is_file("$dir/$file") && substr("$dir/$file", -4) == ".php") {
                        if (array_key_exists(substr($file, 0, -4), $calculators)) {
                            continue;
                        }
                        include_once("$dir/$file");
                        $classname = substr($file, 0, -4);
                        $id = $db->selectValue('shippingcalculator', 'id', 'calculator_name=\'' . $classname . '\'');
                        $calcobj = new $classname($id);
                        if ($calcobj->isSelectable() == true) {
                            $calcobj->update(
                                array(
                                    'title' => $calcobj->name(),
                                    'body' => $calcobj->description(),
                                    'calculator_name' => $classname
                                )
                            );
                        }
                        $calculators[$calcobj->classname] = $calcobj;
                    }
                }
            }
        }

        return gt('e-Commerce calculator stored settings were updated');
	}

}

?>
