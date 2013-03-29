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
class order extends expRecord {
    protected $table = 'orders';
    public $has_many = array('orderitem', 'order_discounts', 'billingmethod', 'order_status_changes');
    public $has_one = array('order_status', 'order_type', 'shippingmethod');
    public $get_assoc_for = array('orderitem', 'billingmethod', 'order_discounts');
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

    protected $attachable_item_types = array(//'content_expFiles'=>'expFile',
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );

    //public $status_codes = array(0=>'New', 1=>'Opened', 2=>'Processing', 3=>'Shipped');

    function __construct($params = null, $get_assoc = true, $get_attached = true) {
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
            foreach ($this->getShippingMethods() as $smid) {
                $this->shippingmethods[$smid]            = new shippingmethod($smid);
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
            if (isset($this->order_status_changes)) usort($this->order_status_changes, array("order", "sortStatuses"));
        }
    }

    private function setReturnCount($orig_referrer, $merge_array = array()) {
        global $router;
        if ($this->return_count != "") {
            $retArray = expUnserialize($this->return_count);
        } else {
            $retArray = array();
        }

        $retArray[] = array('timestamp'=> time(), 'orig_referrer'=> $orig_referrer, 'ectid'=> $router->getTrackingId());
        return serialize($retArray);
    }

    private function mergeReturnCount($merge_array = array()) {
        if ($this->return_count != "") {
            $retArray = expUnserialize($this->return_count);
        } else {
            $retArray = array();
        }

        if (!is_array($merge_array)) $merge_array = expUnserialize($merge_array);

        if (count($merge_array)) {
            foreach ($merge_array as $retCount) {
                $retArray[] = $retCount;
            }
        }

        return serialize($retArray);
    }

    static function setCartCookie($cart) {
        if ($cart) {
            setcookie("cid", $cart->id, time() + 3600 * 24 * 45, '/');
            $_COOKIE['cid'] = $cart->id;
        } else {
            setcookie("cid", $cart->id, time() - 3600 * 24 * 45, '/');
            unset($_COOKIE['cid']);
        }
        return;
    }

    static function getUserCart() {
        global $db, $user, $router;

        $sessAr = expSession::get('verify_shopper');
        // initialize this users cart if they have ecomm installed.
        $active = $db->selectValue('modstate', 'active', 'module="store"' || ECOM);
        if (!expModules::controllerExists('cart') || empty($active)) {
            // if ecomm is turned off, no cart.
            return null;
        } else if (isset($router->params['controller']) && $router->params['controller'] == 'order' &&
            ($router->params['action'] == 'verifyReturnShopper' || $router->params['action'] == 'verifyAndRestoreCart' ||
                $router->params['action'] == 'clearCart') &&
            (!isset($sessAr['validated']) || $sessAr['validated'] != true)
        ) {
            return new order();
        } else {
            // if ecomm is turned off, no cart.		    
            //$active = ;
            if (empty($active)) return null;
            $order  = new order(); //initialize a new order object to use the find function from.
            $ticket = expSession::getTicketString(); //get this users session ticket. this is how we track anonymous users.
            // grab the origional referrer from the session table so that we can transfer it into the cart where it will be used for reporting purposes
            // sessions are temporary so we can't report on the referrer in the session table itsef because it may not be there
            // and we can't just get the referrer ar this point becaues the user likely navigated the site a bit and we want the origional referring site
            $orig_referrer = $db->selectValue('sessionticket', 'referrer', "`ticket`='" . $ticket . "'");

            //see if we have a LIVE and ACTIVE session w/ cart and grab it if so
            $sessioncart = $order->find('first', "invoice_id='' AND sessionticket_ticket='" . $ticket . "'");

            //check to see if the user is logged in, and if so grab their existing cart
            if (!empty($user) && $user->isLoggedIn()) {
                $usercart = $order->find('first', "invoice_id='' AND user_id=" . $user->id);
            }

            //eDebug($sessioncart);
            //eDebug($usercart);

            //enter here if we have NO ACTIVE SESSION CART -OR- We're awaiting a potential cart retore
            if (empty($sessioncart->id) || $sessAr['awaiting_choice'] == true) {
                if (empty($usercart->id)) {
                    // no SESSION cart was found and user is not logged in...
                    //let's see if they have a cart_id cookie set and we'll snag that if so
                    //they won't have any user data, since they are "logged in" once they get to 
                    //checkout, so all we're really doing here is populating a cart for return 
                    //shoppers
                    $cookie_cart_id = isset($_COOKIE['cid']) ? $_COOKIE['cid'] : 0;
                    //eDebug($cookie_cart_id,true);
                    if ($cookie_cart_id) {
                        $tmpCart = new order($cookie_cart_id);
                        if ($tmpCart->id != $cookie_cart_id) {
                            //cookie set, but we gots no cart in the DB so act as if we had no cookie
                            $cart = new order();
                            $cart->update(array("sessionticket_ticket"=> $ticket, 'user_id'=> $user->id, 'orig_referrer'=> $orig_referrer, 'return_count'=> $cart->setReturnCount($orig_referrer)));
                            order::setCartCookie($cart);
                        } else {
                            $u = new user($tmpCart->user_id);
                            //1) Was Not logged in
                            if (empty($tmpCart->user_id) /*&& count($tmpCart->orderitem) == 0*/) {
                                $cart = new order($cookie_cart_id);
                                //update the session ticket and return count                                                                
                                $cart->update(array('sessionticket_ticket'=> $ticket, 'return_count'=> $cart->setReturnCount($orig_referrer)));
                                order::setCartCookie($cart);
                                flash('message', gt('Welcome back'));
                            } //2) Was logged in
                            else if (!empty($tmpCart->user_id)) {
                                //check for is admin first
                                if ($u->isActingAdmin() || $u->isAdmin()) {
                                    //no need to restore anything.
                                    $cart = new order();
                                    $cart->update(array("sessionticket_ticket"=> $ticket, 'user_id'=> $user->id, 'orig_referrer'=> $orig_referrer));
                                    order::setCartCookie($cart);
                                } //Was Logged in with NO items in cart
                                else if (!empty($tmpCart->user_id) && count($tmpCart->orderitem) == 0) {
                                    //silently copy tracking data from old order and continue on
                                    $cart = new order();
                                    $cart->update(array("sessionticket_ticket"=> $ticket, 'user_id'=> $user->id, 'orig_referrer'=> $orig_referrer, 'return_count'=> $tmpCart->setReturnCount($orig_referrer)));
                                    order::setCartCookie($cart);
                                    flash('message', gt('Welcome back'));
                                } //3) Was logged in WITH items in cart
                                else if (!empty($tmpCart->user_id) && count($tmpCart->orderitem) > 0) {
                                    //3) Was Logged in w/ NON-?real user? account
                                    //eDebug(expUtil::right($u->username,10),true);
                                    if ($u->isTempUser()) {
                                        if (isset($sessAr['validated']) && $sessAr['validated']) {
                                            //already went through validation and we're good to go
                                            $cart = new order($sessAr['cid']);
                                            //update the session ticket and return count                              
                                            $cart->update(array('sessionticket_ticket'=> $ticket, 'return_count'=> $cart->mergeReturnCount($sessioncart->return_count), 'orig_referrer'=> $sessioncart->orig_referrer));
                                            order::setCartCookie($cart);
                                            expSession::un_set('verify_shopper');
                                            $user = new user($cart->user_id);
                                            expSession::login($user);
                                            //Update the last login timestamp for this user.
                                            $user->updateLastLogin();
                                            flash('message', gt('Welcome back') . ' ' . $sessAr['firstname'] . '! ' . gt('Your shopping cart has been restored - you may continue shopping or') . ' <a href="' . makelink(array("controller"=> "cart", "action"=> "checkout"),true) . '">checkout</a> ' . gt('at your convenience.'));
                                        } else {
                                            //send to verification? If user has elected to restore their cart
                                            //eDebug($_SESSION);
                                            if (isset($sessAr['awaiting_choice']) && $sessAr['awaiting_choice'] == true) {
                                                /*expSession::set('verify_shopper',array('au'=>1,'orig_path'=>$router->current_url, 'firstname'=>$u->firstname, 'cid'=>$cookie_cart_id));
                                                redirect_to(array("controller"=>"order",'action'=>'verifyReturnShopper'));
                                                orderController::verifyReturnShopper();*/
                                                //just give em the sessioncart
                                                $cart = $sessioncart;
                                                if (count($cart->orderitem) > 0) {
                                                    //added items to current cart, so we'll assume they do not want to restore the previous at this point
                                                    expSession::un_set('verify_shopper');
                                                    order::setCartCookie($cart);
                                                } else {
                                                    flash('message', gt('Welcome back') . ' ' . $u->firstname . '! ' . gt('We see that you have shopped with us before.') . '<br><br><a id="submit-verify" href="' . makelink(array("controller"=> "order", "action"=> "verifyReturnShopper")) . '" rel="nofollow">' . gt('Click Here to Restore Your Previous Shopping Cart') . '</a><br><br><a class="exp-ecom-link" href="' . makelink(array("controller"=> "order", "action"=> "clearCart", "id"=> $cookie_cart_id)) . '">' . gt('Click Here To Start a New Shopping Cart') . '</a>');
                                                    $sessAr['orig_path'] = $router->current_url;
                                                    expSession::set('verify_shopper', $sessAr);
                                                }
                                            } else {
                                                //first time...create a default cart, issue message, set session, rinse, repeat
                                                $cart = new order();
                                                $cart->update(array("sessionticket_ticket"=> $ticket, 'return_count'=> $cart->setReturnCount($orig_referrer)));
                                                expSession::set('verify_shopper', array('au'=> 1, 'orig_path'=> $router->current_url, 'firstname'=> $u->firstname, 'cid'=> $cookie_cart_id, 'awaiting_choice'=> true));
                                                //order::setCartCookie($cart);
                                                flash('message', gt('Welcome back') . ' ' . $u->firstname . '! ' . gt('We see that you have shopped with us before.') . '<br><br><a id="submit-verify" href="' . makelink(array("controller"=> "order", "action"=> "verifyReturnShopper")) . '" rel="nofollow">' . gt('Click Here to Restore Your Previous Shopping Cart') . '</a><br><br><a class="exp-ecom-link" href="' . makelink(array("controller"=> "order", "action"=> "clearCart", "id"=> $cookie_cart_id)) . '">' . gt('Click Here To Start a New Shopping Cart') . '</a>');
                                            }
                                        }
                                    } //4) Was Logged in w/ REAL user account: -- check or ADMIN!
                                    else {
                                        //prompt to login and restore, otherwise reset and start fresh
                                        //this should be all we need to do here
                                        //redirect_to(array("controller"=>"order",'action'=>'verifyReturnShopper','au'=>'0'));
                                        $cart = new order();
                                        $cart->update(array("sessionticket_ticket"=> $ticket, 'user_id'=> $user->id, 'orig_referrer'=> $orig_referrer));
                                        order::setCartCookie($cart);
                                        flash('message', gt('Welcome back') . ' ' . $u->firstname . '! ' . gt('If you would like to pick up where you left off, click here to login and your previous shopping cart will be restored.'));
                                    }
                                }
                            }
                        }
                    } else // no cookie, so create a new cart and set the cookie
                    {
                        $cart = new order();
                        $cart->update(array("sessionticket_ticket"=> $ticket, 'user_id'=> $user->id, 'orig_referrer'=> $orig_referrer));
                        order::setCartCookie($cart);
                    }
                } else {
                    //user is logged in, so we grab their usercart and update the session ticket only
                    //$usercart->update(array('sessionticket_ticket'=>$ticket, 'orig_referrer'=>$orig_referrer));
                    $usercart->update(array('sessionticket_ticket'=> $ticket));
                    $cart = $usercart;
                }
                //enter here if we HAVE an ACTIVE session/cart, but the user is not logged in
            } elseif (!empty($sessioncart->id) && $user->id == 0) {
                // the user isn't logged in yet...the session cart will do for now.
                $cart = $sessioncart;

                // if we hit here we've found a session cart AND a usercart because the user just logged in
                // and had both...that means we need to merge them
            } elseif (!empty($sessioncart->id) && !empty($usercart->id)) {
                // if we hit here we've found a session cart and a usercart...that means we need to merge them
                // if it's not the same cart.
                if ($sessioncart->id == $usercart->id) {
                    $cart = $sessioncart;
                } else {
                    // if the old user cart had gone through any of the checkout process before, than we
                    // will clean that data out now and start fresh.
                    $usercart->cleanOrderitems();

                    //merge the current session cart with previously saved user cart.
                    foreach ($sessioncart->orderitem as $orderitem) {
                        $orderitem->merge(array('orders_id'=> $usercart->id, 'user_id'=> $user->id));
                    }
                    //if session cart HAS coupon codes, delete usercart codes and copy new code to usercart, else leave be
                    if (count($sessioncart->getOrderDiscounts())) {
                        foreach ($usercart->getOrderDiscounts() as $od) {
                            $od->delete();
                        }
                        foreach ($sessioncart->getOrderDiscounts() as $sod) {
                            $sod->orders_id = $usercart->id;
                            $sod->save();
                        }
                    }

                    $cart = new order($usercart->id);
                    $sessioncart->delete();
                }
                order::setCartCookie($cart);
                expSession::un_set('verify_shopper');
                // the user doesn't have a cart with his/her user id in it. this probably means they just
                // logged in so we need to update the cart with the new user id information.
            } elseif (!empty($sessioncart->id) && (empty($usercart->id) && $user->isLoggedIn())) {

                //$sessioncart->update(array('user_id'=>$user->id, 'orig_referrer'=>$orig_referrer));
                $sessioncart->update(array('user_id'=> $user->id));
                $cart = $sessioncart;
            }

            $cart->item_count = 0;
            foreach ($cart->orderitem as $items) {
                if ($items->product->requiresShipping && !$items->product->no_shipping) $cart->shipping_required = true;
                if ($items->product->requiresBilling) $cart->billing_required = true;
                $cart->item_count += $items->quantity;
            }

            $cart->lastcat = expSession::get('last_ecomm_category');
            $cart->total   = $cart->getCartTotal();
            //eDebug($cart,true);
            return $cart;
        }
    }

    public function cleanOrderitems() {
        $deleted_items = array();
        foreach ($this->orderitem as $orderitem) {
            if (!in_array($orderitem->id, $deleted_items)) {
                $similar_items = $orderitem->find('all', "orders_id=" . $this->id . " AND product_id=" . $orderitem->product_id . " AND product_type='" . $orderitem->product_type . "' AND options='" . $orderitem->options . "' AND id !=" . $orderitem->id);
                foreach ($similar_items as $similar_item) {
                    $orderitem->quantity = $orderitem->quantity + $similar_item->quantity;
                    $deleted_items[]     = $similar_item->id;
                    $similar_item->delete();
                }

                $shippingmethod = new shippingmethod($orderitem->shippingmethods_id);
                $shippingmethod->delete();
                $orderitem->shippingmethods_id = 0;
                $orderitem->products_tax       = 0;
                $orderitem->save();
            }
        }
    }

    public function getCurrentShippingMethod() {
        $sm_ids = $this->getShippingMethods();
        $sm     = new shippingmethod(current($sm_ids));
        return $sm;
    }

    public function getShippingMethods() {
        global $db;
        $ids = $db->selectColumn('orderitems', 'shippingmethods_id', 'shippingmethods_id!=0 AND orders_id=' . $this->id, null, true);

        //if we have no order items, then we'll set a 'default' shipping method to the order
        if (empty($ids)) {
            if (isset($this->shippingmethod->id)) {
                $ids = array($this->shippingmethod->id);
            } else {
                $sm = new shippingmethod();
                //(eDebug($db->selectValue('shippingcalculator','id','is_default=1'),true));
                $sm->shippingcalculator_id = $db->selectValue('shippingcalculator', 'id', 'is_default=1');
                $sm->save();
                //$this->setActiveShippingMethod($sm);
                $this->shippingmethods_id = $sm->id;
                $this->save();
                $this->refresh();
                $ids = array($sm->id);
            }
        }
        return $ids;
    }

    /*private function setActiveShippingMethod($sm) {
        $this->shippingmethods[] = $sm;
    }*/

    public function setReferencingIds() {
        $ref_orders            = $this->find('all', 'reference_id=' . $this->id, null, null, null, false, false);
        $this->referencing_ids = array();
        foreach ($ref_orders as $ref_id) {
            $this->referencing_ids[] = $ref_id->id;
        }
        return;
    }

    public function forcedShipping() {
        $this->forced_shipping = false;
        foreach ($this->orderitem as $item) {
            if (!empty($item->product->required_shipping_method)) {
                $this->forced_shipping          = true;
                $this->product_forcing_shipping = $item->product;
                return true;
            }
        }

        //check discounts requiring forced shipping
        $o   = new order_discounts();
        $ods = $o->find('all', 'orders_id=' . $this->id);
        foreach ($ods as $od) {
            if ($od->requiresForcedShipping()) {
                $this->forced_shipping = true;
                return true;
            }
        }
        return false;
    }

    public function getForcedShippingMethod() {
        global $db, $user;

        $forced_calc   = '';
        $forced_method = '';
        foreach ($this->orderitem as $item) {
            if (!empty($item->product->required_shipping_method)) {
                $method                         = new shippingmethod($item->shippingmethods_id);
                $forced_calc                    = $item->product->required_shipping_calculator_id;
                $forced_method                  = $item->product->required_shipping_method;
                $this->forced_shipping          = true;
                $this->product_forcing_shipping = $item->product;
                $this->forcing_shipping_reason  = $item->product->title;
                break;
            }
        }

        #FJD - TODOD: this will require some more work; eg. combining a free shipping discount code with a 
        #product in the cart that is also forcing shipping.  He coupon could require the lowest shipping
        #method, but the product could require overnight or a high-end shipping, so we need to account for this
        //check discounts requiring forced shipping
        if ($forced_calc == '') {
            $o   = new order_discounts();
            $ods = $o->find('all', 'orders_id=' . $this->id);
            foreach ($ods as $od) {
                if ($od->requiresForcedShipping()) {
                    $method                        = new shippingmethod($this->orderitem[0]->shippingmethods_id);
                    $forced_calc                   = $od->getRequiredShippingCalculatorId();
                    $forced_method                 = $od->getRequiredShippingMethod();
                    $this->forced_shipping         = true;
                    $this->forcing_shipping_reason = gt('The discount code you are using');
                    break;
                }
            }
        }
        ###################

        // if this shippingmethod doesn't have an address assigned to it, lets check and see if this
        // user has set one up yet and default to that if so
        if (empty($method->addresses_id) && $user->isLoggedIn()) {
            $address = new address();
            $addy    = $address->find('first', 'user_id=' . $user->id . '  AND is_default=1');
            if (!empty($addy->id)) $method->setAddress($addy);
        }

        $calcname   = $db->selectValue('shippingcalculator', 'calculator_name', 'id=' . $forced_calc);
        $calculator = new $calcname($forced_calc);
        $rates      = $calculator->getRates($this);
        $rate       = $rates[$forced_method];
        $method->update(array('option'=> $forced_method, 'option_title'=> $rate['title'], 'shipping_cost'=> $rate['cost'], 'shippingcalculator_id'=> $forced_calc));
        return $method;
    }

    public function getCurrentBillingMethod() {
        $bm_ids = $this->getBillingMethods();
        $bm     = new billingmethod(current($bm_ids));
        return $bm;
    }

    public function getBillingMethods() {
        global $db;
        return $db->selectColumn('billingmethods', 'id', 'orders_id=' . $this->id, null, true);
    }

    public function getOrderitemsByShippingmethod($shippingmethod_id) {
        $orderitem = new orderitem(null, false, false);
        return $orderitem->find('all', 'orders_id=' . $this->id . " AND shippingmethods_id=" . $shippingmethod_id);
    }

    public function countOrderitemsByShippingmethod($shippingmethod_id) {
        $orderitem = new orderitem(null, false, false);
        return $orderitem->find('count', 'orders_id=' . $this->id . " AND shippingmethods_id=" . $shippingmethod_id);
    }

    public function getCartTotal() {
        $total = 0;
        foreach ($this->orderitem as $item) {
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
    /*function updateOrderDiscounts()
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
    }*/

    function validateDiscounts($redirectOnFailureTo = array('controller'=> 'cart', 'action'=> 'show')) {
        $discounts = $this->getOrderDiscounts();
        if (count($discounts)) {
            foreach ($discounts as $od) {
                $od->validate($redirectOnFailureTo);
            }
            $this->refresh();
            $this->calculateGrandTotal();
        }
        return $discounts;
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

    function getOrderDiscounts() {
        /*$od = new order_discounts();
   return $od->find('all', 'orders_id =' . $this->id);*/
        if (isset($this->order_discounts)) return $this->order_discounts;
        else return null;
    }

    public function calculateGrandTotal() {
        // calulate promo codes and group discounts
        //we need to tally up the cart, apply discounts, TAX that TOTAL somehow (different tax clases come into play), then add shipping

        //grab our discounts
        $cartDiscounts = $this->getOrderDiscounts();

        //reset totals
        $this->total_discounts                 = 0;
        $this->shipping_total                  = 0;
        $this->shipping_total_before_discounts = 0;
        $this->shippingDiscount                = 0;
        $this->surcharge_total                 = 0;
        $this->subtotal                        = 0;
        $this->total                           = 0;
        $this->grand_total                     = 0;
        $this->tax                             = 0;
        $validateDiscountMessage               = '';
        //eDebug($this->surcharge_total);
        //hate doing double loops, but we need to have the subtotal figured out already for 
        //doing the straight dollar disoount calculations below
        for ($i = 0; $i < count($this->orderitem); $i++) {
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
//            $this->subtotal += $this->orderitem[$i]->products_price * $this->orderitem[$i]->quantity;
            $this->subtotal += $this->orderitem[$i]->getTotal();

            $this->surcharge_total += ($this->orderitem[$i]->product->getSurcharge() * $this->orderitem[$i]->quantity);

        }

        for ($i = 0; $i < count($this->orderitem); $i++) {
            //only allowing one discount for now, but in future we'll need to process
            //multiple and accomdate the "weight" and 'allow other discounts' type settings
            //this foreach will only fire once as of now, and will only hit on one or the other
            //TODO: We need to use produce_price_adjusted in the loops to accommodate for more than one discount
            //otherwise it's just resetting them now instead of adding them 
            foreach ($cartDiscounts as $od) {
                //do not calculate invalid discounts, but don't remove either
                $discount = new discounts($od->discounts_id);
                /*$validateDiscountMessage = $discount->validateDiscount();
             if($validateDiscountMessage != '') break;*/

                //percentage discount             
                if ($discount->action_type == 3) {
                    $discount_amount = round($this->orderitem[$i]->products_price * ($discount->discount_percent / 100), 2);
                    // change the price of the orderitem..this is needed for when we calculate tax below.
                    $this->orderitem[$i]->products_price_adjusted = $this->orderitem[$i]->products_price - $discount_amount;
                    // keep a tally  of the total amount being subtracted by this discount.
                    $this->total_discounts += $discount_amount * $this->orderitem[$i]->quantity;
                }

                //straight $$ discount 
                if ($discount->action_type == 4) {
                    $this->total_discounts = $discount->discount_amount;
                    //what % of the order is this product with all it's quantity                    
                    $percentOfTotalOrder = ($this->orderitem[$i]->products_price * $this->orderitem[$i]->quantity) / $this->subtotal;
                    //figoure out how much that'll be and what each quanityt piece will bare
                    $discountAmountPerItem = round(($percentOfTotalOrder * $discount->discount_amount) / $this->orderitem[$i]->quantity, 2);
                    //$discount_amount = $this->orderitem[$i]->products_price * ($discount->discount_percent / 100);
                    // change the price of the orderitem..this is needed for when we calculate tax below.
                    $this->orderitem[$i]->products_price_adjusted = $this->orderitem[$i]->products_price - $discountAmountPerItem;
                    // keep a tally  of the total amount being subtracted by this discount.
                    //$this->total_discounts += $discountAmountPerItem * $this->orderitem[$i]->quantity;                    //eDebug($discountAmountPerItem);
                }
            }

            // calculate the tax for this product
            $taxclass                          = new taxclass($this->orderitem[$i]->product->tax_class_id);
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
         $discount = new discounts($od->discounts_id);
         if ($discount->action_type == 4)
         {
              $this->total_discounts += $discount->discount_amount;
         }
     }   */

        // calculate the shipping costs - need to check shipping discounts here in the future
        $estimate_shipping = false;
        if ($this->shipping_required) {
            $shippingmethods = $this->getShippingMethods();
            if (count($shippingmethods) > 0) {
                foreach ($shippingmethods as $sm_id) {
                    $method = new shippingmethod($sm_id, true);

                    if ($method->requiresShipping($this)) {
                        /*
                        //need to implement handling
                        $shippingCalc = new shippingcalculator($method->shippingcalculator_id);
                        $calc = new $shippingCalc->calculator_name($method->shippingcalculator_id);
                        eDebug($calc,true);*/
                        $this->shipping_total += $method->shipping_cost; // + $method->calculator->getHandling();
                    }
                }
            } else {
                $estimate_shipping = true;
            }
        }

        $this->shipping_total_before_discounts = $this->shipping_total;

        if (isset($cartDiscounts)) {
            foreach ($cartDiscounts as $od) {
                $discount             = new discounts($od->discounts_id);
                $this->shipping_total = $discount->calculateShippingTotal($this->shipping_total);
            }
        }
        $this->shippingDiscount = $this->shipping_total_before_discounts - $this->shipping_total;

        //check here to make sure we don't discount ourselves into oblivion          
        $orderTotalPreDiscounts = $this->subtotal + $this->tax + $this->shipping_total;
        if ($this->total_discounts > $orderTotalPreDiscounts) $this->total_discounts = $orderTotalPreDiscounts;
        $this->total = $this->subtotal - $this->total_discounts;

        $estimate_shipping = true;
        if ($estimate_shipping && !$this->shipping_total) $this->shipping_total = shipping::estimateShipping($this);
        // figure out which tax zones apply to this order.
        $this->taxzones = taxclass::getCartTaxZones($this);

        $this->grand_total = ($this->subtotal - $this->total_discounts) + $this->tax + $this->shipping_total + $this->surcharge_total;

        //if($validateDiscountMessage != '') flash('message',$validateDiscountMessage);
        //eDebug($this, true); 
    }

    public function getOrderType() {
        global $db;
        return $db->selectValue('order_type', 'title', 'id=' . $this->order_type_id);
    }

    /*public function setDefaultOrderType() {
        global $db;
        $default = $db->min('order_type', 'rank');
        $this->order_type_id = $db->selectValue('order_type', 'id', 'rank='.$default);
        $this->save();
        return;
    }*/

    public function getStatus() {
        global $db;
        return $db->selectValue('order_status', 'title', 'id=' . $this->order_status_id);
    }

    /*public function setDefaultStatus() {
       global $db;
       $default = $db->min('order_status', 'rank');
       $this->order_status_id = $db->selectValue('order_status', 'id', 'rank='.$default);
       $this->save();
       return;
   } */

    public function getInvoiceNumber() {
        global $db;
        $sin = ecomconfig::getConfig('starting_invoice_number');
        //$invoice_num = $db->max('orders', 'invoice_id') + 1;

        //start by locking the table to prevent another session from starting this same
        //function before we are done with it.  Other sessions will wait until we're done, which 
        //should be just a few milliseconds.
        $db->lockTable("orders_next_invoice_id");

        //get the next id record
        $invoice_num = $db->max('orders_next_invoice_id', 'next_invoice_id');

        //if it's not set or botched, then reset to the starting invoice number
        $obj = new stdClass();
        if (empty($invoice_num) || $invoice_num < $sin) {
            $invoice_num = $sin;
            //insert the table with the next available number
            $obj->id              = 1;
            $obj->next_invoice_id = $invoice_num + 1;
            $db->insertObject($obj, 'orders_next_invoice_id');
        } else {
            //update the table with the next available number
            $obj->id              = 1;
            $obj->next_invoice_id = $invoice_num + 1;
            $db->updateObject($obj, 'orders_next_invoice_id');
        }

        //unlock the table and return.          
        $db->unlockTables();

        return $invoice_num;
    }

    public function isItemInCart($id, $type) {
        if (empty($id) || empty($type)) return false;

        foreach ($this->orderitem as $item) {
            // return true if we find the item in the users cart
            if ($item->product_type == $type && $item->product_id == $id) return $item;
        }

        // if we make it here we didn't find the item
        return false;
    }

    public function setOrderType($params) {

        if (isset($params['order_type'])) {
            $this->order_type_id = $params['order_type'];
        } else {
            $this->order_type_id = $this->getDefaultOrderType();
        }
        $this->save();
    }

    public function setOrderStatus($params) {

        if (isset($params['order_status'])) {
            $this->order_status_id = $params['order_status'];
        } else {
            $this->order_status_id = $this->getDefaultOrderStatus();
        }
        $this->save();
    }

    public function getOrderTypes() {
        $ot  = new order_type();
        $ots = $ot->find('all');
        $order_types = array();
        foreach ($ots as $order_type) {
            $order_types[$order_type->id] = $order_type->title;
        }
        return $order_types;
    }

    public function getDefaultOrderType() {
        $ot  = new order_type();
        $ots = $ot->find('first', 'is_default=1');
        //eDebug($ots,true);        
        return !empty($ots->id) ? $ots->id : false;
    }

    public function getOrderStatuses() {
        $os  = new order_status();
        $oss = $os->find('all');
        $order_statuses = array();
        foreach ($oss as $order_status) {
            $order_statuses[$order_status->id] = $order_status->title;
        }
        return $order_statuses;
    }

    public function getSalesReps() {
        $sr         = new sales_rep();
        $srs        = $sr->find('all');
        $sales_reps = array();
        foreach ($srs as $sales_rep) {
            $sales_reps[$sales_rep->id] = $sales_rep->initials;
        }
        return $sales_reps;
    }

    public function getDefaultOrderStatus() {
        $os  = new order_status();
        $oss = $os->find('first', 'is_default=1');
        //eDebug($ots,true);        
        return $oss->id;
    }

    static function sortStatuses($a, $b) {
        if ($a->created_at < $b->created_at) return 1;
        else if ($a->created_at > $b->created_at) return -1;
        else if ($a->created_at == $b->created_at) return 0;
    }
}

?>