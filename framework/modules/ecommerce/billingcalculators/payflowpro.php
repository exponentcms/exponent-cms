<?php
##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
class payflowpro extends creditcard {

    function name() {
        return gt("PayPal Payflow Payment Gateway");
    }

//    public $use_title = 'PayPal Payflow Payment Gateway';
    public $payment_type = 'PayPal Payflow';

    function description() {
        return gt("Enabling this payment option will allow your customers to use their credit card to make purchases on your site.  It requires a PayPal Payflow Merchant Account before you can use it to process credit cards.");
    }

//    function hasConfig() {
//        return true;
//    }

//    function hasUserForm() {
//        return true;
//    }

    function isOffsite() {
        return false;
    }

    function isSelectable() {
        return true;
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

    /*function preprocess($billingmethod, $opts, $params)
    {

    } */

//    function process($billingmethod, $opts, $params, $invoice_number) {
    function process($billingmethod, $opts, $params, $order) {
        $opts = expUnserialize($billingmethod->billing_options);  //FIXME why aren't we passing $opts?
        $config = unserialize($this->config);
        //eDebug($config,true);
        switch ($config['process_mode']) {
            case 'S':
                $result = $this->sale_transaction($billingmethod, $opts, $order);
                break;

            case 'A':
                $result = $this->authorization($billingmethod, $opts, $order);
                break;

            // The following are meant to be called directly not necessarily via the process but they are here for completeness sake.
            case 'D':
                $result = $this->delayed_capture($billingmethod, $opts, $order);
                break;

            case 'V':
//                $result = $this->void_transaction($billingmethod, $opts);
                $result = $this->void_transaction($billingmethod, $opts, $order);
                break;

            case 'C':
                $result = $this->credit_transaction($billingmethod, $opts, $order);
                break;
        }

        return $result;

    }

    // sale
    function sale_transaction($billingmethod, $opts, $order) {
//        global $order, $db, $user;

        // make sure we have some billing options saved.
        if (empty($billingmethod) || empty($opts)) return false;
        if ($order->grand_total <= 0) return false;

        // get a shipping address to display in the invoice email.
        $shippingaddress = $order->getCurrentShippingMethod();
        $shipping_state = new geoRegion($shippingaddress->state);
        $shipping_country = new geoCountry($shipping_state->country_id);

        $config = unserialize($this->config);

        $state = new geoRegion($billingmethod->state);
        $country = new geoCountry($state->country_id);

        // set the api endpoint url depending on test mode setting
        if ($config['testmode'] == 1) {
            $submiturl = 'https://pilot-payflowpro.paypal.com';
            flash('message', gt('This Transaction is in TEST MODE'));
        } else {
            $submiturl = 'https://payflowpro.paypal.com';
        }

        $apiParams = array(
            'USER'      => (empty($config['user'])) ? $config['vendor'] : $config['user'],
            'VENDOR'    => $config['vendor'],
            'PARTNER'   => $config['partner'],
            'PWD'       => $config['password'],
            'VERBOSITY' => 'MEDIUM',
            'TENDER'    => 'C', // C = credit card, P = PayPal
            'TRXTYPE'   => 'S', // S = Sale transaction, A = Authorisation, C = Credit, D = Delayed Capture, V = Void
            'ACCT'      => $opts->cc_number,
            'EXPDATE'   => $opts->exp_month . substr($opts->exp_year, 2, 2),
            'NAME'      => $billingmethod->firstname . $billingmethod->lastname,
            'AMT'       => number_format($order->grand_total, 2, '.', ''),

//            'CURRENCY'  =>  'USD',
            'CURRENCY'  => ECOM_CURRENCY,
            'FIRSTNAME' => $billingmethod->firstname,
            'LASTNAME'  => $billingmethod->lastname,
            'STREET'    => $billingmethod->address1,
            'CITY'      => $billingmethod->city,
            'STATE'     => $state->code,
            'ZIP'       => $billingmethod->zip,
            'COUNTRY'   => $country->iso_code_2letter,
            'CLIENTIP'  => $this->getRealIP(),

            'COMMENT1'  => 'Sale Transaction',
            //'COMMENT2'  =>  '',
        );

        if (isset($opts->cvv)) {
            $apiParams['CVV'] = $opts->cvv;
        }

        // convert the api params to a name value pair string
        $nvpstr = "";
//        while (list($key, $value) = each($apiParams)) {
        foreach($apiParams as $key=>$value) {
            $tmpVal = urlencode(preg_replace('/,/', '', $value));
            $nvpstr .= $key . '[' . strlen($tmpVal) . ']=' . $tmpVal . '&';
        }

        // take the last & out for the string
        $nvpstr = substr($nvpstr, 0, -1);

        // build hash
        $request_id = md5($opts->cc_number . $order->grand_total . time() . "1");

        $headers[] = "X-VPS-Request-ID: " . $request_id;
        $headers[] = "Content-Type: text/namevalue "; //or maybe text/xml
        $headers[] = "X-VPS-Timeout: 30";
        $headers[] = "X-VPS-VIT-OS-Name: Linux"; // Name of your OS
        $headers[] = "X-VPS-VIT-OS-Version: CentOS"; // OS Version
        $headers[] = "X-VPS-VIT-Client-Type: PHP/cURL"; // What you are using
        $headers[] = "X-VPS-VIT-Client-Version: 0.01"; // For your info
        $headers[] = "X-VPS-VIT-Client-Architecture: x86"; // For your info
        $headers[] = "X-VPS-VIT-Integration-Product: ExponentCMS"; // For your info, would populate with application name
        $headers[] = "X-VPS-VIT-Integration-Version: 2.0"; // Application version

        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $submiturl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr); //adding POST data
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //verifies ssl certificate
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true); //forces closure of connection when done
        curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST

        $result = curl_exec($ch);

        $headers = curl_getinfo($ch);
        curl_close($ch);

        $response = $this->parseResponse($result); //result array

        $trax_state = '';
//        $object = new stdClass();
        $opts->result->errorCode = -1; //if totally fails, this doesn't get set and passes through
        $opts->result->message = "Transaction failed. Error #-1";
        if (isset($response['RESULT']) && $response['RESULT'] == 0) // Approved !!!
        {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            $opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->CVV2MATCH = $response['CVV2MATCH'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];
            $opts->result->traction_type = 'Sale';
            $trax_state = "complete";
        } else {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            $opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->CVV2MATCH = $response['CVV2MATCH'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];
            $opts->result->traction_type = 'Sale';
            $trax_state = "error";
        }

        $opts->result->payment_status = $trax_state;
//        $opts->result = $object;
        $opts->cc_number = 'xxxx-xxxx-xxxx-' . substr($opts->cc_number, -4);
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));
        $this->createBillingTransaction($billingmethod, number_format($order->grand_total, 2, '.', ''), $opts->result, $trax_state);
        return $opts->result;
    }

    // Authorization
    function authorization($billingmethod, $opts, $order) {
//        global $order, $db, $user;

        // make sure we have some billing options saved.
        if (empty($billingmethod) || empty($opts)) return false;
        if ($order->grand_total <= 0) return false;

        // get a shipping address to display in the invoice email.
        $shippingaddress = $order->getCurrentShippingMethod();
        $shipping_state = new geoRegion($shippingaddress->state);
        $shipping_country = new geoCountry($shipping_state->country_id);

        $config = unserialize($this->config);

        $state = new geoRegion($billingmethod->state);
        $country = new geoCountry($state->country_id);

        // set the api endpoint url depending on test mode setting
        if ($config['testmode'] == 1) {
            $submiturl = 'https://pilot-payflowpro.paypal.com';
            flash('message', gt('This Transaction is in TEST MODE'));
        } else {
            $submiturl = 'https://payflowpro.paypal.com';
        }

        $apiParams = array(
            'USER'      => (empty($config['user'])) ? $config['vendor'] : $config['user'],
            'VENDOR'    => $config['vendor'],
            'PARTNER'   => $config['partner'],
            'PWD'       => $config['password'],
            'VERBOSITY' => 'MEDIUM',
            'TENDER'    => 'C', // C = credit card, P = PayPal
            'TRXTYPE'   => 'A', // S = Sale transaction, A = Authorization, C = Credit, D = Delayed Capture, V = Void
            'ACCT'      => $opts->cc_number,
            'EXPDATE'   => $opts->exp_month . substr($opts->exp_year, 2, 2),
            'NAME'      => $billingmethod->firstname . $billingmethod->lastname,
            'AMT'       => number_format($order->grand_total, 2, '.', ''),

//            'CURRENCY'  =>  'USD',
            'CURRENCY'  => ECOM_CURRENCY,
            'FIRSTNAME' => $billingmethod->firstname,
            'LASTNAME'  => $billingmethod->lastname,
            'STREET'    => $billingmethod->address1,
            'CITY'      => $billingmethod->city,
            'STATE'     => $state->code,
            'ZIP'       => $billingmethod->zip,
            'COUNTRY'   => $country->iso_code_2letter,
            'CLIENTIP'  => $this->getRealIP(),

            'COMMENT1'  => 'Authorization',
            'COMMENT2'  => '',
        );

        if (isset($opts->cvv)) {
            $apiParams['CVV2'] = $opts->cvv;
        }

        // convert the api params to a name value pair string
        $nvpstr = "";
//        while (list($key, $value) = each($apiParams)) {
        foreach($apiParams as $key=>$value) {
            $tmpVal = urlencode(str_replace(',', '', $value));
            $nvpstr .= $key . '[' . strlen($tmpVal) . ']=' . $tmpVal . '&';
            //$nvpstr .= $key . '=' . $tmpVal . '&';
        }

        // take the last & out for the string
        $nvpstr = substr($nvpstr, 0, -1);

        // build hash
        $request_id = md5($opts->cc_number . $order->grand_total . time() . "1");

        $headers[] = "X-VPS-Request-ID: " . $request_id;
        $headers[] = "Content-Type: text/namevalue "; //or maybe text/xml
        $headers[] = "X-VPS-Timeout: 30";
        $headers[] = "X-VPS-VIT-OS-Name: Linux"; // Name of your OS
        $headers[] = "X-VPS-VIT-OS-Version: CentOS"; // OS Version
        $headers[] = "X-VPS-VIT-Client-Type: PHP/cURL"; // What you are using
        $headers[] = "X-VPS-VIT-Client-Version: 0.01"; // For your info
        $headers[] = "X-VPS-VIT-Client-Architecture: x86"; // For your info
        $headers[] = "X-VPS-VIT-Integration-Product: ExponentCMS"; // For your info, would populate with application name
        $headers[] = "X-VPS-VIT-Integration-Version: 2.0"; // Application version

        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $submiturl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr); //adding POST data
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //verifies ssl certificate
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true); //forces closure of connection when done
        curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST

        /*eDebug($config);
        eDebug($nvpstr);
        eDebug($ch);    */
        $result = curl_exec($ch);

        $headers = curl_getinfo($ch);
        curl_close($ch);
        //echo "Here";
        //eDebug($result);

        $response = $this->parseResponse($result); //result array

        //eDebug($response,true);
        $trax_state = '';
//        $object = new stdClass();
        $opts->result->errorCode = -1; //if totally fails, this doesn't get set and passes through
        $opts->result->message = "Transaction failed. Error #-1";
        if (isset($response['RESULT']) && $response['RESULT'] == 0) // Approved !!!
        {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            $opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->CVV2MATCH = $response['CVV2MATCH'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];
            $opts->result->traction_type = 'Authorization';
            $trax_state = "authorized";
        } else {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            $opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->CVV2MATCH = $response['CVV2MATCH'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];
            $opts->result->traction_type = 'Authorization';
            $trax_state = "error";
        }

        $opts->result->payment_status = $trax_state;
//        $opts->result = $object;
        $opts->cc_number = 'xxxx-xxxx-xxxx-' . substr($opts->cc_number, -4);
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));
        $this->createBillingTransaction($billingmethod, number_format(0, 2, '.', ''), $opts->result, $trax_state);
        return $opts->result;
    }

    // delayed capture
    function delayed_capture($billingmethod, $amount, $order) {
//        global $order, $db, $user;

        //eDebug($order);
        $opts = expUnserialize($billingmethod->billing_options);
        //eDebug($billingmethod, true);

        // make sure we have some billing options saved.
        if (empty($billingmethod)) return false;
        //if ($order->grand_total <= 0) return false;

        $config = unserialize($this->config);
        //eDebug($this);
        // set the api endpoint url depending on test mode setting
        if ($config['testmode'] == 1) {
            $submiturl = 'https://pilot-payflowpro.paypal.com';
            flash('message', gt('This Transaction is in TEST MODE'));
        } else {
            $submiturl = 'https://payflowpro.paypal.com';
        }

        //eDebug($config,true);
        $apiParams = array(
            'USER'      => (empty($config['user'])) ? $config['vendor'] : $config['user'],
            'VENDOR'    => $config['vendor'],
            'PARTNER'   => $config['partner'],
            'PWD'       => $config['password'],
            'VERBOSITY' => 'MEDIUM',
            'TENDER'    => 'C', // C = credit card, P = PayPal
            'TRXTYPE'   => 'D', // S = Sale transaction, A = Authorization, C = Credit, D = Delayed Capture, V = Void
            'AMT'       => number_format($amount, 2, '.', ''),

            'ORIGID'    => $opts->result->PNREF,

            'COMMENT1'  => 'Delayed Capture',
            //'COMMENT2'  =>  '',
        );

        // eDebug($apiParams,true);

        // convert the api params to a name value pair string
        $nvpstr = "";
//        while (list($key, $value) = each($apiParams)) {
        foreach($apiParams as $key=>$value) {
            $tmpVal = urlencode(preg_replace('/,/', '', $value));
            $nvpstr .= $key . '[' . strlen($tmpVal) . ']=' . $tmpVal . '&';
        }

        // take the last & out for the string
        $nvpstr = substr($nvpstr, 0, -1);

        // build hash
        $request_id = md5($config['vendor'] . $opts->result->PNREF . time());

        $headers[] = "X-VPS-Request-ID: " . $request_id; //random unique string
        $headers[] = "Content-Type: text/namevalue "; //or maybe text/xml
        $headers[] = "X-VPS-Timeout: 30";
        $headers[] = "X-VPS-VIT-OS-Name: Linux"; // Name of your OS
        $headers[] = "X-VPS-VIT-OS-Version: CentOS"; // OS Version
        $headers[] = "X-VPS-VIT-Client-Type: PHP/cURL"; // What you are using
        $headers[] = "X-VPS-VIT-Client-Version: 0.01"; // For your info
        $headers[] = "X-VPS-VIT-Client-Architecture: x86"; // For your info
        $headers[] = "X-VPS-VIT-Integration-Product: ExponentCMS"; // For your info, would populate with application name
        $headers[] = "X-VPS-VIT-Integration-Version: 2.0"; // Application version

        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $submiturl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr); //adding POST data
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //verifies ssl certificate
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true); //forces closure of connection when done
        curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST

        $result = curl_exec($ch);

        $headers = curl_getinfo($ch);
        curl_close($ch);

        $response = $this->parseResponse($result); //result array

        // eDebug($response,true);
//        $object = new stdClass();
        $trax_state = '';
        $opts->result->errorCode = -1; //if totally fails, this doesn't get set and passes through
        $opts->result->message = "Transaction failed. Error #-1";
        if (isset($response['RESULT']) && $response['RESULT'] == 0) // Approved !!!
        {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            $opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->traction_type = 'Capture';
            $opts->result->amount_captured = $amount;
            $trax_state = "complete";
            $opts->result->payment_status = $trax_state;
//            $object = $opts->result;
        } else {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            /*$opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];
            $opts->result->traction_type = 'Capture'; */
            $trax_state = "error";
        }
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));  //FIXME not sure this is correct, but we need to update billingmethod
        //don't wnat to update if the capture failed, as we can always try again
        $this->createBillingTransaction($billingmethod, number_format($amount, 2, '.', ''), $opts->result, $trax_state);
        return $opts->result;
    }

    // void_transaction
    function void_transaction($billingmethod, $order) {
//        global $order, $db, $user;

        // make sure we have some billing options saved.
        if (empty($billingmethod)) return false;

        $config = unserialize($this->config);
        $opts = expUnserialize($billingmethod->billing_options);
        // set the api endpoint url depending on test mode setting
        if ($config['testmode'] == 1) {
            $submiturl = 'https://pilot-payflowpro.paypal.com';
            flash('message', gt('This Transaction is in TEST MODE'));
        } else {
            $submiturl = 'https://payflowpro.paypal.com';
        }

        $apiParams = array(
            'USER'      => (empty($config['user'])) ? $config['vendor'] : $config['user'],
            'VENDOR'    => $config['vendor'],
            'PARTNER'   => $config['partner'],
            'PWD'       => $config['password'],
            'VERBOSITY' => 'MEDIUM',
            'TENDER'    => 'C', // C = credit card, P = PayPal
            'TRXTYPE'   => 'V', // S = Sale transaction, A = Authorization, C = Credit, D = Delayed Capture, V = Void

            'ORIGID'    => $opts->result->PNREF,

            'COMMENT1'  => 'Void',
            //'COMMENT2'  =>  '',
        );

        // convert the api params to a name value pair string
        $nvpstr = "";
//        while (list($key, $value) = each($apiParams)) {
        foreach($apiParams as $key=>$value) {
            $tmpVal = urlencode(preg_replace('/,/', '', $value));
            $nvpstr .= $key . '[' . strlen($tmpVal) . ']=' . $tmpVal . '&';
        }

        // take the last & out for the string
        $nvpstr = substr($nvpstr, 0, -1);

        // build hash
        $request_id = md5($opts->cc_number . $order->grand_total . time() . "1");

        $headers[] = "X-VPS-Request-ID: " . $request_id;
        $headers[] = "Content-Type: text/namevalue "; //or maybe text/xml
        $headers[] = "X-VPS-Timeout: 30";
        $headers[] = "X-VPS-VIT-OS-Name: Linux"; // Name of your OS
        $headers[] = "X-VPS-VIT-OS-Version: CentOS"; // OS Version
        $headers[] = "X-VPS-VIT-Client-Type: PHP/cURL"; // What you are using
        $headers[] = "X-VPS-VIT-Client-Version: 0.01"; // For your info
        $headers[] = "X-VPS-VIT-Client-Architecture: x86"; // For your info
        $headers[] = "X-VPS-VIT-Integration-Product: ExponentCMS"; // For your info, would populate with application name
        $headers[] = "X-VPS-VIT-Integration-Version: 2.0"; // Application version

        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $submiturl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr); //adding POST data
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //verifies ssl certificate
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true); //forces closure of connection when done
        curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST

        $result = curl_exec($ch);

        $headers = curl_getinfo($ch);
        curl_close($ch);

        $response = $this->parseResponse($result); //result array

        //eDebug($response,true);
//        $object = new stdClass();
        $trax_state = '';
        $opts->result->errorCode = -1; //if totally fails, this doesn't get set and passes through
        $opts->result->message = "Transaction failed. Error #-1";
        if (isset($response['RESULT']) && $response['RESULT'] == 0) // Approved !!!
        {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            $opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            /*$opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];*/
            $opts->result->traction_type = 'Void';
            //$opts->result->amount_captured = $amount;
            $trax_state = "voided";
            $opts->result->payment_status = $trax_state;
//            $object = $opts->result;
        } else {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            /*$opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];
            $opts->result->traction_type = 'Capture'; */
            $trax_state = "error";
        }
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));  //FIXME not sure this is correct, but we need to update billingmethod
        //don't wnat to update if the capture failed, as we can always try again
        $this->createBillingTransaction($billingmethod, 0, $opts->result, $trax_state);
        return $opts->result;
    }

    // credit transaction
    function credit_transaction($billingmethod, $amount, $order) {
//        global $order, $db, $user;

        // make sure we have some billing options saved.
        if (empty($billingmethod)) return false;

        $config = unserialize($this->config);
        $opts = expUnserialize($billingmethod->billing_options);
        // set the api endpoint url depending on test mode setting
        if ($config['testmode'] == 1) {
            $submiturl = 'https://pilot-payflowpro.paypal.com';
            flash('message', gt('This Transaction is in TEST MODE'));
        } else {
            $submiturl = 'https://payflowpro.paypal.com';
        }

        $apiParams = array(
            'USER'      => (empty($config['user'])) ? $config['vendor'] : $config['user'],
            'VENDOR'    => $config['vendor'],
            'PARTNER'   => $config['partner'],
            'PWD'       => $config['password'],
            'VERBOSITY' => 'MEDIUM',
            'TENDER'    => 'C', // C = credit card, P = PayPal
            'TRXTYPE'   => 'C', // S = Sale transaction, A = Authorization, C = Credit, D = Delayed Capture, V = Void
            'AMT'       => number_format($amount, 2, '.', ''),

            'ORIGID'    => $opts->result->PNREF,

            'COMMENT1'  => 'Credit',
            //'COMMENT2'  =>  '',
        );

        // convert the api params to a name value pair string
        $nvpstr = "";
//        while (list($key, $value) = each($apiParams)) {
        foreach($apiParams as $key=>$value) {
            $tmpVal = urlencode(preg_replace('/,/', '', $value));
            $nvpstr .= $key . '[' . strlen($tmpVal) . ']=' . $tmpVal . '&';
        }

        // take the last & out for the string
        $nvpstr = substr($nvpstr, 0, -1);

        // build hash
        $request_id = md5($opts->cc_number . $order->grand_total . time() . "1");

        $headers[] = "X-VPS-Request-ID: " . $request_id;
        $headers[] = "Content-Type: text/namevalue "; //or maybe text/xml
        $headers[] = "X-VPS-Timeout: 30";
        $headers[] = "X-VPS-VIT-OS-Name: Linux"; // Name of your OS
        $headers[] = "X-VPS-VIT-OS-Version: CentOS"; // OS Version
        $headers[] = "X-VPS-VIT-Client-Type: PHP/cURL"; // What you are using
        $headers[] = "X-VPS-VIT-Client-Version: 0.01"; // For your info
        $headers[] = "X-VPS-VIT-Client-Architecture: x86"; // For your info
        $headers[] = "X-VPS-VIT-Integration-Product: ExponentCMS"; // For your info, would populate with application name
        $headers[] = "X-VPS-VIT-Integration-Version: 2.0"; // Application version

        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; // play as Mozilla
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $submiturl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_HEADER, 1); // tells curl to include headers in response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // times out after 45 secs
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // this line makes it work under https
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr); //adding POST data
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //verifies ssl certificate
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true); //forces closure of connection when done
        curl_setopt($ch, CURLOPT_POST, 1); //data sent as POST

        $result = curl_exec($ch);

        $headers = curl_getinfo($ch);
        curl_close($ch);

        $response = $this->parseResponse($result); //result array
        //eDebug($response,true);
//        $object = new stdClass();
        $trax_amount = 0;
        $trax_state = '';
        $opts->result->errorCode = -1; //if totally fails, this doesn't get set and passes through
        $opts->result->message = "Transaction failed. Error #-1";
        if (isset($response['RESULT']) && $response['RESULT'] == 0) // Approved !!!
        {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            $opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            /*$opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];*/
            $opts->result->traction_type = 'Credit';
            $opts->result->amount_captured = $amount;
//            $trax_state = "credited";
            $trax_state = "refunded";
            $opts->result->payment_status = $trax_state;
//            $object = $opts->result;
        } else {
            $opts->result->request_id = $request_id;
            $opts->result->errorCode = $response['RESULT'];
            $opts->result->message = $response['RESPMSG'];
            /*$opts->result->PNREF = $response['PNREF'];
            $opts->result->AUTHCODE = $response['AUTHCODE'];
            $opts->result->AVSADDR = $response['AVSADDR'];
            $opts->result->AVSZIP = $response['AVSZIP'];
            $opts->result->HOSTCODE = $response['HOSTCODE'];
            $opts->result->PROCAVS = $response['PROCAVS'];
            $opts->result->traction_type = 'Capture'; */
            $trax_state = "error";
        }
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));  //FIXME not sure this is correct, but we need to update billingmethod
        //don't wnat to update if the capture failed, as we can always try again
        $this->createBillingTransaction($billingmethod, -(number_format($amount, 2, '.', '')), $opts->result, $trax_state);
        return $opts->result;
    }

    //Config Form
//    function configForm() {
//        $form = BASE . 'framework/modules/ecommerce/billingcalculators/views/payflowpro/configure.tpl';
//        return $form;
//    }

    //process config form
    function parseConfig($values) {
        $config_vars = array('vendor', 'user', 'password', 'partner', 'testmode', 'process_mode', 'accepted_cards', 'email_customer', 'email_admin', 'notification_addy');
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

    // Depdicated?
    //This should return html to display config settings on the view billing method page
    function view($config_object) {
        $html = "<br>" . gt('Settings') . ":<br/><hr>";
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
        $html .= "<br>".gt('Accepted Cards') . ":<hr>";
        $html .= "American Express: " . (($config_object->accept_amex) ? "Yes" : "No") . "<br>";
        $html .= "Discover: " . (($config_object->accept_discover) ? "Yes" : "No") . "<br>";
        $html .= "Mastercard: " . (($config_object->accept_mastercard) ? "Yes" : "No") . "<br>";
        $html .= "Visa: " . (($config_object->accept_visa) ? "Yes" : "No") . "<br><br>";
        //$html .= "Offer Tax Exempt Field: ".(($config_object->offer_tax_exempt_field)?"Yes":"No")."<br>";

        return $html;
    }

    public function postProcess($order, $params) {
        $this->opts = null;
        return true;
    }

    // parse result and return an array
    function parseResponse($response) {
        if (empty($response)) {
            return;
        }

        $respArr = array();
        $response = strstr($response, 'RESULT');
        $valArray = explode('&', $response);
        foreach ($valArray as $val) {
            $valArray2 = explode('=', $val);
            $respArr[$valArray2[0]] = $valArray2[1];
        }
        return $respArr;
    }

    function getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
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
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->cc_type;
    }

    /** Unused */
//    function showOptions($bm) {
//        return expUnserialize($bm);
//    }
}

?>