<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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

class perweightcalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return gt('Per Weight'); }
	public function description() { return gt('Per weight based shipping calculator'); }
    public function addressRequired() { return false; }

    public $shippingmethods = array("01"=>"Per Weight");

    public function __construct($params = null) {
        parent::__construct($params);
        if(isset($this->configdata['shipping_method_name']))
        {
            $this->shippingmethods["01"] = $this->configdata['shipping_method_name'];
        }
        if(isset($this->configdata['shipping_service_name']))
        {
            $this->title = $this->configdata['shipping_service_name'];
        }
    }

    public function getRates($order) {
        $rate = !empty($this->configdata['rate']) ? $this->configdata['rate'] : 0;
        $handling = !empty($this->configdata['handling']) ? $this->configdata['handling'] : 0;
        $weight = 0;
        foreach ($order->orderitem as $item) {
            if (!$item->product->no_shipping) {
                // calculate option weight
//                $weight += $item->product->weight * $item->quantity;
                $weight += $item->getWeight() * $item->quantity;
            }
        }
        $total = $weight * $rate + $handling;
	    $rates = array(
            '01'=>array(
                'id'=>'01',
                'title'=>$this->shippingmethods['01'],
                'cost'=>$total
            )
        );
	    return $rates;
    }

//   	public function configForm() {
//   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/perweightcalculator/configure.tpl';
//   	}

	//process config form
	function parseConfig($values) {
	    $config_vars = array(
            'rate',
            'handling',
            'shipping_service_name',
            'shipping_method_name'
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