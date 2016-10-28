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
 * @subpackage Controllers
 * @package    Modules
 */

class cartController extends expController {
    public $basemodel_name = 'order';
    private $checkout_steps = array('productinfo', 'specials', 'form', 'wizards', 'newsletter', 'confirmation', 'postprocess');

    public $useractions = array(
        'show'                         => 'Show Shopping Cart',
    );

        // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
        'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    static function displayname() {
        return gt("e-Commerce Shopping Cart");
    }

    static function description() {
        return gt("Displays the shopping cart contents from your store.");
    }

    function addItem() {
        global $router;

        $product_type = isset($this->params['product_type']) ? $this->params['product_type'] : 'product';
        $product      = new product();

        //if we're trying to add a parent product ONLY, then we redirect to it's show view
        $c = new stdClass();
        if (isset($this->params['product_id']) && empty($this->params['children'])) $c = $product->find('first', 'parent_id=' . $this->params['product_id']);
        if (!empty($c->id)) {
            flash('message', gt("Please select a product and quantity from the options listed below to add to your cart."));
            redirect_to(array('controller'=> 'store', 'action'=> 'show', 'id'=> $this->params['product_id']));
        }

        //check for multiple product adding
        if (isset($this->params['prod-quantity'])) {
            //we are adding multiple children, so we approach a bit different
            //we'll send over the product_id of the parent, along with id's and quantities of children we're adding

            foreach ($this->params['prod-quantity'] as $qkey=> &$quantity) {
                if (in_array($qkey, $this->params['prod-check'])) {
                    //this might not be working...FJD
                    $child = new $product_type($qkey);
                    /*if ($quantity < $child->minimum_order_quantity)
                    {
                        flash('message', $child->title . " - " . $child->model . " has a minimum order quantity of " . $child->minimum_order_quantity .
                        '. Your quantity has been adjusted accordingly.');
                        $quantity = $child->minimum_order_quantity;

                    }*/
                    $this->params['children'][$qkey] = $quantity;
                }
                if (isset($child)) $this->params['product_id'] = $child->parent_id;
            }
        }

        $product = new $product_type($this->params['product_id'], true, true); //need true here?

        //Check the Main Product quantity
        if (isset($this->params['quantity'])) {
            if (((int)$this->params['quantity']) < $product->minimum_order_quantity) {
                flash('message', gt("Please enter a quantity equal or greater than the minimum order quantity."));
                redirect_to(array('controller'=> 'store', 'action'=> 'show', 'id'=> $this->params['product_id']));
            } else {

            }
            // adjust multiple quantity here
            if ($product->multiple_order_quantity && ((int)$this->params['quantity']) % $product->multiple_order_quantity) {
                flash('message', gt("Please enter a quantity in multiples of") . ' ' . $product->multiple_order_quantity);
                redirect_to(array('controller'=> 'store', 'action'=> 'show', 'id'=> $this->params['product_id']));
            } else {

            }
        }

        // if needed we throw up a form to gather additional information before adding this item to the cart
//        if (($product->product_type == "product" || $product->product_type == "childProduct" || $product->product_type == "donation" || $product->product_type == "eventregistration") && empty($this->params['quick'])) {
//        if ($product->product_type != "giftcard" && empty($this->params['quick'])) {
//        if (empty($this->params['quick'])) {
//            //FIXME shouldn't this be relegated to $product->addToCart???
//            if (($product->hasOptions() && (!isset($this->params['options_shown']) || $this->params['options_shown'] != $product->id)) ||
//                ($product->hasUserInputFields() && (!isset($this->params['input_shown']) || $this->params['input_shown'] != $product->id))) {
//                // if we hit here it means this product type was missing some
//                // information it needs to add the item to the cart..so we need to help
//                // it display its addToCart form
//                /*redirect_to(array(
//                            'controller'=>'cart',
//                            'action'=>'displayForm',
//                            'form'=>'addToCart',
//                            'product_id'=>$this->params['product_id'],
//                            'product_type'=>$this->params['product_type'],
//                            'children'=>serialize($this->params['children']),
//                    ));*/
//                $product->displayForm('addToCart', $this->params);
//                return false;
//            }
//        }
        //product either has no options, user input fields, or has already seen and passed the options page, so we try adding to cart
        //it will validate and fail back to the options page if data is incorrect for whatever reason (eg, bad form post)
        if ($product->addToCart($this->params)) {
            // product was added
            if (ecomconfig::getConfig('show_cart') || !empty($this->params['quick'])) {
                // adding an item displays the shopping cart
//                global $order;
//                $order->calculateGrandTotal();
//                if (!$order->grand_total && !$order->shipping_required) {
//                    redirect_to(array('controller'=>'cart', 'action'=>'quickConfirm'));
//                } elseif (!$order->shipping_required) {
//                    redirect_to(array('controller'=>'cart', 'action'=>'quickPay'));
//                } else {
                //expHistory::back();
                //eDebug(show_msg_queue(false),true);
                redirect_to(array('controller'=>'cart', 'action'=>'show'));
                //expHistory::lastNotEditable();
//                }
            } else {
                // quick added, so just provide message
                if ($product->product_type == "donation") {
                    $type = ' '.gt('Donation');
                } elseif ($product->product_type == "eventregistration") {
                    $type = ' '.gt('Event');
                } else {
                    $type = '';
                }
                flash('message', gt("Added") . " " . $product->title . $type . " " . gt("to your cart.") . " <a href='" . $router->makeLink(array('controller'=> 'cart', 'action'=> 'checkout'), false, true) . "'>" . gt("Click here to checkout now.") . "</a>");
            }
        } else {
            return false;
        }
        expHistory::back();
    }

    function updateQuantity() {
        global $order;
        if (expJavascript::inAjaxAction()) {
            //FIXME though currently unused we don't account for minimym nor multiple quantity settings
            $id      = str_replace('quantity-', '', $this->params['id']);
            $item    = new orderitem($id);
            $updates = new stdClass();
            if (!empty($item->id)) {
                //$newqty = $item->product->updateQuantity($this->params['value']);
                $newqty = $item->product->updateQuantity($this->params['value']);
                if ($newqty > $item->product->quantity) {
                    if ($item->product->availability_type == 1) {
                        $diff             = ($item->product->quantity <= 0) ? $newqty : $newqty - $item->product->quantity;
                        $updates->message = 'Only ' . $item->product->quantity . ' ' . $item->products_name . ' are currently in stock. Shipping may be delayed on the other ' . $diff;
                    } elseif ($item->product->availability_type == 2) {
                        $updates->message    = $item->products_name . ' only has ' . $item->product->quantity . ' on hand. You can not add any more than that to your cart.';
                        $updates->cart_total = expCore::getCurrencySymbol() . number_format($order->getCartTotal(), 2);
                        $updates->item_total = expCore::getCurrencySymbol() . number_format($item->getTotal(), 2);
                        $updates->item_id    = $id;
                        $updates->quantity   = $item->product->quantity;
                        echo json_encode($updates);
                        return true;
                    }
                }
                $item->quantity = $newqty;
                $item->save();
                $order->refresh();
                $updates->cart_total = expCore::getCurrencySymbol() . number_format($order->getCartTotal(), 2);
                $updates->item_total = expCore::getCurrencySymbol() . number_format($item->getTotal(), 2);
                $updates->item_id    = $id;
                $updates->quantity   = $item->quantity;
                echo json_encode($updates);
            }
        } else {
            if (empty($this->params['quantity']) && !empty($this->params['qtyr'])) $this->params['quantity'] = $this->params['qtyr'];
            if (!is_numeric($this->params['quantity'])) {
                flash('error', gt('Please enter a valid quantity.'));
                expHistory::back();
            }

            $item = new orderitem($this->params['id']);

            if (!empty($item->id)) {
                //$newqty = $item->product->updateQuantity($this->params['quantity']);
                $newqty = $this->params['quantity'];
                //$oiObj = new orderitem();
                //$oi = $oiObj->find('all','product_id='.$item->product->id);
                $qCheck = 0; //$item->product->quantity;
                //if (!empty($oi))
                //{
                foreach ($order->orderitem as $orderItem) {
                    if ($orderItem->product_id == $item->product_id) $qCheck += $orderItem->quantity;
                }
                //eDebug("Done",true);
                //}
                /*eDebug($item->quantity);
                eDebug($item->product->quantity);
                eDebug($qCheck);
                eDebug($newqty,true);  */
                //check minimum quantity
                $qtyMessage = '';
                if ($newqty < $item->product->minimum_order_quantity) {
                    $qtyMessage = $item->product->title . ' has a minimum order quantity of ' . $item->product->minimum_order_quantity . '. The quantity has been adjusted and added to your cart.<br/><br/>';
                    $newqty     = $item->product->minimum_order_quantity;
                }
                // adjust multiple quantity here
                if ($newqty % $item->product->multiple_order_quantity) {
                    $qtyMessage = $item->product->title . ' must be ordered in multiples of ' . $item->product->multiple_order_quantity . '. The quantity has been adjusted up and added to your cart.<br/><br/>';
                    $offset = $newqty % $item->product->multiple_order_quantity;
                    $newqty     = $newqty - $offset + $item->product->multiple_order_quantity;
                }

                $itemMessage = '';
                if (($qCheck + ($newqty - $item->quantity)) > $item->product->quantity) {
                    if ($item->product->availability_type == 1) {
                        $diff        = ($item->product->quantity <= 0) ? $newqty : $newqty - $item->product->quantity;
                        $itemMessage = gt('Only') . ' ' . $item->product->quantity . ' ' . $item->products_name . ' ' . gt('are currently in stock. Shipping may be delayed on the other') . ' ' . $diff . "<br/><br/>";
                        //$updates->message = 'Only '.$item->product->quantity.' '.$item->products_name.' are currently in stock. Shipping may be delayed on the other '.$diff;
                    } elseif ($item->product->availability_type == 2) {
                        flash('error', $item->products_name . ' ' . gt('only has') . ' ' . $item->product->quantity . ' ' . gt('on hand. You can not add any more than that to your cart.'));
                        /*$updates->message = $item->products_name.' only has '.$item->product->quantity.' on hand. You can not add any more to your cart.';
                        $updates->cart_total = '$'.number_format($order->getCartTotal(), 2);
                        $updates->item_total = '$'.number_format($item->quantity*$item->products_price, 2);
                        $updates->item_id = $id;
                        $updates->quantity = $item->product->quantity;
                        echo json_encode($updates);  */
                        expHistory::back();
                    }
                } else if ($newqty <= 0) {
                    $item->delete();
                    flash('message', $item->products_name . ' ' . gt('has been removed from your cart.'));
                    expHistory::back();
                }
                $item->quantity = $newqty;
                $item->save();
                $order->refresh();

                /*$updates->cart_total = '$'.number_format($order->getCartTotal(), 2);
        $updates->item_total = '$'.number_format($item->quantity*$item->products_price, 2);
        $updates->item_id = $id;
        $updates->quantity = $item->quantity;      */
                //echo json_encode($updates);
            }
            //redirect_to(array('controller'=>'cart','action'=>'show'));
            flash('message', $qtyMessage . $itemMessage . $item->products_name . ' ' . gt('quantity has been updated.'));
            expHistory::back();
        }
    }

    function removeItem() {
        global $order;
        foreach ($order->orderitem as $item) {
            if ($item->id == intval($this->params['id'])) {
                $product = new  $item->product_type($item->product_id);
                $product->removeItem($item);
                $item->delete();
            }
        }

        expHistory::back();
    }

    function show() {
        global $order;

        //$cartinfo->''ecomconfig::getConfig('email_invoice')
        //$back = expHistory::getLast('viewable');
        //eDebug(new expHistory);
        expHistory::set('viewable', $this->params);
        //eDebug($order,true);
        if (isset($order)) {
            //this triggers creation/updating of the shippingmethod and setting
            //default rate if user has not yet chosen one.
//            $shipping = new shipping();
//            $shipping->getRates();
            $order->calculateGrandTotal();

            //eDebug($order,true);
            //check to see if we have calculate shipping yet - if shipping_total_before_discounts is set
            //to something other than 0, then we have, but we'll set the estimtae to shipping_total to
            //accomodate any applied discounts
            //if (!empty($order->shipping_total_before_discounts))
            //{
            //    $estimated_shipping = $order->shipping_total;
            //}
            //otherwise we'll grab an estimate
            //else
            //{
            //$estimated_shipping = shipping::estimateShipping($order);
            /* $shipping = new shipping();
          $shipping->getRates();
          //eDebug($shipping,true);
          $estimated_shipping = $shipping->pricelist['01']['cost'];*/
            //foreach ($order->orderitem as $item)
            //{
            //eDebug($item->product);
            //}
            //}

            // are there active discounts in the db?
            $discountCheck    = new discounts();
            $discountsEnabled = $discountCheck->find('all', 'enabled=1');
            if (empty($discountsEnabled)) {
                // flag to hide the discount box
                assign_to_template(array(
                    'noactivediscounts'=> '1'
                ));
                $discounts = null;
            } else {
                // get all current discount codes that are valid and applied
                $discounts = $order->validateDiscounts();
            }
        } else {
            $order              = new stdClass();
            $order->orderitem   = new stdClass();
            $items              = null;
            $discounts          = null;
//            $estimated_shipping = null;
        }
        assign_to_template(array(
            'items'    => $order->orderitem,
            'order'    => $order,
            'discounts'=> $discounts,
            /*'estimated_shipping'=>$estimated_shipping*/
        ));

    }

    function cart_only() {
        $this->show();
    }

    function quickpay_donation_cart() {
        $this->show();
    }

    function checkout() {
        global $user, $order, $router;

        if (empty($order)) {
            flash('error', gt('There is an error with your shopping cart.'));
            expHistory::back();
        }

//        $config   = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));

        $order->calculateGrandTotal();
        $order->validateDiscounts(array('controller'=> 'cart', 'action'=> 'checkout'));

        if (!expSession::get('customer-signup') && !$user->isLoggedin()) {  // give opportunity to login or sign up
            expHistory::set('viewable', $this->params);
            expSession::set('customer-login', true);
            flash('message', gt("Please select how you would like to continue with the checkout process."));
            expHistory::redirecto_login(makeLink(array('module'=> 'cart', 'action'=> 'checkout'), 'secure'),true);
        }

//        if ($order->total < intval($config->config['min_order'])) {
//            flashAndFlow('error',gt("Note: Thank you for your decision to purchase. However, our minimum order for merchandise is ").expCore::getCurrencySymbol() . number_format($config->config['min_order'], 2, ".", ",") . ". ".gt("Please increase your quantity or continue shopping."));
//        }
        if ($order->total < intval(ecomconfig::getConfig('min_order'))) {
            flashAndFlow('error',gt("Note: Thank you for your decision to purchase. However, our minimum order for merchandise is ").expCore::getCurrencySymbol() . number_format(ecomconfig::getConfig('min_order'), 2, ".", ",") . ". ".gt("Please increase your quantity or continue shopping."));
        }

        if (empty($order->orderitem)) flashAndFlow('error',gt('There are no items in your cart.'));

        if (!order::getDefaultOrderType()) {
            flashAndFlow('error', gt('This store is not yet fully configured to allow checkouts.')."<br>".gt('You Must Create a Default Order Type').' <a href="'.expCore::makeLink(array('controller'=>'order_type','action'=>'manage')).'">'.gt('Here').'</a>');
        }
        if (!order::getDefaultOrderStatus()) {
            flashAndFlow('error', gt('This store is not yet fully configured to allow checkouts.')."<br>".gt('You Must Create a Default Order Status').' <a href="'.expCore::makeLink(array('controller'=>'order_status','action'=>'manage')).'">'.gt('Here').'</a>');
        }

        $billing = new billing();
        //eDebug($billing,true);
        if (count($billing->available_calculators) < 1) {
            flashAndFlow('error', gt('This store is not yet fully configured to allow checkouts.')."<br>".gt('You Must Activate a Payment Option').' <a href="'.expCore::makeLink(array('controller'=>'billing','action'=>'manage')).'">'.gt('Here').'</a>');
        }
        // set a flow waypoint
        expHistory::set('viewable', $this->params);

        //this validate the discount codes already applied to make sure they are still OK
        //if they are not it will remove them and redirect back to checkout w/ a message flash
        //$order->updateOrderDiscounts();

        //eDebug($order);
        // are there active discounts in the db?
        $discountCheck    = new discounts();
        $discountsEnabled = $discountCheck->find('all', 'enabled=1');
        if (empty($discountsEnabled)) {
            // flag to hide the discount box
            assign_to_template(array(
                'noactivediscounts'=> '1'
            ));
            $discounts = null;
        } else {
            // get all current discount codes that are valid and applied
            $discounts = $order->getOrderDiscounts();
        }
        //eDebug($discounts);
        /*if (count($discounts)>=0) {
              // Mockup code
              $order->totalBeforeDiscounts = $order->total; // reference to the origional total
              $order->total = $order->total*85/100; // to simulate 15%

          } */
        // call each products checkout() callback & calculate total
        foreach ($order->orderitem as $item) {
            $product = new $item->product_type($item->product_id);
            $product->checkout();
        }

        // get the specials...this is just a stub function for now.
        $specials = $this->getSpecials();

        // get all the necessary addresses..shipping, billing, etc
        $address = new address();
        //$addresses_dd = $address->dropdownByUser($user->id);
        $shipAddress = $address->find('first', 'user_id=' . $user->id . ' AND is_shipping=1');
        if (empty($shipAddress) || !$user->isLoggedin()) {  // we're not logged in and don't have an address yet
            expSession::set('customer-signup', false);
            flash('message', gt('Enter your primary address info now.') .
                '<br><br>' .
                gt('You may also optionally provide a password if you would like to return to our store at a later time to view your order history or make additional purchases.') .
                '<br><br>' .
                gt('If you need to add another billing or shipping address you will be able to do so on the following page.'));
            redirect_to(array('controller'=> 'address', 'action'=> 'edit'));
        }

        // get the shipping calculators and the shipping methods if we need them
        $shipping = new shipping();
        //$shipping->shippingmethod->setAddress($shipAddress);
        if (count($shipping->available_calculators) < 1) {
            flashAndFlow('error', gt('This store is not yet fully configured to allow checkouts.')."<br>".gt('You Must Activate a Shipping Option').' <a href="'.expCore::makeLink(array('controller'=>'shipping','action'=>'manage')).'">'.gt('Here').'</a>');
        }

        // we need to get the current shipping method rates
        $shipping->getRates();

        if (strpos($router->current_url, 'https://') === false && (!defined('DISABLE_SSL_WARNING') || DISABLE_SSL_WARNING==0))
            flash('error', gt('This page appears to be unsecured!  Personal information may become compromised!'));

        assign_to_template(array(
//            'cartConfig'          => $config->config,
            //'addresses_dd'=>$addresses_dd,
            //'addresses'=>$addresses,
            'shipping'            => $shipping,
            'user'                => $user,
            'billing'             => $billing,
            'discounts'           => $discounts,
            'order'               => $order,
            'order_types'         => order::getOrderTypes(),
            'default_order_type'  => order::getDefaultOrderType(),
            'order_statuses'      => order::getOrderStatuses(),
            'default_order_status'=> order::getDefaultOrderStatus(),
            'sales_reps'          => order::getSalesReps()
            //'needs_address'=>$needs_address,
        ));
    }

    /**
     * the first thing after checkout.
     *
     */
    public function preprocess() {
        //eDebug($this->params,true);
        global $order, $user, $db;

//        // check to see if this is a no cost/no shipping checkout
//        if ($order->total == 0 && empty($order->shippingmethods) && $this->params['billingcalculator_id'] == 0) {
//             // final the cart totals
//            $order->calculateGrandTotal();
//            $order->setOrderType($this->params);
//            $order->setOrderStatus($this->params);
//        } else {
//
        //eDebug($_POST, true);
        // get the shipping and billing objects, these objects handle the setting up the billing/shipping methods
        // and their calculators
        $shipping = new shipping();
        $billing  = new billing();
        // since we're skipping the billing method selection, do it here
        $billing->billingmethod->update($this->params);
        //this is just dumb. it doesn't update the object, refresh doesn't work, and I'm tired
        $billing = new billing();

        if (!$user->isLoggedIn()) {
            flash('message', gt("It appears that your session has expired. Please log in to continue the checkout process."));
            expHistory::redirecto_login(makeLink(array('module'=> 'cart', 'action'=> 'checkout'), true));
        }

        // Make sure all the pertinent data is there...otherwise flash an error and redirect to the checkout form.
        if (empty($order->orderitem)) {
            flash('error', gt('There are no items in your cart.'));
        }
        if (empty($shipping->calculator->id) && !$shipping->splitshipping) {
            flash('error', gt('You must pick a shipping method'));
        }
        if (empty($shipping->address->id) && !$shipping->splitshipping) {
            flash('error', gt('You must pick a shipping address'));
        }
        if (empty($billing->calculator->id)) {
            flash('error', gt('You must pick a billing method'));
        }
        if (empty($billing->address->id)) {
            flash('error', gt('You must select a billing address'));
        }

        // make sure all the methods picked for shipping meet the requirements
        foreach ($order->getShippingMethods() as $smid) {
            $sm       = new shippingmethod($smid);
            $calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id=' . $sm->shippingcalculator_id);
            $calc     = new $calcname($sm->shippingcalculator_id);
            $ret      = $calc->meetsCriteria($sm);
            if (is_string($ret)) {
                flash('error', $ret);
            }
        }

        // if we encountered any errors we will return to the checkout page and show the errors
        if (!expQueue::isQueueEmpty('error')) {
            redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
//            $this->checkout();
        }

         // final the cart totals
        $order->calculateGrandTotal();
        $order->setOrderType($this->params);
        $order->setOrderStatus($this->params);
        //eDebug($order,true);

        // get the billing options..this is usually the credit card info entered by the user
        if ($billing->calculator != null) {
            if (isset($this->params['cc_type_' . $billing->calculator->calculator_name])) {
                $this->params['cc_type'] = $this->params['cc_type_' . $billing->calculator->calculator_name];
                unset($this->params['cc_type_' . $billing->calculator->calculator_name]);
            }
            $opts = $billing->calculator->userFormUpdate($this->params);
            //$billing->calculator->preprocess($this->params);
            //this should probably be generic-ized a bit more - currently assuming order_type parameter is present, or defaults
            //eDebug(order::getDefaultOrderType(),true);

            // call the billing method's preprocess in case it needs to prepare things.
            // eDebug($billing);
            $result = $billing->calculator->preprocess($billing->billingmethod, $opts, $this->params, $order);
        } else {  // no calculator, so we'll assume no cost checkout
            if (substr($this->params['cash_amount'], 0, strlen(expCore::getCurrencySymbol())) == expCore::getCurrencySymbol()) {
                $this->params['cash_amount'] = substr($this->params['cash_amount'], strlen(expCore::getCurrencySymbol()));
            }
            $opts = new stdClass();
            $opts->cash_amount = $this->params["cash_amount"];

            if ($opts->cash_amount < $order->grand_total) $opts->payment_due = $order->grand_total - $opts->cash_amount;
            $billing->billingmethod->update(array('billing_options' => serialize($opts)));
        }
        //eDebug($opts);
        expSession::set('billing_options', $opts);  //FIXME $opts is usually empty
        //$o = expSession::get('billing_options');
        //eDebug($o,true);
        //eDebug($this->params,true);

        // once in a while it appears the payment processor will return a nullo value in the errorCode field
        // which the previous check takes as a TRUE, as 0, null, and empty will all equate out the same using the ==
        // adding the === will specifically test for a 0 and only a 0, which is what we want

//        }

        if (empty($result->errorCode)) {  //if ($result->errorCode === "0" || $result->errorCode === 0)
			redirect_to(array('controller'=>'cart', 'action'=>'confirm'));
//            $this->confirm();
        } else {
            flash('error', gt('An error was encountered while processing your transaction.') . '<br /><br />' . $result->message);
            expHistory::back();
        }
    }

    public function confirm() {
        global $order;

        //eDebug($this->params);
        if (empty($order->orderitem)) flashAndFlow('error',gt('There are no items in your cart.'));

        // finalize the cart totals
        $order->calculateGrandTotal();

        //eDebug($order);
        // get the shipping and billing objects, these objects handle the setting up the billing/shipping methods
        // and their calculators
        $shipping = new shipping();
        $billing  = new billing();

        $opts = expSession::get('billing_options');
        //eDebug($opts,true);
//        if ($billing->calculator != null) {
//            $view_opts = $billing->calculator->userView($opts);
//        } else {
//            if (empty($opts)) {
//                $view_opts = false;
//            } else {
//                $billinginfo = gt("No Cost");
//                if (!empty($opts->payment_due)) {
//                    $billinginfo .= '<br>'.gt('Payment Due') . ': ' . expCore::getCurrencySymbol() . number_format($opts->payment_due, 2, ".", ",");
//                }
//                $view_opts = $billinginfo;
//            }
//        }
        assign_to_template(array(
            'shipping'   => $shipping,
            'billing'    => $billing,
            'order'      => $order,
            'total'      => $order->total,
            'billinginfo'=> $billing->getBillingInfo($opts),
        ));
    }

    public function process() {
//        global $db, $order, $user;
        global $order, $user;

        //eDebug($order,true);
        if (!$user->isLoggedIn() && empty($this->params['nologin'])) {
            flash('message', gt("It appears that your session has expired. Please log in to continue the checkout process."));
            expHistory::back();

            //expHistory::redirecto_login(makeLink(array('module'=>'cart','action'=>'checkout'), 'secure'));
        }
        // if this error hits then something went horribly wrong or the user has tried to hit this
        // action themselves before the cart was ready or is refreshing the page after they've confirmed the
        // order.
        if (empty($order->orderitem)) flash('error', gt('There are no items in your cart.'));
        if (!expQueue::isQueueEmpty('error')) redirect_to(array('controller'=> 'store', 'action'=> 'showall'));

        // set the gift comments
        $order->update($this->params);

        // save initial order status
        $change = new order_status_changes();
//        $change->from_status_id = null;
        $change->to_status_id   = $order->order_status_id;
        $change->orders_id      = $order->id;
        $change->save();

        // get the biling & shipping info
//        $shipping = new shipping();
        $billing  = new billing();

        // finalize the total to bill
        $order->calculateGrandTotal();
        //eDebug($order,true);
        $order->invoice_id = $order->getInvoiceNumber(false);  // assign the next invoice id, but don't advance it yet
        // call the billing calculators process method - this will handle saving the billing options to the database.
//        if (!($order->total == 0 && empty($order->shippingmethods))) {
        if ($billing->calculator != null) {
//            $result = $billing->calculator->process($billing->billingmethod, expSession::get('billing_options'), $this->params, $invNum);
            $result = $billing->calculator->process($billing->billingmethod, expSession::get('billing_options'), $this->params, $order);
        } else {
            // manually perform createBillingTransaction() normally done within billing calculator process()
            $opts = expSession::get('billing_options');
            $object = new stdClass();
            $object->errorCode = $opts->result->errorCode = 0;
            $opts->result->payment_status = gt("complete");
            if ($opts->cash_amount < $order->grand_total) $opts->result->payment_status = gt("payment due");
            $billing->billingmethod->update(array('billing_options' => serialize($opts),'transaction_state'=>$opts->result->payment_status));

//            $this->createBillingTransaction($billing->billingmethod, number_format($order->grand_total, 2, '.', ''), $opts->result, $opts->result->payment_status);
            $amount = number_format($order->grand_total, 2, '.', '');
            $bt = new billingtransaction();
            $bt->billingmethods_id = $billing->billingmethod->id;
            $bt->billingcalculator_id = $billing->billingmethod->billingcalculator_id;
            $bt->billing_cost = $amount;
            $bt->billing_options  = serialize($opts->result);
            $bt->extra_data = '';
            $bt->transaction_state = $opts->result->payment_status;
            $bt->save();
            $result = $opts;
        }
//        }

        if (empty($result->errorCode)) {
            // if ($result->errorCode === "0" || $result->errorCode === 0)
            // {
            // save out the cart total to the database
            $billing->billingmethod->update(array('billing_cost'=> $order->grand_total));

            // set the invoice number and purchase date in the order table..this finializes the order
            //$invoice_num = $db->max('orders', 'invoice_id') + 1;
            //if ($invoice_num < ecomconfig::getConfig('starting_invoice_number')) $invoice_num += ecomconfig::getConfig('starting_invoice_number');

            // get the first order status and set it for this order
            $invNum = $order->getInvoiceNumber();  // payment was processed so advance the invoice #
//            $order->update(array('invoice_id'=> $invNum, 'purchased'=> time(), 'updated'=> time(), 'comment'=> serialize($comment))); //FIXME $comment doesn't exist
            $order->update(array('invoice_id'=> $invNum, 'purchased'=> time(), 'updated'=> time()));
            //$order->setDefaultStatus(); --FJD?
            //$order->setDefaultOrderType(); --FJD?
            $order->refresh();

            // run each items process callback function
            foreach ($order->orderitem as $item) {
                $product = new $item->product_type($item->product_id);
                $product->process($item, $order->order_type->affects_inventory);
            }

            if ($billing->calculator != null) {
                $billing->calculator->postProcess($order, $this->params);
            }
            orderController::clearCartCookie();
        } else {
            flash('error', gt('An error was encountered while processing your transaction.') . '<br /><br />' . $result->message);
            expHistory::back();

            //redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
        }

//        if (!DEVELOPMENT) {
            // send email invoices to the admins & users if needed
        if ($order->order_type->emails_customer)
            $invoice = renderAction(array('controller'=> 'order', 'action'=> 'email', 'id'=> $order->id));
//        } elseif ($user->isAdmin()) {
//            flash('message', gt('Development on, skipping email sending.'));
//        }
        expSession::un_set('record');
        //assign_to_template(array('order'=>$order, 'billing'=>$billing, 'shipping'=>$shipping, 'result'=>$result, 'billinginfo'=>$billinginfo));
        flash('message', gt('Your order has been submitted.'));
        redirect_to(array('controller'=> 'order', 'action'=> 'myOrder', 'id'=> $order->id, 'tc'=> 1));
    }

    function quickPay() {
        global $order, $user;

        if ($order->shipping_required) redirect_to(array('controller'=> 'cart', 'action'=> 'checkout'),true);
        if (empty($order->orderitem)) flashAndFlow('error',gt('There are no items in your cart.'));

        // if we made it here it means that the item was add to the cart.
        expHistory::set('viewable', $this->params);

        // call each products checkout() callback & calculate total
        foreach ($order->orderitem as $item) {
            $product = new $item->product_type($item->product_id);
            $product->checkout();
        }

        // setup the billing & shipping calculators info
//        if ($product->requiresBilling) {
        if ($order->billing_required) {
            $billing = new billing();
            assign_to_template(array(
                'billing'=> $billing
            ));
        }

//        if ($product->requiresShipping) {
        if ($order->shipping_required) {  //FIXME we exit earlier if shipping_required???
            $shipping            = new shipping();
            $shipping->pricelist = $shipping->listPrices();
            assign_to_template(array(
                'shipping'=> $shipping
            ));
        }

        assign_to_template(array(
            'product'=> $product,
            'user'   => $user,
            'order'  => $order
        ));
    }

    function quickConfirm() {
        global $order, $user;

        if ($order->shipping_required || $order->grand_total) redirect_to(array('controller'=> 'cart', 'action'=> 'checkout'),true);
        if (empty($order->orderitem)) flashAndFlow('error',gt('There are no items in your cart.'));

        // if we made it here it means that the item was add to the cart.
        expHistory::set('viewable', $this->params);

        // call each products checkout() callback & calculate total
        foreach ($order->orderitem as $item) {
            $product = new $item->product_type($item->product_id);
            $product->checkout();
        }

        assign_to_template(array(
            'product'=> $product,
            'user'   => $user,
            'order'  => $order
        ));
    }

    function processQuickPay() {
        global $order, $template;

        // reuse the confirm action's template
        $tplvars = $template->tpl->tpl_vars;
        $template = expTemplate::get_template_for_action($this, 'confirm', $this->loc);
        $template->tpl->tpl_vars = array_merge($tplvars,$template->tpl->tpl_vars);

        if (!empty($this->params['billing'])) {
            $billing = new billing();
            $billing->billingmethod->setAddress($this->params['billing']);
        }

        if (!empty($this->params['shipping'])) {
            die('NEED TO IMPLEMENT THE SHIPPING PIECE!!'); //TODO
            $shipping = new shipping();
            $shipping->shippingingmethod->setAddress($this->params['shipping']);
            assign_to_template(array(
                'shipping'=> $shipping
            ));
        }

        $opts = $billing->calculator->userFormUpdate($this->params);
        $order->calculateGrandTotal();
        expSession::set('billing_options', $opts);  //FIXME $opts is usually empty
        assign_to_template(array(
            'billing'    => $billing,
            'order'      => $order,
            'total'      => $order->total,
//            'billinginfo'=> $billing->calculator->userView($opts),
            'billinginfo'=> $billing->getBillingInfo($opts),
            'nologin'    => 1
        ));
    }

    public function splitShipping() {
        global $user, $order;

        expHistory::set('viewable', $this->params);

        // get all the necessary addresses..shipping, billing, etc
        $address      = new address(null, false, false);
        $addresses_dd = $address->dropdownByUser($user->id);

        if (count($addresses_dd) < 2) {
            expHistory::set('viewable', $this->params);
            flash('error', gt('You must have more than 1 address to split your shipment.  Please add another now.'));
            redirect_to(array('controller'=> 'address', 'action'=> 'edit'));
        }

        // setup the list of orderitems
        $orderitems = array();
        foreach ($order->orderitem as $item) {
            if ($item->product->requiresShipping == true) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    $orderitems[] = $item;
                }
            }
        }

        if (count($orderitems) < 2) {
            flashAndFlow('error',gt('You must have a minimum of 2 items in your shopping cart in order to split your shipment.'));
        }

        expHistory::set('viewable', $this->params);
        assign_to_template(array(
            'addresses_dd'=> $addresses_dd,
            'orderitems'  => $orderitems,
            'order'       => $order
        ));
    }

    public function saveSplitShipping() {
        global $db;

        $addresses            = array();
        $orderitems_to_delete = '';

        foreach ($this->params['orderitems'] as $id=> $address_ids) {
            foreach ($address_ids as $address_id) {
                if (empty($addresses[$address_id][$id])) {
                    $addresses[$address_id][$id] = 1;
                } else {
                    $addresses[$address_id][$id]++;
                }
            }

            if (!empty($orderitems_to_delete)) $orderitems_to_delete .= ',';
            $orderitems_to_delete .= $id;
        }

        foreach ($addresses as $addy_id => $orderitems) {
            $shippingmethod = new shippingmethod();
            $shippingmethod->setAddress($addy_id);

            foreach ($orderitems as $orderitem_id => $qty) {
                $orderitem = new orderitem($orderitem_id);
                unset(
                    $orderitem->id,
                    $orderitem->shippingmethods_id
                );
                $orderitem->shippingmethods_id = $shippingmethod->id;
                $orderitem->quantity           = $qty;
                $orderitem->save();
            }
        }

        $db->delete('orderitems', 'id IN (' . $orderitems_to_delete . ')');
        redirect_to(array('controller'=>'cart', 'action'=>'selectShippingMethods'));
//        $this->selectShippingMethods();
    }

    public function selectShippingMethods() {
        global $order;

        expHistory::set('editable', $this->params);
        $shipping          = new shipping();
        $shippingmethod_id = $order->getShippingMethods();

        $shipping_items = array();
        foreach ($shippingmethod_id as $id) {
            $shipping_items[$id] = new order();
            $shipping_items[$id]->method    = new shippingmethod($id);
            $shipping_items[$id]->orderitem = $order->getOrderitemsByShippingmethod($id);
            foreach ($shipping_items[$id]->orderitem as $key=> $item) {
                if ($item->product->requiresShipping == false) {
                    unset($shipping_items[$id]->orderitem[$key]);
                }
            }

            if (empty($shipping_items[$id]->orderitem)) {
                unset($shipping_items[$id]);
            } else {
                foreach ($shipping->available_calculators as $calcid=> $name) {
                    if (class_exists($name)) {
                        $calc = new $name($calcid);
                        $shipping_items[$id]->prices[$calcid] = $calc->getRates($shipping_items[$id]);
                        //eDebug($shipping_items[$id]->prices[$id]);
                    }
                }
            }
        }

        assign_to_template(array(
            'shipping_items'=> $shipping_items,
            'shipping'      => $shipping
        ));
    }

    public function customerSignup() {
        if (expSession::get('customer-login')) expSession::un_set('customer-login');
        expSession::set('customer-signup', true);
        redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
//        $this->checkout();
    }

    public function saveShippingMethods() {
        global $order;

        $shipping               = new shipping();
        $order->shippingmethods = array();

        // if they didn't fill out anything
        if (empty($this->params['methods'])) {
            expValidator::failAndReturnToForm(gt("You did not pick any shipping options"), $this->params);
        }

        // if they don't check all the radio buttons
        if (count($this->params['methods']) < count($this->params['calcs'])) {
            expValidator::failAndReturnToForm(gt("You must select a shipping options for all of your packages."), $this->params);
        }

        foreach ($this->params['methods'] as $id=> $method) {
            $cost           = $this->params['cost'][$method];
            $title          = $this->params['title'][$method];
            $shippingmethod = new shippingmethod($id);
            $shippingmethod->update(array(
                'option'               => $method,
                'option_title'         => $title,
                'shipping_cost'        => $cost,
                'shippingcalculator_id'=> $this->params['calcs'][$id],
            ));

            $order->shippingmethods[] = $shippingmethod->id;
        }

        redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
//        $this->checkout();
    }

    function createaddress() {
//        global $db, $user;
        global $user;

        if ($user->isLoggedIn()) {
            // save the address, make it default if it is the users first one
            $address = new address();
            $count   = $address->find('count', 'user_id=' . $user->id);
            if ($count == 0) $this->params['is_default'] = 1;
            $this->params['user_id'] = $user->id;
            $address->update($this->params);

            // set the billing/shipping method
            if (isset($this->params['addresstype'])) {
                if ($this->params['addresstype'] == 'shipping') {
                    $shipping = new shipping();
                    $shipping->shippingmethod->setAddress($address);
                } elseif ($this->params['addresstype'] == 'billing') {
                    $billing = new billing();
                    $billing->billingmethod->setAddress($address);
                }
            }

        }

		redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
//        $this->checkout();
    }

    function getSpecials() {
        //STUB::flesh this function out eventually.
        return null;
    }

    // Discount Codes

    function isValidDiscountCode($code) {
        // is the code valid?
        if ($code == '12345') {
            # psudocode:
            # grab current order discounts
            # $discounts = new discountCode($order);
            # append the new discount code to the current codes
            # $discounts->appendCode($code);

            return true;
        } else {
            return false;
        }
    }

    /*function checkDiscount() {
         // handles what to do when a code valid or not
         if (isValidDiscountCode($this->params['discountcode'])) {
             flash('message', gt("Discount Code Applied"));
             redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
         } else {
             flash('error', gt("Sorry, the discount code provided is not a valid code."));
             redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
         }
     }   */

    function addDiscountToCart() {
//        global $user, $order;
        global $order;
        //lookup discount to see if it's real and valid, and not already in our cart
        //this will change once we allow more than one coupon code

        $discount = new discounts();
        $discount = $discount->getCouponByName(expString::escape($this->params['coupon_code']));

        if (empty($discount)) {
            flash('error', gt("This discount code you entered does not exist."));
            //redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
            expHistory::back();
        }

        //check to see if it's in our cart already
        if ($this->isDiscountInCart($discount->id)) {
            flash('error', gt("This discount code is already in your cart."));
            //redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
            expHistory::back();
        }

        //this should really be reworked, as it shoudn't redirect directly and not return
        $validateDiscountMessage = $discount->validateDiscount();
        if ($validateDiscountMessage == "") {
            //if all good, add to cart, otherwise it will have redirected
            $od               = new order_discounts();
            $od->orders_id    = $order->id;
            $od->discounts_id = $discount->id;
            $od->coupon_code  = $discount->coupon_code;
            $od->title        = $discount->title;
            $od->body         = $discount->body;
            $od->save();
            // set this to just the discount applied via this coupon?? if so, when though? $od->discount_total = ??;
            flash('message', gt("The discount code has been applied to your cart."));
        } else {
            flash('error', $validateDiscountMessage);
        }
        //redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
        expHistory::back();
    }

    function removeDiscountFromCart($id = null, $redirect = true) {
        //eDebug($params);
        if ($id == null) $id = $this->params['id'];
        $od = new order_discounts($id);
        $od->delete();
        flash('message', gt("The discount code has been removed from your cart"));
        if ($redirect == true) {
            //redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
            expHistory::back();
        }
    }

    function isDiscountInCart($discountId) {
        global $order;
        $cds = $order->getOrderDiscounts();
        if (count($cds) == 0) return false;

        foreach ($cds as $d) {
            if ($discountId == $d->discounts_id) return true;
        }
        return false;
    }

//    function configure() {
//        expHistory::set('editable', $this->params);
//        $this->loc->src = "@globalcartsettings";
//        $config         = new expConfig($this->loc);
//        $this->config   = $config->config;
//        assign_to_template(array(
//            'config'=> $this->config,
//            'title' => static::displayname()
//        ));
//    }

    //this is ran after we alter the quantity of the cart, including
    //delete items or runing the updatequantity action
    private function rebuildCart() {
        //group items by type and id
        //since we can have the same product in different items (options and quantity discount)
        //remove items and readd?
        global $order;
        //eDebug($order,true);
        $items = $order->orderitem;
        foreach ($order->orderitem as $item) {
            $item->delete();
        }
        $order->orderitem = array();
        $order->refresh();
        foreach ($items as $item) {

            for ($x = 1; $x <= $item->quantity; $x++) {
                $product   = $item->product;
                $price     = $product->getBasePrice();
                $basePrice = $price;
                $options   = array();
                if (!empty($item->opts)) {
                    foreach ($item->opts as $opt) {
                        $cost = $opt[2] == '$' ? $opt[4] : $basePrice * ($opt[4] * .01);
                        $cost = $opt[3] == '+' ? $cost : $cost * -1;
                        $price += $cost;
                        $options[] = $opt;
                    }
                }
                $params['options']        = serialize($options);
                $params['products_price'] = $price;
                $params['product_id']     = $product->id;
                $params['product_type']   = $product->product_type;

                $newitem = new orderitem($params);
                //eDebug($item, true);
                $newitem->products_price = $price;
                $newitem->options        = serialize($options);

                $sm                          = $order->getCurrentShippingMethod();
                $newitem->shippingmethods_id = $sm->id;
                $newitem->save();
                $order->refresh();
            }
        }
        $order->save();
        /*eDebug($items);


        $options = array();
        foreach ($this->optiongroup as $og) {
            if ($og->required && empty($params['options'][$og->id][0])) {

                flash('error', $this->title.' '.gt('requires some options to be selected before you can add it to your cart.'));
                redirect_to(array('controller'=>store, 'action'=>'show', 'id'=>$this->id));
            }
            if (!empty($params['options'][$og->id])) {
                foreach ($params['options'][$og->id] as $opt_id) {
                    $selected_option = new option($opt_id);
                    $cost = $selected_option->modtype == '$' ? $selected_option->amount :  $this->getBasePrice() * ($selected_option->amount * .01);
                    $cost = $selected_option->updown == '+' ? $cost : $cost * -1;
                    $price += $cost;
                    $options[] = array($selected_option->id,$selected_option->title,$selected_option->modtype,$selected_option->updown,$selected_option->amount);
                }
            }
        }
        //die();
        // add the product to the cart.
        $params['options'] = serialize($options);
        $params['products_price'] = $price;
        $item = new orderitem($params);
        //eDebug($item, true);
        $item->products_price = $price;
        $item->options = serialize($options);

        $sm = $order->getCurrentShippingMethod();
        $item->shippingmethods_id = $sm->id;
        $item->save();                            */
        return true;

    }

    public function empty_cart() {
        global $order;
        foreach ($order->orderitem as $orderItem) {
            $orderItem->delete();
        }
        flash('message', gt('Your shopping cart is now empty.'));
        expHistory::back();
    }

//    function saveconfig() {
//        // setup and save the config
//        $this->loc->mod = "cart";
//        $this->loc->src = "@globalcartsettings";
//        $this->loc->int = "";
//        parent::saveconfig();
//    }

    /**
     * get the metainfo for this module
     *
     * @return array
     */
    function metainfo() {
        global $router;

        if (empty($router->params['action'])) return false;

        // figure out what metadata to pass back based on the action we are in.
        $action = $router->params['action'];
        $metainfo = array('title' => '', 'keywords' => '', 'description' => '', 'canonical' => '', 'noindex' => true, 'nofollow' => true);
        $storename = ecomconfig::getConfig('storename');
        switch ($action) {
            default:
                $metainfo['title'] = gt("Shopping Cart") . " - " . $storename;
                $metainfo['keywords'] = SITE_KEYWORDS;
                $metainfo['description'] = SITE_DESCRIPTION;
//                $metainfo['canonical'] = URL_FULL.substr($router->sefPath, 1);
//                $metainfo['canonical'] = $router->plainPath();
        }

        return $metainfo;
    }

}

?>