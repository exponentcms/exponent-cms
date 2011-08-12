<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class ecomconfig extends expConfig {
    public static function getConfig($configname) {
        
        /**
         * this allows the sourcing on the store config to stay consistent.
         * This way, when we call ecomconfig::getConfig('configwewant') we 
         * don't get unexpected results
         *
         * @author Phillip Ball
         */
        
        $cfg->mod = "ecomconfig";
        $cfg->src = "@globalstoresettings";
        $cfg->int = "";
        $config = new expConfig($cfg);
        
        
        //$config = new expConfig(makeLocation('ecomconfig'));
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
