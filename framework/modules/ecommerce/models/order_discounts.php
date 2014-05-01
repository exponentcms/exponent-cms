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
 * @subpackage Models
 * @package Core
 */
class order_discounts extends expRecord {
    public $table = 'order_discounts';
    public $has_one = array('discounts');    
    /*public $validates = array(
        'presence_of'=>array(
            'title'=>array('message'=>'Title is a required field.')
        ));*/
        
   
    function validate($redirectOnFailureTo = array('controller'=>'cart', 'action'=>'checkout'))
    {
        global $router;
        /*$discount = new discounts($this->discounts_id);
        $validateDiscountMessage = $discount->validateDiscount();*/        
        $validateDiscountMessage = $this->discounts->validateDiscount();
        
        if ($validateDiscountMessage == "")
        {
            return true;
        }
        else
        {
            //somthing is wrong so we need to remove the code, flash an erorr, and redirect to rebuild the cart
            $this->delete();
            flash('error', $validateDiscountMessage . gt("This discount code has been removed from your cart."));
            //redirect_to($redirectOnFailureTo);          
            redirect_to($router->current_url,true);
        }        
    }
        
    function caclulateDiscount()
    {
        global $order;
        $discount = new discounts($this->discounts_id);
        //check discount type and calculate accordingly
        //eDebug($this);   
        //eDebug($discount, true);
        if ($discount->action_type == 3)  //Pecentage off entire cart
        {
            //eDebug("Here1",true);
            return $order->totalBeforeDiscounts * ($discount->discount_percent/100);
        }
        elseif ($discount->action_type == 4)  //Fixed amount off entire cart
        {
            //eDebug("Here2",true);
            //eDebug ($discount->discount_amount, true);
            return $discount->discount_amount;
        }
    }
      
    public function isCartDiscount()
    {
        if ($this->discounts->action_type < 5) return true;
        else return false;
    }
    
    public function isShippingDiscount()
    {
        if ($this->discounts->action_type >= 5) return true;
        else return false;
    }
    
    public function requiresForcedShipping()
    {
        if(empty($this->discounts->required_shipping_calculator_id))return false;
        else return true;
    }  
    
    public function getRequiredShippingCalculatorId()
    {           
        return $this->discounts->required_shipping_calculator_id;
    }
    
    public function getRequiredShippingMethod()
    {
        return $this->discounts->required_shipping_method;
    }
}

?>