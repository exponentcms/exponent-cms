<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
	public function name() { return gt('Free'); }
	public function description() { return gt('Offers free shipping on all orders'); }
    public function addressRequired() { return false; }

    public $shippingmethods = array("01"=>"Free");

    public function __construct($params = null) {
        parent::__construct($params);
        if(isset($this->configdata['free_shipping_method_default_name']))
        {
            $this->shippingmethods["01"] = $this->configdata['free_shipping_method_default_name'];
        }
        if(isset($this->configdata['shipping_service_name']))
        {
            $this->title = $this->configdata['shipping_service_name'];
        }
    }

    public function meetsCriteria($shippingmethod) {
        return true;
    }

    public function getRates($order) {                        
	    $rates = array(
            '01'=>array(
                'id'=>'01',
                'title'=>$this->shippingmethods["01"],
                'cost'=>0
            )
        );
	    return $rates;
    }	
    
//   	public function configForm() {
//   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/freeshippingcalculator/configure.tpl';
//   	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array(
            'shipping_service_name',
            'free_shipping_method_default_name'
        );
        $config = array();
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