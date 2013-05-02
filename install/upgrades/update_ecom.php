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
 * This is the class update_ecom
 */
class update_ecom extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.9';  // code was changed in 2.0.9

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Updates e-Commerce header/footer to new format"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.0.9, the e-Commerce header/footer configuration was revised.  This Script updates those entries."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        $config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
        if (defined('ECOM') && (!empty($config->config['header']) || !empty($config->config['footer']))) {
            return true;
        } else return false;
	}

	/**
	 * updates new ecom header/footer properties/fields
	 * @return bool
	 */
	function upgrade() {
        $fixed = 0;
        $config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
        if (!empty($config->config['header'])) {
            $config->config['ecomheader'] = $config->config['header'];
            unset ($config->config['header']);
            $fixed++;
        }
        if (!empty($config->config['footer'])) {
            $config->config['ecomfooter'] = $config->config['footer'];
            unset ($config->config['footer']);
            $fixed++;
        }
        $config->update(array('config'=>$config->config));
        return ($fixed?$fixed:gt('No')).' '.gt('e-Commerce settings were corrected');
	}

}

?>
