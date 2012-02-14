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

class flatratecalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return gt('Flat Rate Shipping'); }
	public function description() { return gt('Flat Rate Shipping calculator'); }
	public function hasUserForm() { return true; }
	public function hasConfig() { return true; }
	public function addressRequired() { return true; }
	public function isSelectable() { return true; }

    public $shippingmethods = array("01"=>"Flat Rate");

    public function getRates($order) {        
	    $rates = array('01'=>array('id'=>'01','title'=>$this->shippingmethods['01'],'cost'=>$this->configdata['rate']));
	    return $rates;
    }	
    
   	public function configForm() { 
   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/flatratecalculator/configure.tpl';
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
