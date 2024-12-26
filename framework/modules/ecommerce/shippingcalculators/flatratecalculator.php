<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
	public function name() { return gt('Flat Rate'); }
	public function description() { return gt('Flat Rate shipping calculator - single rate for entire order'); }
    public function addressRequired() { return false; }

    public $shippingmethods = array("01"=>"Flat Rate");

    public function getRates($order) {
        $location_upcharge = 0;
        //if certain states, add $$ from config
        $currentMethod = $order->getCurrentShippingMethod();
        //Get the config and parse to get the states/regions only
        $upcharges = ecomconfig::getConfig('upcharge');
        $countryUpcharge = ecomconfig::splitConfigUpCharge($upcharges, 'country');
        if (array_key_exists($currentMethod->country, $countryUpcharge)) {
            $location_upcharge += $countryUpcharge[$currentMethod->country]; // $c[$i] += $stateUpcharge[$currentMethod->state]; Commented this though i'm not sure if this is done intentionally
        }
        $stateUpcharge = ecomconfig::splitConfigUpCharge($upcharges, 'region');
        if (array_key_exists($currentMethod->state, $stateUpcharge)) {
            $location_upcharge += $stateUpcharge[$currentMethod->state]; // $c[$i] += $stateUpcharge[$currentMethod->state]; Commented this though i'm not sure if this is done intentionally
        }

	    $rates = array(
            '01'=>array(
                'id'=>'01',
                'title'=>$this->shippingmethods['01'],
                'cost'=>$this->configdata['rate'] + $location_upcharge
            )
        );
	    return $rates;
    }

//   	public function configForm() {
//   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/flatratecalculator/configure.tpl';
//   	}

	//process config form
	function parseConfig($values) {
	    $config_vars = array(
            'rate'
        );
        $config = array();
	    foreach ($config_vars as $varname) {
	        if ($varname == 'rate') {
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