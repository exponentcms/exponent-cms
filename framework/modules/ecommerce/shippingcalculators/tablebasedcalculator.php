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

class tablebasedcalculator extends shippingcalculator {
    /*
     * Returns the name of the shipping calculator, for use in the Shipping Administration Module
     */
    //overridden methods:
	// public $table = 'table_based_shipping_charges';
	public $has_many = array('shippingspeeds');
	
    public function name() { return gt('Simple'); }
    public function description() { return gt('Order Total Cost based shipping calculator'); }
    public function addressRequired() { return false; }

    public function __construct($params = null) {
        parent::__construct($params);
        if(isset($this->configdata['shipping_service_name']))
        {
            $this->title = $this->configdata['shipping_service_name'];
        }
    }

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
        $currentMethod = $order->getCurrentShippingMethod();
		
		//Get the config and parse to get the states/regions only
		$upcharge = ecomconfig::getConfig('upcharge');
		$stateUpcharge = ecomconfig::splitConfigUpCharge($upcharge, 'region');
		
        //2 - alaska
        //21 - hawaii
        //52 - PuertoRico
        // $stateUpcharge = array('2','21','52');
        $rates = array();
	    if(!empty($c)) {
            for ($i = 0, $iMax = count($c); $i < $iMax; $i++) {
				if (array_key_exists($currentMethod->state, $stateUpcharge)) {
					$c[$i] += $stateUpcharge[$currentMethod->state]; // $c[$i] += $stateUpcharge[$currentMethod->state]; Commented this though i'm not sure if this is done intentionally 
				}
                if($i > 9) $rates[($i+1)] = array(
                    'id' => 0 . ($i+1),
                    'title' => @$this->shippingspeeds[$i]->speed,
                    'cost' => $c[$i]
                );
                else $rates[0 . ($i+1)] = array(
                    'id' => 0 . ($i+1),
                    'title' => @$this->shippingspeeds[$i]->speed,
                    'cost' => $c[$i]
                );
			}
		}
	     
        if(!count($rates)) $rates['01'] = array(
            'id' => '01',
            'title' => gt("Table Based Shipping is Currently NOT Configured"),
            'cost' => 0
        );
		return $rates;
    }    
    
//    public function configForm() {
//       return BASE.'framework/modules/ecommerce/shippingcalculators/views/tablebasedcalculator/configure.tpl';
//    }
    
    //process config form
    function parseConfig($values) {
		global $db;

		$where = " shippingcalculator_id = {$values['id']}";
		$speeds = $db->selectObjects("shippingspeeds", $where);
        $config_vars = array(
            'handling',
            'to',
            'from'
        );
        $config = array();
		foreach($speeds as $item) {
			$config_vars[] = str_replace(' ', '_', $item->speed);
		}
		// eDebug($config_vars, true);
        $sorted_config = array();
        foreach ($config_vars as $varname) {
            if ($varname == 'rate' || $varname == 'handling') {
                $config[$varname] = isset($values[$varname]) ? expUtil::currency_to_float($values[$varname]) : null;
            } else {
                $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
            }
            $sorted_config[$varname] = array();
        }
        // sort by lower end of range values
        $from = $config['from'];
        asort($from);
        $i = 0;
        foreach ($from as $key=>$val) {
            foreach ($config_vars as $varname) {
                $sorted_config[$varname][$i] = $config[$varname][$key];
            }
            $i++;
        }

        return $sorted_config;
    }
    
    function availableMethods() {
        $shippingmethods = array();
        for ($i = 0, $iMax = count($this->shippingspeeds); $i < $iMax; $i++) {
            if($i > 9 ) $shippingmethods[($i+1)] = $this->shippingspeeds[$i]->speed;
			else $shippingmethods[0 . ($i+1)] = $this->shippingspeeds[$i]->speed;
		}
		
        return $shippingmethods;
    }

    public function editspeed() {
        return BASE.'framework/modules/ecommerce/shippingcalculators/views/tablebasedcalculator/editspeed.tpl';
    }

    /**
     * Unused at this time
     *
     * @return int
     */
    public function getHandling() {
        return isset($this->configdata['handling']) ? $this->configdata['handling'] : 0;
    }

    /**
     * Unused at this file
     *
     * @return mixed
     */
    public function getMessage() {
        return $this->configdata['message'];
    }
	
}

?>