<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * @subpackage Models
 * @package Core
 */
class billing extends expRecord {
    //public $table = 'billing';

    public $address = null;
    public $calculator = null;
    public $available_calculators = array();
    public $selectable_calculators = array();
    public $form = '';
    public $billingmethod = null;

    public function __construct($id=null) {
        global $user;

        // if an ID is passed in we'll look up the cart from the database...it means this is
        // probably a completed order an admin is looking at it via the order admin interface.
        //if (empty($id)) {
        //    global $order;            
        //} else {
        //$order = new order($id);
        //}
        
        /*
        // check for this users billing method object.  If it hasn't been created yet then lets do it now.
        if (empty($order->billingmethod)) {
            $order->billingmethod[0] = new billingmethod();
            $order->billingmethod[0]->update(array('orders_id'=>$order->id)); 
        } */
    
        //if (empty($order->billingmethod[0]->addresses_id) && $user->isLoggedIn()) {    
        //if ($user->isLoggedIn()) { 
        if ($id==null)
        {   
            // since this is a new billingmethod object, lets initialize it with the users billing address.
            global $order;                              
            $address = new address();
            //FJD $defaultaddy = $address->find('first', 'user_id='.$user->id.' AND is_default=1');
            if (empty($order->billingmethod)) {
                $order->billingmethod[0] = new billingmethod();
                $order->billingmethod[0]->update(array('orders_id'=>@$order->id)); // @ added to ditch notice when ecom is off
            }
            $billingAddy = $address->find('first', 'user_id='.$user->id.' AND is_billing=1');
            $order->billingmethod[0]->setAddress($billingAddy);
        }else{
            $order = new order($id);        
            if (empty($order->id)) return false;                    
        }
        //}

        $this->address = new address($order->billingmethod[0]->addresses_id);
        //$this->address = new address($order->billingmethod[0]->id);
        $this->available_calculators = billing::listAvailableCalculators();
        $this->selectable_calculators = $this->selectableCalculators();
        $this->calculator_views = $this->getCalcViews();
        
        // if there is only one available calculator we'll force it on the user
        // also if the user hasn't selected a calculator yet well set it to a default.
        $number_of_calculators = count($this->available_calculators);
        
        if ($number_of_calculators == 1 || empty($order->billingmethod[0]->billingcalculator_id)) {
            reset($this->available_calculators);
            $calcid = key($this->available_calculators);
            $order->billingmethod[0]->update(array('billingcalculator_id'=>$calcid));
        }           

	    if ($number_of_calculators > 0) {
            $calcname = $this->available_calculators[$order->billingmethod[0]->billingcalculator_id];  
		
            if (!empty($calcname)) {
                $this->calculator = new $calcname($order->billingmethod[0]->billingcalculator_id);
            } else {
                $this->calculator = null;
            }
        } else {
            $this->calculator = null;
        }
        
        $this->billingmethod = $order->billingmethod[0];
        
        $options = unserialize($this->billingmethod->billing_options);
        $this->info = empty($this->calculator->id) ? '' : $this->calculator->userView($options);
		
		foreach($this->available_calculators as $key => $item) {
			$calc  = new $item($key);
			$this->form[$key] = $calc->userForm();
		}
        
		// eDebug($this->form, true);	
    }

    public static function listAvailableCalculators() {
        global $db,$user;
        
        $calcs = array();
        foreach ($db->selectObjects('billingcalculator', 'enabled=1') as $calcObj) {
            $calcNameReal = $calcObj->calculator_name;
            $calc = new $calcNameReal($calcObj->id);
            if($user->isAdmin() || $calc->isRestricted() == false)
            {
                $calcs[$calc->id] = $calc->calculator_name;
            }
        }
        
        return $calcs;
    }

    public function selectableCalculators() {
        global $db,$user;
        $calcs = array();
        foreach ($db->selectObjects('billingcalculator', 'enabled=1') as $calcObj) {
            $calcNameReal = $calcObj->calculator_name;
            $calc = new $calcNameReal($calcObj->id);
            if($user->isAdmin() || $calc->isRestricted() == false)
            {
                $calcs[$calc->id] = $calc->user_title;
            }
        }
        
        return $calcs;
    }
    
    public function getCalcViews() {        
        $dirs = array(
            BASE.'themes/'.DISPLAY_THEME.'/modules/ecommerce/views/billing/',
            BASE.'framework/modules/ecommerce/views/billing/',
        );
        
        $views = array();
        foreach ($this->available_calculators as $key=>$calcname) {
            if (file_exists($dirs[0].$calcname.'.tpl')) {
                $views[$calcname]['view'] = $dirs[0].$calcname.'.tpl';    
            } else {
                $views[$calcname]['view'] = $dirs[1].$calcname.'.tpl';    
            }   
            $views[$calcname]['id'] = $key;          
        }
        
        return array_reverse($views);
    }
    
    public function getCalcForms() {
        //eDebug($this);
        foreach ($this->available_calculators as $calcid=>$calcname) {            
            $calc = new $calcname($calcid);
            $forms[$calcname] = $calc->userForm();
        }        
        return array_reverse($forms);
    }
    
   /* public function refresh()
    {
        if (empty($this->id)) return false;
         $number_of_calculators = count($this->available_calculators);
        
        if ($number_of_calculators == 1 || empty($order->billingmethod[0]->billingcalculator_id)) {
            reset($this->available_calculators);
            $calcid = key($this->available_calculators);
            $order->billingmethod[0]->update(array('billingcalculator_id'=>$calcid));
        }           
        
        if ($number_of_calculators > 0) {
            $calcname = $this->available_calculators[$order->billingmethod[0]->billingcalculator_id];            
            $this->calculator = new $calcname($order->billingmethod[0]->billingcalculator_id);
        } else {
            $this->calculator = null;
        }
        //parent::update()
                                    
    } */
}

?>