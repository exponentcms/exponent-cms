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

class freeshippingcalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return exponent_lang_getText('Free Shipping'); }
	public function description() { return exponent_lang_getText('Use this to offer customers free shipping'); }
	public function hasUserForm() { return true; }
	public function hasConfig() { return true; }
	public function addressRequired() { return true; }
	public function isSelectable() { return true; }

    public $shippingmethods = array("01"=>"Free Shipping");

    public function getRates($order) {        
	    $rates = array('01'=>array('id'=>'01','title'=>'Free','cost'=>0));
	    return $rates;
    }	
    
   	public function configForm() { 
   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/freeshippingcalculator/configure.tpl';
   	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array('rate');
	    foreach ($config_vars as $varname) {
	        if ($varname == 'rate') {
	            $config[$varname] = isset($values[$varname]) ? preg_replace("/[^0-9.]/","",$values[$varname]) : null;    
	        } else {
	            $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
	        }
	        
	    }
	    
		return $config;
	}
	
	function availableMethods() {
	    return $this->shippingmethods;
	}
}

?>
