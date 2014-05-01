<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * @subpackage Models
 * @package Core
 */
class ecomconfig extends expConfig {
    public static function getConfig($configname) {
        
        /**
         * this allows the sourcing on the store config to stay consistent.
         * This way, when we call ecomconfig::getConfig('config_we_want') we
         * don't get unexpected results
         *
         * @author Phillip Ball
         */
        $config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
        
        //$config = new expConfig(expCore::makeLocation('ecomconfig'));
        if (isset($config->config[$configname])) {
            return $config->config[$configname];
        } else {
            return null;
        }
    }
	
	public static function splitConfigUpCharge($upcharge = array(), $type = 'region') {
		$upchargeRate = array();
        if(count($upcharge))
        {
		    foreach($upcharge as $key => $item) {
			    $tmp = explode('_', $key);
			    if($tmp[0] == $type) {
				    $upchargeRate[$tmp[1]] = $item;
			    }
		    }
		}
		return $upchargeRate;
	}
}

?>