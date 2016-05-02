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
 * @subpackage Calculators
 * @package    Modules
 */

class splitcreditcard extends creditcard {

    function description() {
        return gt("Enabling this payment option will allow your customers to use their credit card to make purchases on your site.  The credit card number
	        will be split with part of it being stored in the database and the other part getting emailed to site administrator.");
    }

    function isSelectable() {
        return true;
    }

//    public function captureEnabled() {
//        return true;
//    }

//    public function voidEnabled() {
//        return true;
//    }

//    public function creditEnabled() {
//        return true;
//    }

    //TODO: I don't think this is used any more but i don't have a clue
    //Called for billing method selection screen, return true if it's a valid billing method.
//    function pre_process() {
//        return true;
//    }

    //called when the order is submitted. Should return an object...
    function process($billingmethod, $opts, $params, $order) {
        $opts = expUnserialize($billingmethod->billing_options);  //FIXME why aren't we passing $opts?

        // make sure we have some billing options saved.
        if (empty($opts))
            return false;

        //FIXME this is where we lose the split credit card data
		// get the configuration data
		$config = unserialize($this->config);

		$txtmessage = gt("The following order requires your attention") . "\r\n\r\n";
        $txtmessage .= $this->textmessage($opts);

		$htmlmessage = gt("The following order requires your attention") . "<br><br>";
		$htmlmessage .= $this->htmlmessage($opts);

		$addresses = explode(',', $config['notification_addy']);
        $from = array(ecomconfig::getConfig('from_address') => ecomconfig::getConfig('from_name'));
        if (empty($from[0]))
            $from = array(SMTP_FROMADDRESS => ecomconfig::getConfig('storename'));
        foreach ($addresses as $address) {
		    $mail = new expMail();
		    $mail->quickSend(array(
                'html_message'=>$htmlmessage,
                'text_message'=>$txtmessage,
                'to'=>trim($address),
                'from'=>$from,
                'subject'=>gt('Billing Information for an order placed on') . ' '.ecomconfig::getConfig('storename'),
		    ));
		}
        $opts->cc_number = 'xxxx-xxxx-xxxx-' . substr($opts->cc_number, -4);

//        $object = new stdClass();
//        $object->errorCode = $opts->result->errorCode = 0;
//        $object->payment_status = 'complete';
//        $this->opts->result = $object;
        $opts->result->errorCode = 0;
        $opts->result->payment_status = gt("authorization pending");
        $opts->result->message = "User selected a credit card";
//        $opts->result->token = '';
        $opts->result->transId = '';
//        $billingmethod->update(array('billing_options' => serialize($this->opts), 'transaction_state' => "Pending"));
//        $billingmethod->update(array('billing_options' => serialize($this->opts), 'transaction_state' => "complete"));
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $opts->result->payment_status));
//        $this->createBillingTransaction($billingmethod, number_format($order->grand_total, 2, '.', ''), $this->opts->result, "complete");
        $this->createBillingTransaction($billingmethod, number_format(0, 2, '.', ''), $opts->result, $opts->result->payment_status);
        return $opts->result;
    }

//    function postProcess($order, $params) {
//        return true;
//    }

    //Form for user input
//    function userForm() {
//        return parent::userForm();
//    }

    //process user input. This function should return an object of the user input.
    //the returnd object will be saved in the session and passed to post_process.
    //If need be this could use another method of data storage, as long post_process can get the data.
//    function userFormUpdate($params) {
//        return parent::userFormUpdate($params);
//    }

    //Should return html to display user data.
//    function userView($opts) {
//        return parent::userView($opts);
//    }

    //Config Form
//    function configForm() {
//        $form = BASE . 'framework/modules/ecommerce/billingcalculators/views/splitcreditcard/configure.tpl';
//        return $form;
//    }

    //process config form
    function parseConfig($values) {
        $config_vars = array('accepted_cards', 'email_customer', 'email_admin', 'notification_addy');
        foreach ($config_vars as $varname) {
            $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
        }

        return $config;
    }

    //process config form
//    function update($params = array()) {
//        /*$config_object->email_contact = $values['email_contact'];
//        $config_object->email_subject = $values['email_subject'];
//        $config_object->email_intro = $values['email_intro'];
//
//        $config_object->accept_amex = (isset($values['accept_amex']) ? 1 : 0);
//        $config_object->accept_discover = (isset($values['accept_discover']) ? 1 : 0);
//        $config_object->accept_mastercard = (isset($values['accept_mastercard']) ? 1 : 0);
//        $config_object->accept_visa = (isset($values['accept_visa']) ? 1 : 0);
//        $config_object->email_customer = isset($values['email_customer']);
//        $config_object->email_customer_invoice = isset($values['email_customer_invoice']);
//        $config_object->email_other_invoice = $values['email_other_invoice'];
//        $config_object->email_customer_status_change = isset($values['email_customer_status_change']);
//        $config_object->email_other_status_change = $values['email_other_status_change'];
//        return $config_object;*/
//    }

    //This is called when a billing method is deleted. It can be used to clean up if you
    //have any custom user_data storage.
//    function delete($where = '') {
//        return;
//    }

    //This should return html to display config settings on the view billing method page
    function view($config_object) {
        /*$html .= '<br />Settings:<hr>';
        $html .= 'Email Contact: ' . $config_object->email_contact.'<br />';
        $html .= 'Subject of Message: '. $config_object->email_subject.'<br />';
        $html .= 'Message: '.$config_object->email_message.'<br />';
        $html .= '<br />Accepted Cards:<hr>';
        $html .= 'American Express: '.(($config_object->accept_amex)?'Yes':'No').'<br />';
        $html .= 'Discover: '.(($config_object->accept_discover)?'Yes':'No').'<br />';
        $html .= 'Mastercard: '.(($config_object->accept_mastercard)?'Yes':'No').'<br />';
        $html .= 'Visa: '.(($config_object->accept_visa)?'Yes':'No').'<br />';
        return $html;*/
    }

    /**
     * Unused at this point
     *
     * @param $opts
     * @return string
     */
    function textmessage($opts) {
        global $order;   //FIXME we do NOT want the global $order

        $message = gt('Order Number') . ": $order->invoice_id\r\n";
//        $message .= gt('Credit Card Type') . ': ' . $this->cards[$opts->cc_type] . "\r\n";
        $message .= gt('Credit Card Number') . ': ' . substr(self::formatCreditCard($opts->cc_number, $opts->cc_type), 0, -4) . 'xxxx' . "\r\n";
        $message .= gt('Credit Card CVV Number') . ': ' . $opts->cvv . "\r\n";
        $message .= gt('Expires on') . ': ' . $opts->exp_month . '/' . $opts->exp_year . "\r\n";
        return $message;
    }

    /**
     * Unused at this point
     *
     * @param $opts
     * @return string
     */
    function htmlmessage($opts) {
        global $order;   //FIXME we do NOT want the global $order

        $message = gt("Order Number") . ": $order->invoice_id<br>";
//        $message .= gt('Credit Card Type') . ': ' . $this->cards[$opts->cc_type] . "<br>";
        $message .= gt('Credit Card Number') . ': ' . substr(self::formatCreditCard($opts->cc_number, $opts->cc_type), 0, -4) . 'xxxx' . "<br>";
        $message .= gt('Credit Card CVV Number') . ': ' . $opts->cvv . "<br>";
        $message .= gt('Expires on') . ': ' . $opts->exp_month . '/' . $opts->exp_year . "<br>";
        return $message;
    }

//    function getPaymentAuthorizationNumber($billingmethod) {
//        $ret = expUnserialize($billingmethod->billing_options);
//        return $ret->result->token;  //FIXME we don't store a 'token'
//    }

    function getPaymentReferenceNumber($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        if (isset($ret->result)) {
            return $ret->result->transId;
        } else {
            return $ret->transId;
        }
    }

    function getPaymentStatus($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->payment_status;
    }

    function getAVSAddressVerified($billingmethod) {
        return 'X';
    }

    function getPaymentMethod($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $this->cards[$ret->cc_type] . ' - ' . substr($ret->cc_number, -4);
    }

    function getAVSZipVerified($billingmethod) {
        return 'X';
    }

    function getCVVMatched($billingmethod) {
        return 'X';
    }

}

?>