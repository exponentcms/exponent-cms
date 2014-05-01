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

class tablebasedcalculator extends shippingcalculator {
    /*
     * Returns the name of the shipping calculator, for use in the Shipping Administration Module
     */
    //overridden methods:
	// public $table = 'table_based_shipping_charges';
	public $has_many = array('shippingspeeds');
	
    public function name() { return gt('Table Based Shipping'); }
    public function description() { return gt('Table Based Shipping calculator'); }
    public function hasUserForm() { return true; }
    public function hasConfig() { return true; }
    public function addressRequired() { return false; }
    public function isSelectable() { return true; }    

    public function getRates($order) {   
        $a = $order->total;        
		
		//get the rates
		for($i = 0; $i < @count($this->configdata['from']); $i++) {
			// We need to check if it is not the last in the array since we don't have a 'to' value in the last element            
            if(count($this->configdata['from']) != ($i + 1)) {                
				if( expUtil::isNumberGreaterThanOrEqualTo($a,$this->configdata['from'][$i]) && expUtil::isNumberLessThanOrEqualTo($a,$this->configdata['to'][$i])) {  
					foreach($this->shippingspeeds as $item) {
						$c[] = @$this->configdata[str_replace(' ', '_', $item->speed)][$i];
					}
					break;
				}
			} else {
                if( $a >= floatval($this->configdata['from'][$i]) ) {
					foreach($this->shippingspeeds as $item) {
						$c[] = @$this->configdata[str_replace(' ', '_', $item->speed)][$i];
					}
					break;
				}
			}            
		}
		 //if certain states, add $$ from config
        $currentMethod = $order->getCurrentShippingMethod(); //third created shipping method
		
		//Get the config and parse to get the states/regions only
		$upcharge = ecomconfig::getConfig('upcharge');
		$stateUpcharge = ecomconfig::splitConfigUpCharge($upcharge, 'region');
		
        //2 - alaska
        //21 - hawaii
        //52 - PuertoRico
        // $stateUpcharge = array('2','21','52');
        $rates = array();
	    if(!empty($c)) {
			for($i = 0; $i < count($c); $i++) {
			
				if (array_key_exists($currentMethod->state, $stateUpcharge)) { 
					$c[$i] += $stateUpcharge[$currentMethod->state]; // $c[$i] += $stateUpcharge[$currentMethod->state]; Commented this though i'm not sure if this is done intentionally 
				}
                if($i > 9) $rates[($i+1)] = array('id' => 0 . ($i+1), 'title' => @$this->shippingspeeds[$i]->speed, 'cost' => $c[$i]);
                else $rates[0 . ($i+1)] = array('id' => 0 . ($i+1), 'title' => @$this->shippingspeeds[$i]->speed, 'cost' => $c[$i]);
				
			}            
		}
	     
        if(!count($rates)) $rates['01'] = array('id' => '01', 'title' => "Table Based Shipping is Currently NOT Configured", 'cost' => 0);
		return $rates;
    }    
    
    public function configForm() { 
       return BASE.'framework/modules/ecommerce/shippingcalculators/views/tablebasedcalculator/configure.tpl';
    }
    
    //process config form
    function parseConfig($values) {
		global $db;
		$where = " shippingcalculator_id = {$values['id']}";
		$speeds = $db->selectObjects("shippingspeeds", $where);
        $config_vars = array('to', 'from');
		foreach($speeds as $item) {
			$config_vars[] = str_replace(' ', '_', $item->speed);
		}
		// eDebug($config_vars, true);
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

		for($i = 0; $i < count($this->shippingspeeds); $i++) {
            if($i > 9 ) $shippingmethods[($i+1)] = $this->shippingspeeds[$i]->speed;
			else $shippingmethods[0 . ($i+1)] = $this->shippingspeeds[$i]->speed;
		}
		
        return $shippingmethods;
    }
    
    public function getHandling() {
        return isset($this->configdata['handling']) ? $this->configdata['handling'] : 0;
    }
    
    public function getMessage() {
        return $this->configdata['message'];
    }
	
	public function editspeed() {
        return BASE.'framework/modules/ecommerce/shippingcalculators/views/tablebasedcalculator/editspeed.tpl';
    }
	
}

?>