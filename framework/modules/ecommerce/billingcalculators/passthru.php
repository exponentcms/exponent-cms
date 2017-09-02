<?php
##################################################
#
# Copyright (c) 2004-2017 OIC Group, Inc.
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
 * @package    Modules
 */
/** @define "BASE" "../../../.." */

class passthru extends billingcalculator {

    function name() {
        return gt("Passthru Payment");
    }

//    public $use_title = 'Pass-Thru';
    public $payment_type = 'Passthru';

    function description() {
        return gt("Enabling this payment option will allow you or your customers to bypass payment processing at the cart and allow payment methods after the order is processed, such as cash, check, pay in store, or manually process via credit card.") . "<br>** " . gt("This is a restricted payment option and only accessible by site admins.");
    }

    function hasConfig() {
        return false;
    }

    function hasUserForm() {
        return false;
    }

    function isRestricted() {
        return true;
    }

    //Called for billing medthod seletion screen, return true if it's a valid billing method.
//    function pre_process($config_object, $order, $billaddress, $shippingaddress) {
//        return true;
//    }

//    function post_process() {
//        return true;
//    }

    //Config Form
//    function form($config_object) {
//        $form = new form();
//        if (!$config_object) {
//            $config_object->give_change = true;
//        }
//        $form->register("give_change", gt("Give Change?"), new checkboxcontrol($config_object->give_change));
//        $form->register("submit", "", new buttongroupcontrol("Save", "", "Cancel"));
//        return $form->toHTML();
//    }

    //process config form
//	function update($values, $config_object) {  //FIXME doesn't match parent declaration update($params = array())
//		$config_object->give_change = $values['give_change'];
//		return $config_object;
//	}

    //Form for user input
    function userForm($config_object = null, $user_data = null) {
        $form = new form();
        $htmlinfo = gt("You may place your order and pay with a check or money order.  If paying by check, your order will be held util we receive the check and it clears our bank account.  Money order orders will be processed upon our receipt of the money order.") . "<br/><br/>";
        $form->register(uniqid(""), "", new htmlcontrol($htmlinfo));
        $form->register("cash_amount", gt("Cash Amount:"), new textcontrol());
        return $form->toHTML();
    }

    //process user input. This function should return an object of the user input.  //FIXME never used
    //the returnd object will be saved in the session and passed to post_process.
    //If need be this could use another method of data storage, as long post_process can get the data.
    function userProcess($values, $config_object, $user_data) {
        $user_data->cash_amount = $values["cash_amount"];
        return $user_data;
    }

    //This is called when a billing method is deleted. It can be used to clean up if you
    //have any custom user_data storage.
    function delete($where = '') {
        return;
    }

    //This should return html to display config settings on the view billing method page
    function view($config_object) {
        //add restrictions config stuff here
        return '';
    }

    //Should return html to display user data.
    function userView($billingmethod) {
        $opts = expUnserialize($billingmethod->billing_options);
           //eDebug($opts,true);
        if (isset($opts->result)) return '';
        $ot = new order_type($opts->order_type);
        $os = new order_status($opts->order_status);
        if (!empty($opts->sales_rep_1_id)) $sr1 = new sales_rep($opts->sales_rep_1_id);
        if (!empty($opts->sales_rep_2_id)) $sr2 = new sales_rep($opts->sales_rep_2_id);
        if (!empty($opts->sales_rep_3_id)) $sr3 = new sales_rep($opts->sales_rep_3_id);
        $msg = gt('Order Type') . ': ' . $ot->title;
        $msg .= '<br>' . gt('Order Status') . ': ' . $os->title;
        if (!empty($sr1)) $msg .= '<br>' . gt('Sales Rep 1') . ': ' . $sr1->initials;
        if (!empty($sr2)) $msg .= '<br>' . gt('Sales Rep 2') . ': ' . $sr2->initials;
        if (!empty($sr3)) $msg .= '<br>' . gt('Sales Rep 3') . ': ' . $sr3->initials;
        //$order
        return $msg;
    }

    function userFormUpdate($params) {
        //eDebug($params,true);
        //eturn array('order_type'=>$params['order_type'],'order_status'=>$params['order_status'],'sales_rep_1_id'=>$params['sales_rep_1_id'],'sales_rep_2_id'=>$params['sales_rep_2_id'],'sales_rep_3_id'=>$params['sales_rep_3_id']);
        $obj = new stdClass();
        $obj->order_type = $params['order_type'];
        $obj->order_status = $params['order_status'];
        if (isset($params['sales_rep_1_id'])) $obj->sales_rep_1_id = $params['sales_rep_1_id'];
        if (isset($params['sales_rep_2_id'])) $obj->sales_rep_2_id = $params['sales_rep_2_id'];
        if (isset($params['sales_rep_2_id'])) $obj->sales_rep_3_id = $params['sales_rep_3_id'];
        return $obj;
    }

    function preprocess($billingmethod, $opts, $params, $order) {
        $billingmethod->update(array('billing_options' => serialize($opts)));
        if (isset($params['sales_rep_1_id'])) $order->sales_rep_1_id = $params['sales_rep_1_id'];
        if (isset($params['sales_rep_2_id'])) $order->sales_rep_2_id = $params['sales_rep_2_id'];
        if (isset($params['sales_rep_3_id'])) $order->sales_rep_3_id = $params['sales_rep_3_id'];
        $order->save();
        /* eDebug($billingmethod);
         eDebug($opts);
         eDebug($params,true); */
        return true;
    }

//    function process($billingmethod, $opts, $params, $invoice_number) {
    function process($billingmethod, $opts, $params, $order) {
        $opts = expUnserialize($billingmethod->billing_options);  //FIXME why aren't we passing $opts?
        $opts->result->errorCode = 0;
        $opts->result->message = 'Authorization pending.';
        $opts->result->PNREF = 'Pending';
        $opts->result->authorization_code = 'Pending';
        $opts->result->AVSADDR = 'Pending';
        $opts->result->AVSZIP = 'Pending';
        $opts->result->CVV2MATCH = 'Pending';
        $opts->result->traction_type = 'Pending';
        $trax_state = "authorization pending";
        $trax_state->payment_status = $trax_state;

        //$opts2->billing_info = $opts;
//        $opts2 = new stdClass();
//        $opts2->result = $object;
        //eDebug($opts,true);
        /*$opts->result = $object;
        $opts->cc_number = 'xxxx-xxxx-xxxx-'.substr($opts->cc_number, -4);*/
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));
        $this->createBillingTransaction($billingmethod, number_format(0, 2, '.', ''), $opts->result, $trax_state);
        return $opts->result;
    }

    function postProcess($order, $params) {
        //check order types and create new user if necessary
        global $db, $user;

        $ot = new order_type($order->order_type_id);
        if ($ot->creates_new_user == true) {
            $addy = new address($order->billingmethod[0]->addresses_id);
            $newUser = new user();
            $newUser->username = $addy->email . time(); //make a unique username
//            $password = md5(time() . mt_rand(50, 1000)); //generate random password
            $password = expValidator::generatePassword(); //generate random password
            $newUser->setPassword($password, $password);
            $newUser->email = $addy->email;
            $newUser->firstname = $addy->firstname;
            $newUser->lastname = $addy->lastname;
            $newUser->is_system_user = false;
            $newUser->save(true);
            $newUser->refresh();
            $addy->user_id = $newUser->id;
            $addy->is_default = true;
            $addy->save();
            $order->user_id = $newUser->id;
            $order->save();

            if ($order->orderitem[0]->shippingmethod->addresses_id != $addy->id) {
                $addy = new address($order->orderitem[0]->shippingmethod->addresses_id);
                $addy->user_id = $newUser->id;
                $addy->is_default = false;
                $addy->save();
            }

            //make sure current user is good to go
            $defAddy = $addy->find('first', 'user_id=' . $user->id);
            $obj = new stdClass();
            $obj->id = $defAddy->id;
            $db->setUniqueFlag($obj, 'addresses', 'is_default', 'user_id=' . $user->id);
            $db->setUniqueFlag($obj, 'addresses', 'is_shipping', 'user_id=' . $user->id);
            $db->setUniqueFlag($obj, 'addresses', 'is_billing', 'user_id=' . $user->id);
        }
        return true;
    }

    function getPaymentAuthorizationNumber($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        //eDebug($ret);
        return $ret->result->authorization_code;
    }

    function getPaymentReferenceNumber($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        if (isset($ret->result)) {
            return $ret->result->PNREF;
        } else {
            return $ret->PNREF;
        }
    }

    function getPaymentStatus($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->message;
    }

    function getAVSAddressVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->AVSADDR;
    }

    function getAVSZipVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->AVSZIP;
    }

    function getCVVMatched($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->CVV2MATCH;
    }

    function getPaymentMethod($billingmethod) {
        //$ret = expUnserialize($billingmethod->billing_options);
        return 'Manual';
    }

}

?>