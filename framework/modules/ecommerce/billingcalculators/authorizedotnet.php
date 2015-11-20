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
 * @subpackage Calculators
 * @package    Modules
 */

define('ECOM_AUTHORIZENET_AUTH_CAPTURE', 0);
define('ECOM_AUTHORIZENET_AUTH_ONLY', 1);

class authorizedotnet extends creditcard {

    const APPROVED = 1;
    const DECLINED = 2;
    const ERROR = 3;
    const HELD = 4;

    function name() {
        return gt("Authorize.net Payment Gateway");
    }

    public function captureEnabled() {
        return true;
    }

    public function voidEnabled() {
        return true;
    }

    public function creditEnabled() {
        return true;
    }

    function description() {
        return "Enabling this payment option will allow your customers to use their credit card to make purchases on your site.  It does require
	    an account with Authorize.net before you can use it to process credit cards.";
    }

//    function hasUserForm() {
//        return true;
//    }

    function isOffsite() {
        return false;
    }

    function isSelectable() {
        return true;
    }

    function process($billingmethod, $opts, $params, $order) {
        global $db, $user;

        $opts = expUnserialize($billingmethod->billing_options);  //FIXME why aren't we passing $opts?
        // make sure we have some billing options saved.
        if (empty($billingmethod) || empty($opts)) return false;

        // get a shipping address to display in the invoice email.
        $shippingaddress = $order->getCurrentShippingMethod();
        $shipping_state = new geoRegion($shippingaddress->state);
        $shipping_country = new geoCountry($shipping_state->country_id);

        $config = unserialize($this->config);

        $state = new geoRegion($billingmethod->state);
        $country = new geoCountry($state->country_id);

        $data = array(
            "x_login"              => $config['username'],
            "x_version"            => '3.1',
            "x_tran_key"           => $config['transaction_key'],
            //"x_password"=>$config['password'],
            "x_delim_data"         => 'TRUE',
            "x_delim_char"         => '|',
            "x_relay_response"     => 'FALSE',
            "x_first_name"         => $billingmethod->firstname,
            "x_last_name"          => $billingmethod->lastname,
            "x_address"            => $billingmethod->address1,
            "x_city"               => $billingmethod->city,
            "x_state"              => $state->code,
            "x_zip"                => $billingmethod->zip,
            "x_country"            => $country->iso_code_2letter,
            //"x_phone"=>empty($billingmethod->phone) ? '' : $billingmethod->phone,  //FIXME
            "x_phone"              => '309-680-5600',
            "x_email"              => $user->email,
            "x_invoice_num"        => $order->invoice_id,
            "x_ship_to_first_name" => $shippingaddress->firstname,
            "x_ship_to_last_name"  => $shippingaddress->lastname,
            "x_ship_to_address"    => $shippingaddress->address1,
            "x_ship_to_city"       => $shippingaddress->city,
            "x_ship_to_state"      => $shipping_state->code,
            "x_ship_to_zip"        => $shippingaddress->zip,
            "x_ship_to_country"    => $shipping_country->iso_code_2letter,
            "x_amount"             => $order->grand_total,
            "x_description"        => "Secure Order from " . HOSTNAME,
            "x_method"             => 'CC',
            "x_recurring_billing"  => 'NO',
            "x_card_num"           => $opts->cc_number,
            "x_exp_date"           => $opts->exp_month . '/' . $opts->exp_year,
            "x_card_code"          => $opts->cvv,
        );

        if (!empty($user->email) && $config['email_customer']) {
            $data['x_email_customer'] = 'TRUE';
        } else {
            $data['x_email_customer'] = 'FALSE';
        }

        if ($config['process_mode'] == ECOM_AUTHORIZENET_AUTH_CAPTURE) {
            $data['x_type'] = "AUTH_CAPTURE";
        } else if ($config['process_mode'] == ECOM_AUTHORIZENET_AUTH_ONLY) {
            $data['x_type'] = "AUTH_ONLY";
        }

        //Check if it is test mode and assign the proper url        
        if ($config['testmode']) {
            $url = "https://test.authorize.net/gateway/transact.dll";
            //$data["x_test_request"] = "TRUE"; 
            flash('message', gt('Authorize.net is in TEST Mode!'));

        } else {
            $url = "https://secure.authorize.net/gateway/transact.dll";
        }

        $data2 = "";
        while (list($key, $value) = each($data)) {
//			$data2 .= $key . '=' . urlencode(ereg_replace(',', '', $value)) . '&';
            $data2 .= $key . '=' . urlencode(str_ireplace(',', '', $value)) . '&';
        }

        // take the last & out for the string
        $data2 = substr($data2, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Windows 2003 Compatibility
        $authorize = curl_exec($ch);
        curl_close($ch);

        $response = explode("|", $authorize);
//        $response = $this->parseResponse($authorize, '|');

//        $object = new stdClass();
        if ($response[0] == 1) { //Approved !!!
            $opts->result->errorCode = 0;
            $opts->result->message = $response[3] . " Approval Code: " . $response[4];
            $opts->result->status = 'Approved';
            $opts->result->AUTHCODE = $response[4];
            $opts->result->AVSResponse = $response[5];
            $opts->result->HASH = $response[37];
            $opts->result->CVVResponse = $response[38];
            $opts->result->PNREF = $response[6];
//            $object->transactionID = $response[6];
            $opts->result->transId = $response[6];
            $opts->result->correlationID = $response[7];
            if ($config['process_mode'] == ECOM_AUTHORIZENET_AUTH_CAPTURE) {
                $trax_state = "complete";
                $billingcost = $order->grand_total;
            } else if ($config['process_mode'] == ECOM_AUTHORIZENET_AUTH_ONLY) {
                $trax_state = "authorized";
                $billingcost = 0;
            }
        } else {
            $opts->result->errorCode = $response[2]; //Response reason code
            $opts->result->message = $response[3];
            $trax_state = "error";
        }
        $opts->result->payment_status = $trax_state;

//        $opts->result = $object;
        $opts->cc_number = 'xxxx-xxxx-xxxx-' . substr($opts->cc_number, -4);
        $billingmethod->update(array('billing_options' => serialize($opts)));
        $this->createBillingTransaction($billingmethod, number_format($billingcost, 2, '.', ''), $opts->result, $trax_state);
        return $opts->result;
    }

    function credit_transaction($billingmethod, $amount, $order) {
        global $user;

        $config = unserialize($this->config);
        $opts = unserialize($billingmethod->billing_options);

        $data = array(
            'x_login'          => $config['username'],
            'x_tran_key'       => $config['transaction_key'],
            'x_type'           => 'VOID',
            'x_amount'         => $amount,
            'x_card_num'       => substr($opts->cc_number, -4),
//            'x_trans_id'       => urlencode($billing_options->result->transactionID),
            'x_trans_id'       => urlencode($opts->result->transId),
            'x_relay_response' => 'FALSE',
            'x_delim_data'     => 'TRUE',
            "x_delim_char"     => '|'
        );

        if (!empty($user->email) && $config['email_customer']) {
            $data['x_email_customer'] = 'TRUE';
        } else {
            $data['x_email_customer'] = 'FALSE';
        }

        //Check if it is test mode and assign the proper url        
        if ($config['testmode']) {
            $url = "https://test.authorize.net/gateway/transact.dll";
            //$data["x_test_request"] = "TRUE"; 
            flash('message', gt('Authorize.net is in TEST Mode!'));

        } else {
            $url = "https://secure.authorize.net/gateway/transact.dll";
        }

        $data2 = "";
        while (list($key, $value) = each($data)) {
            $data2 .= $key . '=' . urlencode(str_ireplace(',', '', $value)) . '&';
        }

        // take the last & out for the string
        $data2 = substr($data2, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Windows 2003 Compatibility
        $authorize = curl_exec($ch);
        curl_close($ch);

        $response = explode("|", $authorize);
        if ($response[2] == 1) { //if it is completed
            $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => 'voided'));
            $this->createBillingTransaction($billingmethod, urldecode($response[9]), $opts->result, 'voided');

            flash('message', gt('Void Completed Successfully.'));
            redirect_to(array('controller' => 'order', 'action' => 'show', 'id' => $billingmethod->orders_id));
        } else { // if it has error which like means it is already settled

            $data = array(
                'x_login'          => $config['username'],
                'x_tran_key'       => $config['transaction_key'],
                'x_type'           => 'CREDIT',
                'x_amount'         => $amount,
                'x_card_num'       => substr($opts->cc_number, -4),
//                'x_trans_id'       => urlencode($billing_options->result->transactionID),
                'x_trans_id'       => urlencode($opts->result->transId),
                'x_relay_response' => 'FALSE',
                'x_delim_data'     => 'TRUE',
                "x_delim_char"     => '|'
            );

            $data2 = "";
            while (list($key, $value) = each($data)) {
                $data2 .= $key . '=' . urlencode(str_ireplace(',', '', $value)) . '&';
            }

            // take the last & out for the string
            $data2 = substr($data2, 0, -1);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Windows 2003 Compatibility
            $authorize = curl_exec($ch);
            curl_close($ch);

            $response = explode("|", $authorize); //FIXME what to do with this?

            $opts->result->errorCode = 0;
            $opts->result->payment_status = gt("refunded");
            $opts->result->transId = '';
            $opts->result->message = "Transaction Refunded";
            $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => 'refunded'));
            $this->createBillingTransaction($billingmethod, -(number_format($amount, 2, '.', '')), $opts->result, 'refunded');

            flash('message', gt('Refund Completed Successfully.'));
            redirect_to(array('controller' => 'order', 'action' => 'show', 'id' => $billingmethod->orders_id));
        }
    }

	 function delayed_capture($billingmethod, $amount, $order) {
	
        global $user;

        $config = unserialize($this->config);
        $opts = unserialize($billingmethod->billing_options);

        $data = array(
            'x_login'          => $config['username'],
            'x_tran_key'       => $config['transaction_key'],
            'x_type'           => 'PRIOR_AUTH_CAPTURE',
				
            'x_amount'         => $amount,
            'x_card_num'       => substr($opts->cc_number, -4),
		//		'x_trans_id'       => $transaction_id,
//            'x_trans_id'       => urlencode($billing_options->result->transactionID),
            'x_trans_id'       => urlencode($opts->result->transId),
            'x_relay_response' => 'FALSE',
            'x_delim_data'     => 'TRUE',
            "x_delim_char"     => '|'
        );

        if (!empty($user->email) && $config['email_customer']) {
            $data['x_email_customer'] = 'TRUE';
        } else {
            $data['x_email_customer'] = 'FALSE';
        }

        //Check if it is test mode and assign the proper url        
        if ($config['testmode']) {
            $url = "https://test.authorize.net/gateway/transact.dll";
            //$data["x_test_request"] = "TRUE"; 
            flash('message', gt('Authorize.net is in TEST Mode!'));

        } else {
            $url = "https://secure.authorize.net/gateway/transact.dll";
        }

        $data2 = "";
        while (list($key, $value) = each($data)) {
            $data2 .= $key . '=' . urlencode(str_ireplace(',', '', $value)) . '&';
        }

        // take the last & out for the string
        $data2 = substr($data2, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Windows 2003 Compatibility
        $authorize = curl_exec($ch);
        curl_close($ch);

        $response = explode("|", $authorize); //FIXME what to do with this?

         $opts->result->errorCode = 0;
         $opts->result->payment_status = gt("complete");
         $opts->result->transId = '';
         $opts->result->message = "Transaction Captured";
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => 'complete'));
        $this->createBillingTransaction($billingmethod, number_format($amount, 2, '.', ''), $opts->result, 'complete');

        flash('message', gt('Captured Transaction Successfully.'));
        redirect_to(array('controller' => 'order', 'action' => 'show', 'id' => $billingmethod->orders_id));
     
    }

	function void_transaction($billingmethod, $order) {

        global $user;

        $amount = 0;  //FIXME initialize the amount??
        $config = unserialize($this->config);
        $opts = unserialize($billingmethod->billing_options);

        $data = array(
            'x_login'          => $config['username'],
            'x_tran_key'       => $config['transaction_key'],
            'x_type'           => 'Void',

            'x_amount'         => $amount,
            'x_card_num'       => substr($opts->cc_number, -4),
		//		'x_trans_id'       => $transaction_id,
//            'x_trans_id'       => urlencode($billing_options->result->transactionID),
            'x_trans_id'       => urlencode($opts->result->transId),
            'x_relay_response' => 'FALSE',
            'x_delim_data'     => 'TRUE',
            "x_delim_char"     => '|'
        );

        if (!empty($user->email) && $config['email_customer']) {
            $data['x_email_customer'] = 'TRUE';
        } else {
            $data['x_email_customer'] = 'FALSE';
        }

        //Check if it is test mode and assign the proper url        
        if ($config['testmode']) {
            $url = "https://test.authorize.net/gateway/transact.dll";
            //$data["x_test_request"] = "TRUE"; 
            flash('message', gt('Authorize.net is in TEST Mode!'));

        } else {
            $url = "https://secure.authorize.net/gateway/transact.dll";
        }

        $data2 = "";
        while (list($key, $value) = each($data)) {
            $data2 .= $key . '=' . urlencode(str_ireplace(',', '', $value)) . '&';
        }

        // take the last & out for the string
        $data2 = substr($data2, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Windows 2003 Compatibility
        $authorize = curl_exec($ch);
        curl_close($ch);

        $response = explode("|", $authorize); //FIXME what to do with this?

        $opts->result->traction_type = 'Void';
        $opts->result->errorCode = 0;
        $opts->result->payment_status = gt("voided");
        $opts->result->transId = '';
        $opts->result->message = "Transaction Voided";
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => 'voided'));
        $this->createBillingTransaction($billingmethod, number_format($amount, 2, '.', ''), $opts->result, 'voided');

        return $opts->result;

    }

    //Config Form
//    function configForm() {
//        $form = BASE . 'framework/modules/ecommerce/billingcalculators/views/authorizedotnet/configure.tpl';
//        return $form;
//    }

    //process config form
    function parseConfig($values) {
        $config_vars = array('username', 'transaction_key', 'password', 'testmode', 'accepted_cards', 'email_customer', 'email_admin', 'notification_addy', 'process_mode');
        foreach ($config_vars as $varname) {
            $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
        }

        return $config;
    }

    //This is called when a billing method is deleted. It can be used to clean up if you
    //have any custom user_data storage.
    function delete($where = '') {
        return;
    }

    //This should return html to display config settings on the view billing method page
    function view($config_object) {
        $html = "<br>" . gt("Settings") . ":<br/><hr>";
        $html .= "API Login ID: " . $config_object->username . "<br>";
        $html .= "Transaction Key: " . $config_object->transaction_key . "<br>";
        $html .= "Password: " . $config_object->password . "<br>";
        $html .= "Test Mode: " . (($config_object->test_mode) ? "Yes" : "No") . "<br>";
        $html .= "Process Mode: ";
        if ($config_object->process_mode == ECOM_AUTHORIZENET_AUTH_CAPTURE) {
            $html .= gt("Authorize and Capture") . "<br>";
        } else if ($config_object->process_mode == ECOM_AUTHORIZENET_AUTH_ONLY) {
            $html .= gt("Authorize Only") . "<br>";
        }
        $html .= "<br>" . gt("Accepted Cards") . ":<hr>";
        $html .= "American Express: " . (($config_object->accept_amex) ? "Yes" : "No") . "<br>";
        $html .= "Discover: " . (($config_object->accept_discover) ? "Yes" : "No") . "<br>";
        $html .= "Mastercard: " . (($config_object->accept_mastercard) ? "Yes" : "No") . "<br>";
        $html .= "Visa: " . (($config_object->accept_visa) ? "Yes" : "No") . "<br><br>";
        //$html .= "Offer Tax Exempt Field: ".(($config_object->offer_tax_exempt_field)?"Yes":"No")."<br>";

        return $html;
    }

    public function postProcess($order = null, $params = null) {
        $this->opts = null;
        return true;
    }

    function getPaymentAuthorizationNumber($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->AUTHCODE;
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
        return $ret->result->status;
    }

    function getAVSAddressVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        $response = $ret->result->AVSResponse;
        if (stristr($response, 'P') || stristr($response, 'S') || stristr($response, 'U')) return "N/A";
        elseif (stristr($response, 'A') || stristr($response, 'X') || stristr($response, 'Y')) return 'Y'; else return 'X';
    }

    function getAVSZipVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        $response = $ret->result->AVSResponse;
        if (stristr($response, 'P') || stristr($response, 'S') || stristr($response, 'U')) return "N/A";
        elseif (stristr($response, 'W') || stristr($response, 'X') || stristr($response, 'Y') || stristr($response, 'Z')) return 'Y'; else return 'X';
    }

    function getCVVMatched($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        $response = $ret->result->CVVResponse;
        if (stristr($response, 'M')) return 'Y';
        else return 'X';
    }

    function getPaymentMethod($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->cc_type;
    }

    /**
     * Parses an AuthorizeNet AIM Response.
     *
     * @param string $response The response from the AuthNet server.
     * @param string $delimiter The delimiter  (default is "|")
     * @return stdClass
     */
    public function parseResponse($response, $delimiter)
    {
        $return = new stdClass();

        if ($response) {

            // Split Array
            $response_array = explode($delimiter, $response);

            /**
             * If AuthorizeNet doesn't return a delimited response.
             */
            if (count($response_array) < 10) {
                $return->approved = false;
                $return->error = true;
                $return->error_message = "Unrecognized response from AuthorizeNet: $response";
                return $return;
            }

            // Set all fields
            $return->response_code        = $response_array[0];
            $return->response_subcode     = $response_array[1];
            $return->response_reason_code = $response_array[2];
            $return->response_reason_text = $response_array[3];
            $return->authorization_code   = $response_array[4];
            $return->avs_response         = $response_array[5];
            $return->transaction_id       = $response_array[6];
            $return->invoice_number       = $response_array[7];
            $return->description          = $response_array[8];
            $return->amount               = $response_array[9];
            $return->method               = $response_array[10];
            $return->transaction_type     = $response_array[11];
            $return->customer_id          = $response_array[12];
            $return->first_name           = $response_array[13];
            $return->last_name            = $response_array[14];
            $return->company              = $response_array[15];
            $return->address              = $response_array[16];
            $return->city                 = $response_array[17];
            $return->state                = $response_array[18];
            $return->zip_code             = $response_array[19];
            $return->country              = $response_array[20];
            $return->phone                = $response_array[21];
            $return->fax                  = $response_array[22];
            $return->email_address        = $response_array[23];
            $return->ship_to_first_name   = $response_array[24];
            $return->ship_to_last_name    = $response_array[25];
            $return->ship_to_company      = $response_array[26];
            $return->ship_to_address      = $response_array[27];
            $return->ship_to_city         = $response_array[28];
            $return->ship_to_state        = $response_array[29];
            $return->ship_to_zip_code     = $response_array[30];
            $return->ship_to_country      = $response_array[31];
            $return->tax                  = $response_array[32];
            $return->duty                 = $response_array[33];
            $return->freight              = $response_array[34];
            $return->tax_exempt           = $response_array[35];
            $return->purchase_order_number= $response_array[36];
            $return->md5_hash             = $response_array[37];
            $return->card_code_response   = $response_array[38];
            $return->cavv_response        = $response_array[39];
            $return->account_number       = $response_array[50];
            $return->card_type            = $response_array[51];
            $return->split_tender_id      = $response_array[52];
            $return->requested_amount     = $response_array[53];
            $return->balance_on_card      = $response_array[54];

            $return->approved = ($return->response_code == self::APPROVED);
            $return->declined = ($return->response_code == self::DECLINED);
            $return->error    = ($return->response_code == self::ERROR);
            $return->held     = ($return->response_code == self::HELD);

            if ($return->error) {
                $return->error_message = "AuthorizeNet Error:
                Response Code: ".$return->response_code."
                Response Subcode: ".$return->response_subcode."
                Response Reason Code: ".$return->response_reason_code."
                Response Reason Text: ".$return->response_reason_text."
                ";
            }
        } else {
            $return->approved = false;
            $return->error = true;
            $return->error_message = "Error connecting to AuthorizeNet";
        }

        return $return;
    }

}

?>