<?php

/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 */
 
class order_discounts extends expRecord {
    public $table = 'order_discounts';
    /*public $validates = array(
        'presence_of'=>array(
            'title'=>array('message'=>'Title is a required field.')
        ));*/
        
   
    function validate()    
    {
        $discount = new discounts($this->discount_id);
        $validateDiscountMessage = $discount->validateDiscount();
        if ($validateDiscountMessage == "")
        {
            return true;
        }
        else
        {
            //somthing is wrong so we need to remove the code, flash an erorr, and redirect to rebuild the cart
            $this->delete();
            flash('error', $validateDiscountMessage . "This discount code has been removed from your cart.");
            redirect_to(array('controller'=>'cart', 'action'=>'checkout'));          
        }        
    }
        
    function caclulateDiscount()
    {
        global $order;
        $discount = new discounts($this->discount_id);
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
        
}

?>
