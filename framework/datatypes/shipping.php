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

class shipping extends expRecord {
	public $table = 'shipping';
	public $available_calculators = array();
	public $calculator = null;
	public $shippingmethod = null;
	public $options = null;
	public $available_options = null;
	public $address = null;
	public $splitshipping = false;
	public $forced_shipping = false;
	
	public function __construct() {
        global $order, $user;

        if (empty($order->id)) return false;
        
        $existing_shippingmethods = $order->getShippingMethods();        
        $this->available_calculators = $this->listAvailableCalculators();
        $this->selectable_calculators = $this->selectableCalculators();
        
        if (count($existing_shippingmethods) == 1) {
            if ($order->forcedShipping()) {
                $this->shippingmethod = $order->getForcedShippingMethod();
                $this->forced_shipping = true;
            } else {
                $this->shippingmethod = $order->getCurrentShippingMethod();
            }        
            
            // if this shippingmethod doesn't have an address assigned to it, lets check and see if this
            // user has set one up yet and default to that if so
            //if (empty($this->shippingmethod->addresses_id) && $user->isLoggedIn()) {            
            if ($user->id !=0) {            
                $address = new address();
                $addy = $address->find('first', 'user_id='.$user->id.'  AND is_shipping=1');
                if (empty($addy->id)) $addy = $address->find('first', 'user_id='.$user->id);
                if (!empty($addy->id)) $this->shippingmethod->setAddress($addy);                
            }                                                                     
            $this->address = new address($this->shippingmethod->addresses_id);
            
            $number_of_calculators = count($this->available_calculators);
            if ($number_of_calculators == 1 || empty($this->shippingmethod->shippingcalculator_id)) {
                $calcid = key($this->available_calculators);
                if ($this->shippingmethod->shippingcalculator_id != $calcid) {
                    $this->shippingmethod->update(array('shippingcalculator_id'=>$calcid));
                }
            } 
            
            if (!empty($this->available_calculators) && !empty($this->shippingmethod->shippingcalculator_id)) {
                $calcname = $this->available_calculators[$this->shippingmethod->shippingcalculator_id];            
                $this->calculator = new $calcname($this->shippingmethod->shippingcalculator_id);
            } else {
                $this->calculator = null;                
            }            
            
            $this->getRates();
            
        } else {
            eDebug($this);
            eDebug($order);
            eDebug("Error in shipping constuctor.", true) ;
            //NO split shipping for now
            /*$this->splitshipping = true;
            $this->splitmethods = array();
            foreach ($existing_shippingmethods as $smid) {
                $method = new shippingmethod($smid);
                if ($method->requiresShipping()) {
                    $this->splitmethods[$method->id] = $method;
                    $this->splitmethods[$method->id]->orderitem = $order->getOrderitemsByShippingmethod($method->id);
                }
                
            } */
        }
    }
	
	public function getRates() {
	    global $order;
        
	    if (!empty($this->calculator->id) && (!empty($this->shippingmethod->addresses_id) || !$this->calculator->addressRequired())) {	
		    $this->pricelist = $this->calculator->getRates($order);
		} else {
		    $this->pricelist = array();
		}

		// if the user hasn't selected a shipping option yet we will default one for him now.
		if ((!empty($this->shippingmethod->id) && (is_array($this->pricelist) && (count($this->pricelist) > 0)))) { 
		    if(empty($this->shippingmethod->option)) {
		        $opt = current($this->pricelist);
		        $this->shippingmethod->update(array('option'=>$opt['id'],'option_title'=>$opt['title'],'shipping_cost'=>$opt['cost'])); //updates SECOND created shipping method w/ rates, as that was the one set to $this->shippingmethod
		    } else {
		        if ($this->shippingmethod->shipping_cost != $this->pricelist[$this->shippingmethod->option]['cost']) {
		            $opt = $this->pricelist[$this->shippingmethod->option];
		            $this->shippingmethod->update(array('option'=>$opt['id'],'option_title'=>$opt['title'],'shipping_cost'=>$opt['cost']));
		        }
		    }
		}		
		//return $pricelist;
	}
	
	public function listAvailableCalculators() {
	    global $db;
	    $calcs = array();
	    foreach ($db->selectObjects('shippingcalculator', 'enabled=1') as $calc) {
	        $calcs[$calc->id] = $calc->calculator_name;
	    }
	    
		return $calcs;
    }
    
    public static function listAllCalculators() {
	    $calcs = array();
	    foreach ($db->selectObjects('shippingcalculator') as $calc) {
	        $calcs[$calc->id] = $calc->calculator_name;
	    }
	    
		return $calcs;
    }
    
    public function selectableCalculators() {
	    global $db;
	    $calcs = array();
	    foreach ($db->selectObjects('shippingcalculator', 'enabled=1') as $calc) {
	        $calcs[$calc->id] = $calc->title;
	    }
	    
		return $calcs;
    }
    
    static function estimateShipping($order)
    {        
        $c = new shippingcalculator();
        $calc = $c->find('first',"enabled=1 AND is_default=1");
        $calcName = $calc->calculator_name;
        $calculator = new $calcName();
        if($calculator->addressRequired())
        {
            return 0;
        }
        else
        {
            $rates = $calculator->getRates($order);
            return $rates['01']['cost'];
        }
    }
}

?>
