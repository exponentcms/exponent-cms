<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Created by Adam Kessler @ 09/06/2007
#
# This file is part of Acorn Web API
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

class order extends expRecord {
	protected $table = 'orders';
	public $has_many = array('orderitem', 'billingmethod', 'order_status_changes');
	public $has_one = array('order_status','order_type');
    public $get_assoc_for = array('orderitem','billingmethod');
	public $total = 0;
	public $shippingmethods = array();
	public $orderitem = array();
	public $shipping_required = false;
	public $billing_required = false;
	public $product_discounts = 0;
	public $cart_discounts = 0;
	public $applied_discounts = 0;
	public $promos = array();
	public $taxzones = array();
	public $forced_shipping = false;
	public $product_forcing_shipping = '';
	
    protected $attachable_item_types = array(
        //'content_expFiles'=>'expFile', 
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );
    
	public $status_codes = array(0=>'New', 1=>'Opened', 2=>'Processing', 3=>'Shipped');
	
	function __construct($params=null, $get_assoc=true, $get_attached=true) {
	    parent::__construct($params, $get_assoc, $get_attached);

	    //check to see if this is a completed order and if so, add the items and shipping info    
        if (!empty($this->purchased)) {
	        // final the cart totals
	        //$this->calculateGrandTotal();	        
	        foreach ($this->orderitem as $items) {            
                if ($items->product->requiresShipping) $this->shipping_required = true;
                if ($items->product->requiresBilling) $this->billing_required = true;
	        }                              
	        //$this->shipping_total = 0;
            foreach($this->getShippingMethods() as $smid) {
                $this->shippingmethods[$smid] = new shippingmethod($smid) ;
                $this->shippingmethods[$smid]->orderitem = $this->getOrderitemsByShippingmethod($smid);
                
                $requiresShipping = false;
                foreach ($this->shippingmethods[$smid]->orderitem as $oi) {
                    if ($oi->product->requiresShipping) $requiresShipping = true;
                }
                /*if ($requiresShipping == true) {
    	            $this->shipping_total += $this->shippingmethods[$smid]->shipping_cost;
	            }  */
            }
            
            // grab our tax zones
            $this->taxzones = taxclass::getCartTaxZones($this);
        
            /*$this->total = $this->getCartTotal();
            $this->calculateTax();
            $this->grand_total = $this->total + $this->tax + $this->shipping_total;*/
            //$this->calculateGrandTotal();
	    }
	}
	
	static function getUserCart() {
		global $db,$user;
		// if ecomm is turned off, no cart.
		$active = $db->selectValue('modstate', 'active', 'module="storeController"');
        if (empty($active)) return null;
		$order = new order(); //initialize a new order object to use the find function from.
		$ticket = exponent_sessions_getTicketString();  //get this users session ticket. this is how we track anonymous users.
        // grab the origional referrer from the session table so that we can transfer it into the cart where it will be used for reporting purposes
        // sessions are temporary so we can't report on the referrer in the session table itsef because it may not be there
        // and we can't just get the referrer ar this point becaues the user likely navigated the site a bit and we want the origional referring site
        $orig_referrer = $db->selectValue('sessionticket', 'referrer', "`ticket`='".$ticket."'");
		$sessioncart = $order->find('first', "invoice_id='' AND sessionticket_ticket='".$ticket."'");
        if (!empty($user) && $user->isLoggedIn()) {
			$usercart = $order->find('first', "invoice_id='' AND user_id=".$user->id);
		}
		if (empty($sessioncart->id)) {	
			if (empty($usercart->id)) {
				// no cart was found...create one
				$cart = new order();
				$cart->update(array("sessionticket_ticket"=>$ticket, 'user_id'=>$user->id, 'orig_referrer'=>$orig_referrer));
			} else {
				$usercart->update(array('sessionticket_ticket'=>$ticket, 'orig_referrer'=>$orig_referrer));
				$cart = $usercart;
			}
		} elseif(!empty($sessioncart->id) && $user->id == 0) { 
			// the user isn't logged in yet...the session cart will do for now.
			$cart = $sessioncart;
		} elseif(!empty($sessioncart->id) && !empty($usercart->id)) {
			// if we hit here we've found a session cart and a usercart...that means we need to merge them
			// if it's not the same cart.
			if ($sessioncart->id == $usercart->id) {
				$cart = $sessioncart;
			} else {
			    // if the old user cart had gone through any of the checkout process before, than we
			    // will clean that data out now and start fresh.
			    $usercart->cleanOrderitems();
			    
				//merge the current session cart with previously saved user cart.
				foreach($sessioncart->orderitem as $orderitem) {
				    $orderitem->merge(array('orders_id'=>$usercart->id, 'user_id'=>$user->id));
				}
				$cart = new order($usercart->id);
				$sessioncart->delete();
			}
		} elseif (!empty($sessioncart->id) && (empty($usercart->id) && $user->isLoggedIn())) {
			// the user doesn't have a cart with his/her user id in it. this probably means they just logged in
			// so we need to update the cart with the new user id information.
			$sessioncart->update(array('user_id'=>$user->id, 'orig_referrer'=>$orig_referrer));
			$cart = $sessioncart;
		}
	
        $cart->item_count = 0;     
	    foreach ($cart->orderitem as $items) {            
            if ($items->product->requiresShipping && !$items->product->no_shipping) $cart->shipping_required = true;
            if ($items->product->requiresBilling) $cart->billing_required = true;
            $cart->item_count += $items->quantity;  
	    } 
        
        		$cart->lastcat = exponent_sessions_get('last_ecomm_category');	
		$cart->total = $cart->getCartTotal();	
		return $cart;
	}

    public function cleanOrderitems() {    
        $deleted_items = array();
        foreach($this->orderitem as $orderitem) {
            if (!in_array($orderitem->id, $deleted_items)) {
                $similar_items = $orderitem->find('all', "orders_id=".$this->id." AND product_id=".$orderitem->product_id." AND product_type='".$orderitem->product_type."' AND options='".$orderitem->options."' AND id !=".$orderitem->id);
                foreach ($similar_items as $similar_item) {
                    $orderitem->quantity = $orderitem->quantity + $similar_item->quantity;
                    $deleted_items[] = $similar_item->id;
                    $similar_item->delete();
                }
                
                $shippingmethod = new shippingmethod($orderitem->shippingmethods_id);
                $shippingmethod->delete();
                $orderitem->shippingmethods_id = 0;
                $orderiten->products_tax = 0;
                $orderitem->save();
            }
        }
    }
    
    public function getCurrentShippingMethod() {
        $sm_ids = $this->getShippingMethods();
        $sm = new shippingmethod(current($sm_ids));
        
        return $sm;
    }
    
    public function getShippingMethods() {
        global $db;        
        $ids = $db->selectColumn('orderitems', 'shippingmethods_id', 'shippingmethods_id!=0 AND orders_id='.$this->id, null, true);
        if (empty($ids)) {
            $sm = new shippingmethod();
            $sm->save();
            $ids = array($sm->id);
        }
        return $ids;
    }
    
    public function forcedShipping() {
        foreach ($this->orderitem as $item) {
            if (!empty($item->product->required_shipping_method)) {
                $this->forced_shipping = true;
                $this->product_forcing_shipping = $item->product;
                return true;
            }
        }
        
        return false;
    }
    
    public function getForcedShippingMethod() {
        global $db, $user;
        
        $forced_calc = '';
        $forced_method = '';
        foreach ($this->orderitem as $item) {
            if (!empty($item->product->required_shipping_method)) {
                $method = new shippingmethod($item->shippingmethods_id);
                $forced_calc = $item->product->required_shipping_calculator_id;
                $forced_method = $item->product->required_shipping_method;
                $this->forced_shipping = true;
                $this->product_forcing_shipping = $item->product;
                break;
            }
        }

        // if this shippingmethod doesn't have an address assigned to it, lets check and see if this
        // user has set one up yet and default to that if so
        if (empty($method->addresses_id) && $user->isLoggedIn()) {
            $address = new address();
            $addy = $address->find('first', 'user_id='.$user->id.'  AND is_default=1');
            if (!empty($addy->id)) $method->setAddress($addy);
        }
        
        $calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id='.$forced_calc);
        $calculator = new $calcname($forced_calc);
        $rates = $calculator->getRates($this);
		$rate = $rates[$forced_method];
		$method->update(array('option'=>$forced_method,'option_title'=>$rate['title'],'shipping_cost'=>$rate['cost'], 'shippingcalculator_id'=>$forced_calc));
		return $method;
    }
    
    public function getCurrentBillingMethod() {
        $bm_ids = $this->getBillingMethods();
        $bm = new billingmethod(current($bm_ids));
        
        return $bm;
    }
    
    public function getBillingMethods() {
        global $db;        
        return $db->selectColumn('billingmethods', 'id', 'orders_id='.$this->id, null, true);
    }
    
    public function getOrderitemsByShippingmethod($shippingmethod_id) {
        $orderitem = new orderitem(null,false,false);
        return $orderitem->find('all', 'orders_id='.$this->id." AND shippingmethods_id=".$shippingmethod_id);
    }
    
    public function countOrderitemsByShippingmethod($shippingmethod_id) {
        $orderitem = new orderitem(null,false,false);
        return $orderitem->find('count', 'orders_id='.$this->id." AND shippingmethods_id=".$shippingmethod_id);
    }
    
	public function getCartTotal() {
		$total = 0;
        foreach($this->orderitem as $item) {
            //$total += $item->products_price * $item->quantity;
            $total += $item->getTotal();
        }
		return $total;
	}
	
	/*public function calculateTax() {
	    global $user;
	    
	    $this->tax = 0;
	    foreach($this->orderitem as $item) {
            $taxclass = new taxclass($item->product->tax_class_id);
            $item->products_tax = $taxclass->getProductTax($item);
            $this->tax += $item->products_tax;
        }
        
        return $this->tax;
	}   */
	
    //this is taking into account only one discount allowed for the time being
    //and does not include the tax calculations - this is a simple cart estimate discount
    function updateOrderDiscounts()
    {      
        $this->totalBeforeDiscounts = $this->total; // reference to the origional total
        $this->cart_discounts = 0;
        $this->total_applied_discounts = 0;
        foreach ($this->getOrderDiscounts() as $od)
        {               
            $od->validate();
            $this->cart_discounts += $od->caclulateDiscount();
        }                                                                    
        $this->total_applied_discounts = $this->cart_discounts;
        $this->total = $this->totalBeforeDiscounts - $this->total_applied_discounts;
    }
    
    //this is taking into account only one discount allowed for the time being
   /* function updateTaxDiscounts()
    {      
        foreach ($this->getOrderDiscounts() as $od)
        {   
            
            $this->cart_discounts += $od->caclulateDiscount();
        }
        $this->applied_discounts = $this->cart_discounts;
        $this->total = $this->totalBeforeDiscounts - $this->applied_discounts; 
        
    }     */
    
    function getOrderDiscounts() 
    {                                 
        $od = new order_discounts();
        return $od->find('all', 'orders_id =' . $this->id);     
    }
    
	public function calculateGrandTotal() {
	    // calulate promo codes and group discounts
        //we need to tally up the cart, apply discounts, TAX that TOTAL somehow (different tax clases come into play), then add shipping
                 
        //grab our discounts
        $cartDiscounts = $this->getOrderDiscounts();
        
        //reset totals
        $this->total_discounts = 0;
        $this->shipping_total = 0;      
        $this->surcharge_total = 0;  
        $this->subtotal = 0;
        $this->total = 0;
        $this->grand_total = 0;
        $this->tax = 0;
        
        //hate doing double loops, but we need to have the subtotal figured out already for 
        //doing the straight dollar disoount calculations below
        for($i=0; $i<count($this->orderitem); $i++) {
            // figure out the amount of the discount
            /*if (!empty($this->product_discounts)) {
                $discount_amount = ($this->orderitem[$i]->products_price * ($this->product_discounts * .01));
                // change the price of the orderitem..this is needed for when we calculate tax below.
                $this->orderitem[$i]->products_price = $this->orderitem[$i]->products_price - $discount_amount;
                // keep a tally  of the total amount being subtracted by this discount.
                $this->total_discounts += $discount_amount;                
            }*/  
            //$this->orderitem[$i]->products_price = $this->orderitem[$i]->getPriceWithOptions(); // * $this->orderitem[$i]->quantity;
            $this->orderitem[$i]->products_price_adjusted = $this->orderitem[$i]->products_price;
            
            //$this->orderitem[$i]->products_price_original = $this->orderitem[$i]->product->getPrice();
            $this->subtotal += $this->orderitem[$i]->products_price * $this->orderitem[$i]->quantity; 
            $this->surcharge_total .= ($this->orderitem[$i]->product->surcharge * $this->orderitem[$i]->quantity); 
        }
        
        for($i=0; $i<count($this->orderitem); $i++) {
            //only allowing one discount for now, but in future we'll need to process
            //multiple and accomdate the "weight" and 'allow other discounts' type settings
            //this foreach will only fire once as of now, and will only hit on one or the other
            //TODO: We need to use produce_price_adjusted in the loops to accomodate for more than one disocunt
            //otherwise it's just resetting them now instead of adding them 
            foreach ($cartDiscounts as $od)
            {
                $discount = new discounts($od->discount_id);
                if ($discount->action_type == 3)
                {                       
                    $discount_amount = round($this->orderitem[$i]->products_price * ($discount->discount_percent / 100),2);
                    // change the price of the orderitem..this is needed for when we calculate tax below.
                    $this->orderitem[$i]->products_price_adjusted = $this->orderitem[$i]->products_price - $discount_amount;
                    // keep a tally  of the total amount being subtracted by this discount.
                    $this->total_discounts += $discount_amount * $this->orderitem[$i]->quantity;                
                }    
                
                if ($discount->action_type == 4)
                {   
                    //what % of the order is this product with all it's quantity                    
                    $percentOfTotalOrder = ($this->orderitem[$i]->products_price * $this->orderitem[$i]->quantity) / $this->subtotal ;                
                    //figoure out how much that'll be and what each quanityt piece will bare
                    $discountAmountPerItem = round(($percentOfTotalOrder * $discount->discount_amount) / $this->orderitem[$i]->quantity,2);
                    //$discount_amount = $this->orderitem[$i]->products_price * ($discount->discount_percent / 100);
                    // change the price of the orderitem..this is needed for when we calculate tax below.
                    $this->orderitem[$i]->products_price_adjusted = $this->orderitem[$i]->products_price - $discountAmountPerItem;
                    // keep a tally  of the total amount being subtracted by this discount.
                    $this->total_discounts += $discountAmountPerItem * $this->orderitem[$i]->quantity;                
                }                              
            }
            
            // calculate the tax for this product
            $taxclass = new taxclass($this->orderitem[$i]->product->tax_class_id);
            $this->orderitem[$i]->products_tax = $taxclass->getProductTax($this->orderitem[$i]);
            $this->tax += $this->orderitem[$i]->products_tax * $this->orderitem[$i]->quantity;
            
            //save out the order item
            $this->orderitem[$i]->save();
        }
   	    
   	    // add the "cart discounts" - percentage for sure, but straight can work also should be added after the final total is calculated, 
        //including tax but not shipping                                                     
        // $this->updateOrderDiscounts();  
        
        /*foreach ($cartDiscounts as $od)
        {
            $discount = new discounts($od->discount_id); 
            if ($discount->action_type == 4)
            {                       
                 $this->total_discounts += $discount->discount_amount;                           
            }                 
        }   */
                  
	    // calculate the shipping costs - need to check shipping discounts here in the future
	    
	    if ($this->shipping_required) {	        
	        $shippingmethods = $this->getShippingMethods();
	        foreach ($shippingmethods as $sm_id) {
	            $method = new shippingmethod($sm_id,true);                
	            if ($method->requiresShipping()) {                    
                    /*
                    //need to implement handling
                    $shippingCalc = new shippingcalculator($method->shippingcalculator_id);
                    $calc = new $shippingCalc->calculator_name($method->shippingcalculator_id);
                    eDebug($calc,true);*/                  
    	            $this->shipping_total += $method->shipping_cost; // + $method->calculator->getHandling();
	            }
	        }	        
	    }
        
        //needs lots of love here before we can implement
        /*foreach ($this->getOrderDiscounts() as $od)
        {
            $discount = new discounts($od->discount_id); 
            if ($discount->action_type == 5)
            {                       
                  $this->shipping_total = 0;  
            }                 
        }*/ 
        
        //check here to make sure we don't discount ourselves into oblivion          
        $orderTotalPreDiscounts =  $this->subtotal + $this->tax + $this->shipping_total;
        if ($this->total_discounts > $orderTotalPreDiscounts) $this->total_discounts = $orderTotalPreDiscounts;
        $this->total = $this->subtotal - $this->total_discounts;
        
        // figure out which tax zones apply to this order.
        $this->taxzones = taxclass::getCartTaxZones($this);
        
	    $this->grand_total = ($this->subtotal - $this->total_discounts) + $this->tax + $this->shipping_total + $this->surcharge_total;
        //eDebug($this, true); 
	}
    
    public function getOrderType() {
        global $db;
        return $db->selectValue('order_type', 'title', 'id='.$this->order_type_id);
    }
    
    public function setDefaultOrderType() {
        global $db;
        $default = $db->min('order_type', 'rank');
        $this->order_type_id = $db->selectValue('order_type', 'id', 'rank='.$default);
        $this->save();
    }
    
    public function getStatus() {
        global $db;
        return $db->selectValue('order_status', 'title', 'id='.$this->order_status_id);
    }
    
    public function setDefaultStatus() {
        global $db;
        $default = $db->min('order_status', 'rank');
        $this->order_status_id = $db->selectValue('order_status', 'id', 'rank='.$default);
        $this->save();
    }
    
    public function getInvoiceNumber() {
        global $db;
        $invoice_num = $db->max('orders', 'invoice_id') + 1;
	    if ($invoice_num < ecomconfig::getConfig('starting_invoice_number')) $invoice_num += ecomconfig::getConfig('starting_invoice_number');
	    return $invoice_num;
    }
    
    public function isItemInCart($id, $type) {
        if (empty($id) || empty($type)) return false;
        
        foreach($this->orderitem as $item) {
            // return true if we find the item in the users cart
            if ($item->product_type == $type && $item->product_id == $id) return $item;
        }
        
        // if we make it here we didn't find the item
        return false;
    }
}
?>
