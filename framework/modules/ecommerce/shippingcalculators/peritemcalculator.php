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
 * @subpackage Calculators
 * @package Modules
 */

class peritemcalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return gt('Per Item'); }
	public function description() { return gt('Per item based shipping calculator'); }
	public function hasUserForm() { return false; }
	public function hasConfig() { return true; }
	public function addressRequired() { return false; }
	public function isSelectable() { return true; }

    public $shippingmethods = array("01"=>"Per Item");

    public function getRates($order) {
        $rate = !empty($this->configdata['rate']) ? $this->configdata['rate'] : '';
        $handling = !empty($this->configdata['handling']) ? $this->configdata['handling'] : '';
        $count = 0;
        foreach ($order->orderitem as $item) {
            if (!$item->product->no_shipping) $count += $item->quantity;
        }
        $total = $count * $rate + $handling;
	    $rates = array(
            '01'=>array(
                'id'=>'01',
                'title'=>$this->shippingmethods['01'],
                'cost'=>$total
            )
        );
	    return $rates;
    }	
    
   	public function configForm() { 
   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/peritemcalculator/configure.tpl';
   	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array(
            'rate',
            'handling'
        );
        $config = array();
	    foreach ($config_vars as $varname) {
	        if ($varname == 'rate' || $varname == 'handling') {
	            $config[$varname] = isset($values[$varname]) ? expUtil::currency_to_float($values[$varname]) : null;
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