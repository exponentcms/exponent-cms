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
 * @subpackage Calculators
 * @package Modules
 */

class freeshippingcalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return gt('Free Shipping Calculator'); }
	public function description() { return gt('Use this to offer customers free shipping.'); }
	public function hasUserForm() { return true; }
	public function hasConfig() { return true; }
	public function addressRequired() { return true; }
	public function isSelectable() { return true; }

    public $shippingmethods = array("01"=>"Free Shipping");

    public function __construct($params)
    {
        parent::__construct($params);
        if(isset($this->configdata['free_shipping_method_default_name']))
        {
            $this->shippingmethods["01"] = $this->configdata['free_shipping_method_default_name'];
        }
    }
    
    public function getRates($order) {                        
        if(isset($this->configdata['free_shipping_option_default_name']))
        {
            $title = $this->configdata['free_shipping_option_default_name'];
        }
        else
        {
            $title = "Free";
        }
	    $rates = array('01'=>array('id'=>'01','title'=>$title,'cost'=>0));        
	    return $rates;
    }	
    
   	public function configForm() { 
   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/freeshippingcalculator/configure.tpl';
   	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array('free_shipping_option_default_name','free_shipping_method_default_name');
	    foreach ($config_vars as $varname) {	        
	        $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
	    }   	    
		return $config;
	}
	
	function availableMethods() {
	    return $this->shippingmethods;
	}
}

?>