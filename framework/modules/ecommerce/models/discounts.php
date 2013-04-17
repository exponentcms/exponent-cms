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
 * @package    Core
 */
class discounts extends expRecord {
    public $table = 'discounts';
    public $validates = array(
        'presence_of'   => array(
            'title'       => array('message' => 'Name is a required field.'),
            'coupon_code' => array('message' => 'Coupon Code is a required field.'),
        ),
        'uniqueness_of' => array(
            'coupon_code' => array('message' => 'That Coupon Code is already in use.'),
        )/*,
        'numericality_of'=>array(           
           'discount_amount'=>array('message'=>'Please enter a proper value for Discount Amount.'),
           'discount_percent'=>array('message'=>'Please enter a proper value for Discount Percent.'),
           'minimum_order_amount'=>array('message'=>'Please enter a proper value for Minimum Order Amount.') )      */
    );

    /*
    -- % off product price (not implemented yet)
    -- Fixed amount of product price (not implemented yet)
    -- % discount for whole cart
    -- Fixed discount for whole cart
    -- Free Shipping
    -- Buy X get Y free (must have items in cart, per conditions)  (not implemented yet)
    
    */
    public $actions = array(
        3 => 'Pecentage off entire cart',
        4 => 'Fixed amount off entire cart',
        5 => 'Free shipping',
        6 => 'Fixed amount off shipping'
    );

    //public $discount_types = array(1=>'%', 2=>'$');

    public function getCouponByName($code) {
        //if valid, return objec, else return null       
        return $this->find('first', 'coupon_code="' . trim($code) . '"');
    }

    public function isAvailable() {
        //FJD TODO: actually calculate this
        //if never_expired return false
        //if current datetime is within being and end valid dates, return false
        //else return true        
        if (!$this->enabled) return false;
        if ($this->never_expires == true) return true;

        /* eDebug(time());
         eDebug($this->startdate_time);
         eDebug($this->enddate_time, true);    */
        if (time() >= $this->startdate_time && time() <= $this->enddate_time) return true;
        else return false;
    }

    /*public static function getUsersDiscounts(&$order) {
        $no_more_discounts = false;
        
        $groupdiscounts = groupdiscounts::getGroupDiscountsForUser();
        foreach($groupdiscounts as $discount) {
            if ($discount->discount_type == 2 && $no_more_discounts == false) {
                $order->cart_discounts += $discount->discount_amount;
                $no_more_discounts = $discount->dont_allow_other_discounts;
            } elseif ($discount->discount_type == 1 && $no_more_discounts == false) {
                $order->product_discounts += $discount->discount_amount;
                $no_more_discounts = $discount->dont_allow_other_discounts;
            }
        }
    }*/

    public function update($params = array()) {

        //FJD: FIXME: this is here because expRecord throughs an error in the build function when trying to run
        //strip slashes on an array.  Should probably figure out a way to tell Exp if a field is supposed to be
        //serialized, and then we can handle it all in expRecord.
        $params['group_ids'] = serialize($params['group_ids']);

        $this->startdate = datetimecontrol::parseData('startdate', $params);
        $this->startdate_time = datetimecontrol::parseData('startdate_time', $params) + $this->startdate;
        $this->enddate = datetimecontrol::parseData('enddate', $params);
        $this->enddate_time = datetimecontrol::parseData('enddate_time', $params) + $this->enddate;
        //                   eDebug($_POST);   
        //eDebug($params);
        //eDebug($this, true);
        parent::update($params);
    }

    function validateDiscount() {
        global $order, $user;

        $retMessage = "";
        if (!$this->isAvailable()) {
            return gt("This discount code you entered is currently unavailable.");
        }

        //check discounts rules
        //.5 isExpired
        //1. uses per coupon
        //2. uses per customer

        //4. check group requirements                   
        //-1 = 'ALL LOGGED IN USERS'
        //-2 => 'ALL NON-LOGGED IN USERS'
        $required_groups = expUnserialize($this->group_ids);

        if (count($required_groups)) {
            $users_groups = $user->getGroupMemberships();

            if ($user->isLoggedIn()) {
                $loggedInGroup = new stdClass();
                $loggedInGroup->id = "-2";
                $users_groups[] = $loggedInGroup;
            }
            $inARequiredGroup = false;
            foreach ($users_groups as $ug) {
                if (in_array($ug->id, $required_groups)) $inARequiredGroup = true;
            }
            //eDebug($required_groups);
            //eDebug($users_groups);
            if (!$inARequiredGroup) return gt("This discount is not available to your user group.");
        }

        //5. check minimum order amount
        $order->calculateGrandTotal();
        if ($order->subtotal < $this->minimum_order_amount) {
            $retMessage = gt("You must purchase a minimum of") . " " . expCore::getCurrencySymbol() . /* number_format() */
                $this->minimum_order_amount . " " . gt("to use this coupon code.");
        }

        //check rules of products in cart
        //FJD TODO: not yet implemeneted 

        return $retMessage;
    }

    public function calculateShippingTotal($shippingTotal) {
        if ($this->action_type == 5) {
            return 0;
        } else if ($this->action_type == 6) {
            $ret = $shippingTotal - $this->shipping_discount_amount;
            if ($ret < 0) return 0;
            else return $ret;
        } else {
            return $shippingTotal;
        }
    }

    /* public static function getOrderDiscounts(&$order) {
         $groupdiscounts = groupdiscounts::getGroupDiscountsForUser();
         foreach($groupdiscounts as $discount) {
             if ($discount->discount_type == 2) {
                 $order->cart_discounts[] = $discount;
             } elseif ($discount->discount_type == 1) {
                 $order->product_discounts[] = $discount;
             }
         }
     } */

#    public static function calculate ($discount, $order) {
#        // just subtract a dollar amount...we don't need to worry about the apply_when parameter in this case
#        if ($discount->discount_type == 2) {
#            $amount_off = $discount->discount_amount;
#        } elseif ($discount->discount_type == 1) {
#            // if the discount type is a percentage then we need to find out when to apply it 
#            // to the order
#            if ($discount->apply_when == 1) {
#                $amount_off = $order->total * ($discount->discount_amount * .01);
#            } elseif ($discount->apply_when == 1) {
#                $total_shipping = $order->total + $order->shipping_total;
#                $amount_off = $total_shipping * ($discount->discount_amount * .01);
#            }
#        }
#        
#        return $amount_off;
#    }
}

?>