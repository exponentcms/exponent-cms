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

class tablebasedcalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return exponent_lang_getText('Table Based Shipping'); }
	public function description() { return exponent_lang_getText('Table Based Shipping calculator'); }
	public function hasUserForm() { return true; }
	public function hasConfig() { return true; }
	public function addressRequired() { return true; }
	public function isSelectable() { return true; }

    public $shippingmethods = array("01"=>"8-10 Day", "02"=>"6-7 Day");

    public function getRates($order) {   
        
        $a = $order->total;
        if ($a < 20.00) { $c1=5.95; $c2=6.95;}
        elseif ($a >=20.00 && $a < 25.00) { $c1=6.95; $c2=7.95;}
        elseif ($a >=25.00 && $a < 30.00) { $c1=7.95; $c2=9.65;}
        elseif ($a >=30.00 && $a < 35.00) { $c1=8.95; $c2=10.85;}                     
        elseif ($a >=35.00 && $a < 40.00) { $c1=9.25; $c2=11.85;}    
        elseif ($a >=40.00 && $a < 45.00) { $c1=9.65; $c2=12.85;}
        elseif ($a >=45.00 && $a < 50.00) { $c1=9.85; $c2=13.45;}
        elseif ($a >=50.00 && $a < 55.00) { $c1=9.85; $c2=13.85;}
        elseif ($a >=55.00 && $a < 60.00) { $c1=9.85; $c2=14.95;} 
        elseif ($a >=60.00 && $a < 70.00) { $c1=10.25; $c2=15.85;}
        elseif ($a >=70.00 && $a < 80.00) { $c1=10.85; $c2=17.65;}
        elseif ($a >=80.00 && $a < 90.00) { $c1=11.25; $c2=17.95;}
        elseif ($a >=90.00 && $a < 100.00) { $c1=11.65; $c2=18.95;}
        elseif ($a >=100.00 && $a < 130.00) { $c1=12.00; $c2=20.85;}
        elseif ($a >=130.00 && $a < 140.00) { $c1=12.45; $c2=22.50;}
        elseif ($a >=140.00 && $a < 150.00) { $c1=13.45; $c2=23.85;}
        elseif ($a >=150.00 && $a < 180.00) { $c1=13.85; $c2=26.85;}
        elseif ($a >=180.00 && $a < 200.00) { $c1=14.50; $c2=27.95;}        
        elseif ($a >=200.00 && $a < 225.00) { $c1=15.00; $c2=30.85;}
        elseif ($a >=225.00 && $a < 250.00) { $c1=16.00; $c2=32.85;}
        elseif ($a >=250.00 && $a < 275.00) { $c1=17.45; $c2=36.85;}
        elseif ($a >=275.00 && $a < 300.00) { $c1=17.85; $c2=38.85;}
        elseif ($a >=300.00 && $a < 350.00) { $c1=18.00; $c2=39.85;}
        elseif ($a >=350.00 && $a < 400.00) { $c1=18.45; $c2=42.85;}
        elseif ($a >=400.00 && $a < 450.00) { $c1=19.45; $c2=44.85;}
        elseif ($a >=450.00 && $a < 500.00) { $c1=19.85; $c2=55.85;}
        elseif ($a >=500.00 && $a < 750.00) { $c1=21.50; $c2=65.85;}
        elseif ($a >=750.00 && $a < 1000.00) { $c1=23.85; $c2=75.85;}
        elseif ($a >=1000.00 && $a < 1250.00) { $c1=25.00; $c2=85.00;}
        elseif ($a >=1250.00 && $a < 1500.00) { $c1=27.50; $c2=86.00;}
        elseif ($a >=1500.00 && $a < 1750.00) { $c1=32.50; $c2=90.00;}
        elseif ($a >=1750.00 && $a < 2000.00) { $c1=45.00; $c2=97.50;}
        elseif ($a >=2000.00 && $a < 2250.00) { $c1=55.00; $c2=108.75;}
        elseif ($a >=2250.00 && $a < 2500.00) { $c1=65.00; $c2=125.00;}
        elseif ($a >=2500.00) { $c1=75.00; $c2=150.00;}

        //if certain states, add $$ from config
        //eDebug($order);
        $currentMethod = $order->getCurrentShippingMethod(); 
        //eDebug($currentmethod);
        //2 - alaska
        //21 - hawaii
        //52 - PuertoRico
        $stateUpcharge = array('2','21','52');
        if (in_array($currentMethod->state, $stateUpcharge)) { $c1 += 1.50; $c2 += 1.50; }
	    $rates = array('01'=>array('id'=>'01','title'=>$this->shippingmethods['01'],'cost'=>$c1),'02'=>array('id'=>'02','title'=>$this->shippingmethods['02'],'cost'=>$c2) );
	    return $rates;
    }	
    
   	public function configForm() { 
   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/tablebasedcalculator/configure.tpl';
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
    
    public function getHandling() {
        return isset($this->configdata['handling']) ? $this->configdata['handling'] : 0;
    }
    
    public function getMessage() {
        return $this->configdata['message'];
    }
}

?>
