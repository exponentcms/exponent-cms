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

//TODO: make into php5 class with access modifiers proprities and all that jazz.
class paypalExpressCheckout extends billingcalculator {

    const PAYPAL_API_VERSION = '124.0';

    /**
     * The name that will be displayes in the payment methods selector admin screen.
     *
     * @return string Then name of the billing calculator
     */
    function name() {
        return gt('PayPal Express');
    }

//    public $use_title = 'PayPal Express';
    public $payment_type = 'PayPal';

    /**
     * The description that will be displayed in the payment methods selector admin screen
     *
     * @return string A short description
     */
    function description() {
        return gt("Enabling this payment option will allow your customers to use a PayPal account to make purchases. It requires a Merchant Account with PayPal in order to obtain an API signature.");
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

    /**
     * Does this billing calculator have a User Form?
     *
     * @return boolean
     */
    function hasUserForm() {
        return false;
    }

    /**
     * Does this billing calculator take the user offsite?
     *
     * @return boolean
     */
    function isOffsite() {
        return true;
    }

    /**
     * For paypal this will call out to the PP api and get a token then redirect to PP.
     * PP then redirects back the site with token in the url. We can pick up that token
     * from the url such that if we already have it we'll call another PP api to get the
     * details and make it match up to the order.
     *
     * @param mixed $billingmethod The billing method information for this user
     * @param mixed $opts
     * @param array $params The url prameters, as if sef was off.
     * @param       $order
     *
     * @return mixed An object indicating pass of failure.
     */
    function preprocess($billingmethod, $opts, $params, $order) {
//        global $db, $user;

        //eDebug($params);
        if (!isset($params['token'])) {

            //eDebug($billingmethod);
            //eDebug($opts);
            // make sure we have some billing options saved.
            if (empty($billingmethod) /*|| empty($opts)*/) {
                return false;
            }

            // get a shipping address to display in the invoice email.
            $shippingaddress = $order->getCurrentShippingMethod();
            $shipping_state = new geoRegion($shippingaddress->state);
//            $shipping_country = new geoCountry($shipping_state->country_id);
            $shipping_country = new geoCountry($shippingaddress->country);

//            $state = new geoRegion($billingmethod->state);
//            $country = new geoCountry($state->country_id);

            $config = expUnserialize($this->config);
            //eDebug($config, true);  
            if ($config['testmode']) {
                /**
                 * This is the URL that the buyer is first sent to to authorize payment with their paypal account
                 * change the URL depending if you are testing on the sandbox or going to the live PayPal site For the sandbox,
                 * the URL is https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=
                 * For the live site, the URL is https://www.paypal.com/webscr&cmd=_express-checkout&token=
                 *
                 * @var string
                 */
                $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
                if (!empty($params['in_context'])) {
                    $paypal_url = 'https://www.sandbox.paypal.com/checkoutnow?token=';
                }
            } else {
                $paypal_url = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=';
                if (!empty($params['in_context'])) {
                    $paypal_url = 'https://www.paypal.com/checkoutnow?token=';
                }
            }

            /**
             * After the user has completed things at Paypal they will be sent back to our site. This tells PayPal where to send them
             *
             * @var string
             */
            $returnURL = makeLink(array('controller' => 'cart', 'action' => 'preprocess'));

            /**
             * If the user cancels the transaction at PayPal, they are sent back to our site. This tells PayPal where to send them
             *
             * @var string
             */
            $cancelURL = makeLink(array('controller' => 'cart', 'action' => 'checkout'), true);

            $shipname = $shippingaddress->firstname . ' ';
            $shipname .= empty($shippingaddress->middlename) ? $shippingaddress->lastname : $shippingaddress->middlename . ' ' . $shippingaddress->lastname;

            $shipstreet = $shippingaddress->address1;
            $shipstreet .= empty($shippingaddress->address2) ? '' : ', ' . $shippingaddress->address2;

            if ($config['testmode']) {
                $uname = $config['testusername'];
                $pwd = $config['testpassword'];
                $sig = $config['testsignature'];
            } else {
                $uname = $config['username'];
                $pwd = $config['password'];
                $sig = $config['signature'];
            }
            /**
             * An array of the data sent to PayPal. It will be transformend into Name=Value pairs later.
             *
             * @var array
             */
            $data = array(
                // required parameters
                'METHOD'                             => 'SetExpressCheckout',
                'USER'                               => $uname,
                'PWD'                                => $pwd,
                'SIGNATURE'                          => $sig,
                'VERSION'                            => paypalExpressCheckout::PAYPAL_API_VERSION,
                'RETURNURL'                          => $returnURL,
                'CANCELURL'                          => $cancelURL,
                'ALLOWNOTE'                          => '1', // 0 or 1 to allow buyer to send note from paypal, we don't do anything with it so turn it off
                'NOSHIPPING'                         => $order->shipping_required?'0':'1',
                'PAYMENTREQUEST_0_PAYMENTACTION'     => $config['process_mode'],
                'PAYMENTREQUEST_0_CURRENCYCODE'      => ECOM_CURRENCY, // currency code
                'PAYMENTREQUEST_0_ITEMAMT'           => number_format($order->total, 2, '.', ''), // total item cost
                'PAYMENTREQUEST_0_SHIPPINGAMT'       => number_format($order->shipping_total + $order->surcharge_total, 2, '.', ''), // total shipping cost
                'PAYMENTREQUEST_0_TAXAMT'            => number_format($order->tax, 2, '.', ''), // total tax cost
                'PAYMENTREQUEST_0_AMT'               => number_format($order->grand_total, 2, '.', ''), // total amount
                'ADDROVERRIDE'                       => '1',
                'PAYMENTREQUEST_0_SHIPTONAME'        => $shipname,
                'PAYMENTREQUEST_0_SHIPTOSTREET'      => $shipstreet,
                'PAYMENTREQUEST_0_SHIPTOCITY'        => $shippingaddress->city,
                'PAYMENTREQUEST_0_SHIPTOSTATE'       => $shipping_state->code,
                'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $shipping_country->iso_code_2letter,
                'PAYMENTREQUEST_0_SHIPTOZIP'         => $shippingaddress->zip
            );

            for ($n = 0, $nMax = count($order->orderitem); $n < $nMax; $n++) {
                $data['L_PAYMENTREQUEST_0_NAME' . $n] = strlen($order->orderitem[$n]->products_name) > 127 ? substr($order->orderitem[$n]->products_name, 0, 124) . "..." : $order->orderitem[$n]->products_name;
                $desc = strip_tags($order->orderitem[$n]->product->body);
                $data['L_PAYMENTREQUEST_0_DESC' . $n] = strlen($desc) > 127 ? substr($desc, 0, 124) . "..." : $desc;
                $data['L_PAYMENTREQUEST_0_NUMBER' . $n] = strlen($order->orderitem[$n]->product->model) > 127 ? substr($order->orderitem[$n]->product->model, 0, 124) . "..." : $order->orderitem[$n]->product->model;
                $data['L_PAYMENTREQUEST_0_QTY' . $n] = $order->orderitem[$n]->quantity;
//                $data['L_PAYMENTREQUEST_0_TAXAMT' . $n] = number_format(($order->orderitem[$n]->products_tax), 2, '.', '');  // note: will cause failure when using taxed shipping
                $data['L_PAYMENTREQUEST_0_AMT' . $n] = number_format(($order->orderitem[$n]->products_price_adjusted), 2, '.', '');
                //$it += number_format(($order->orderitem[$n]->products_tax), 2, '.', '') * $order->orderitem[$n]->quantity;
                //$tt += number_format(($order->orderitem[$n]->products_price_adjusted), 2, '.', '') * $order->orderitem[$n]->quantity;
            }
            //eDebug($data, true);
            /* eDebug($shippingaddress);
          eDebug($shipping_state);
          eDebug($shipping_country, true); */

            $nvpResArray = $this->paypalApiCall($data);

//            $object = new stdClass();
            if (!empty($nvpResArray['curl_error'])) {
                //curl error
                $opts->result->errorCode = $nvpResArray['curl_errno']; //Response reason code
                $opts->result->message = $nvpResArray['curl_error'];

//                $opts->result = $object;
                $billingmethod->update(array('billing_options' => serialize($opts)));
            } elseif ($nvpResArray['ACK'] == 'Error' || $nvpResArray['ACK'] == 'Failure' || $nvpResArray['ACK'] == 'FailureWithWarning' || $nvpResArray['ACK'] == 'Warning') {
                // paypal error
                $opts->result->errorCode = "";
                $opts->result->message = gt("The following errors occurred") . ": ";

                // its possible there are more than one error. 
                foreach ($nvpResArray as $k => $v) {
                    if (is_array($v)) {
                        $opts->result->errorCode .= $v['ERRORCODE'] . ", ";
                        $opts->result->message .= $v['LONGMESSAGE'] . ", ";
//                        $object->errorCode .= $v['L_ERRORCODE0'].", ";
//                        $object->message .= $v['L_LONGMESSAGE0'].", ";
                    }
                }
                // remove the trailing ", " (comma space)
                $opts->result->errorCode = preg_replace("/,\s$/", "", $opts->result->errorCode);
                $opts->result->message = preg_replace("/,\s$/", ".", $opts->result->message);

//                $opts->result = $object;
                $billingmethod->update(array('billing_options' => serialize($opts)));
            } else {
                // Approved
                $opts->result->errorCode = 0;
                $opts->result->message = gt("SetExpressCheckout successfully returned token.");
                $opts->result->token = $nvpResArray['TOKEN'];
                $opts->result->correlationID = $nvpResArray['CORRELATIONID'];

//                $opts->result = $object;
                $billingmethod->update(array('billing_options' => serialize($opts)));

                // redirect to PayPal checkout
                redirect_to($paypal_url . $nvpResArray['TOKEN']);
            }
        } else {  // 2nd time through before displaying checkout confirm
            //eDebug($params);
            //eDebug($billingmethod);
            $opts = expUnserialize($billingmethod->billing_options);  //FIXME why aren't we passing $opts?
            //eDebug($object,true);
            if ($opts->result->token == $params['token']) {
                $opts->result->errorCode = 0;
                $opts->result->message = gt("User has approved the payment at PayPal");
                $opts->result->PayerID = $params['PayerID'];
                $opts->result->payment_status = 'pending';
                $opts->result->transId = gt('not yet assigned');
                $billingmethod->update(array('billing_options' => serialize($opts)));
                return $opts->result;
            } else {
                $opts->result->errorCode = 1;
                $opts->result->message = gt("PayPal Token Mismatch");
                $opts->result->PayerID = $params['PayerID'];
                $billingmethod->update(array('billing_options' => serialize($opts)));
                return $opts->result;
            }
        }
        return $opts->result;
    }

//    function process($billingmethod, $opts, $params, $invoice_number) {
    function process($billingmethod, $opts, $params, $order) {
        $opts = expUnserialize($billingmethod->billing_options);  //FIXME why aren't we passing $opts?
        $config = expUnserialize($this->config);

        if ($config['testmode']) {
            $uname = $config['testusername'];
            $pwd = $config['testpassword'];
            $sig = $config['testsignature'];
        } else {
            $uname = $config['username'];
            $pwd = $config['password'];
            $sig = $config['signature'];
        }
        //eDebug($order);
        $data = array(
            // required parameters
            'METHOD'                         => 'DoExpressCheckoutPayment',
            'USER'                           => $uname,
            'PWD'                            => $pwd,
            'SIGNATURE'                      => $sig,
            'VERSION'                        => paypalExpressCheckout::PAYPAL_API_VERSION,
            'SOLUTIONTYPE'                   => 'Sole', //added per post
            'LANDINGPAGE'                    => 'Billing', //added per post
            'TOKEN'                          => $opts->result->token,
            'PAYERID'                        => $opts->result->PayerID,
            'PAYMENTREQUEST_0_INVNUM'        => $order->invoice_id,
            'PAYMENTREQUEST_0_CUSTOM'        => 'Invoice #' . $order->invoice_id,
            'PAYMENTREQUEST_0_PAYMENTACTION' => $config['process_mode'],
            'PAYMENTREQUEST_0_CURRENCYCODE'  => ECOM_CURRENCY,
            'PAYMENTREQUEST_0_ITEMAMT'       => number_format($order->total, 2, '.', ''),
            'PAYMENTREQUEST_0_SHIPPINGAMT'   => number_format($order->shipping_total + $order->surcharge_total, 2, '.', ''),
            'PAYMENTREQUEST_0_TAXAMT'        => number_format($order->tax, 2, '.', ''),
            'PAYMENTREQUEST_0_AMT'           => number_format($order->grand_total, 2, '.', ''),
        );

//        $it = 0;
//        $tt = 0;
        for ($n = 0, $nMax = count($order->orderitem); $n < $nMax; $n++) {
            $data['L_PAYMENTREQUEST_0_NAME' . $n] = strlen($order->orderitem[$n]->products_name) > 127 ? substr($order->orderitem[$n]->products_name, 0, 124) . "..." : $order->orderitem[$n]->products_name;
            $desc = strip_tags($order->orderitem[$n]->product->body);
            $data['L_PAYMENTREQUEST_0_DESC' . $n] = strlen($desc) > 127 ? substr($desc, 0, 124) . "..." : $desc;
            $data['L_PAYMENTREQUEST_0_NUMBER' . $n] = strlen($order->orderitem[$n]->product->model) > 127 ? substr($order->orderitem[$n]->product->model, 0, 124) . "..." : $order->orderitem[$n]->product->model;
            $data['L_PAYMENTREQUEST_0_QTY' . $n] = $order->orderitem[$n]->quantity;
//            $data['L_PAYMENTREQUEST_0_TAXAMT' . $n] = number_format(($order->orderitem[$n]->products_tax), 2, '.', ''); // note: will cause failure when using taxed shipping
            $data['L_PAYMENTREQUEST_0_AMT' . $n] = number_format(($order->orderitem[$n]->products_price_adjusted), 2, '.', '');
            //$it += number_format(($order->orderitem[$n]->products_tax), 2, '.', '') * $order->orderitem[$n]->quantity;
            //$tt += number_format(($order->orderitem[$n]->products_price_adjusted), 2, '.', '') * $order->orderitem[$n]->quantity;
        }

        //eDebug($it);
        //eDebug($tt);
        //eDebug($data);  
        //eDebug($billing_options, true);  

        $nvpResArray = $this->paypalApiCall($data);
        //eDebug($nvpResArray);  

        //if ($nvpResArray['ACK'] == 'Failure' || $nvpResArray['ACK'] == 'FailureWithWarning') 
        //{ 
        //FJD: somehow some orders have snuck through wihtout fully processing, so I switched this 
        //around to check for succcess ONLY and then default to an error otherwise    
        if (!empty($nvpResArray['curl_error'])) {
            //curl error            
            $opts->result->errorCode = $nvpResArray['curl_errno']; //Response reason code
            $opts->result->message = $nvpResArray['curl_error'];

            //$opts->result = $object;                
            $transaction_state = "Temporary Failure";
            $trax_state = "error";
        } else if ($nvpResArray['ACK'] == 'Success' || $nvpResArray['ACK'] == 'SuccessWithWarning') {
            /*
            [TOKEN] => EC-7YW97132PA0236148 [TIMESTAMP] => 2010-01-16T21:49:15Z [CORRELATIONID] => 7f49bba2eac7e 
            [ACK] => Success [VERSION] => 59.0 [BUILD] => 1152253 [TRANSACTIONID] => 1AA09727DG247464P [TRANSACTIONTYPE] => cart 
            [PAYMENTTYPE] => instant [ORDERTIME] => 2010-01-16T21:49:14Z [AMT] => 118.09 [FEEAMT] => 3.72 [TAXAMT] => 6.75 
            [CURRENCYCODE] => USD [PAYMENTSTATUS] => Pending [PENDINGREASON] => paymentreview [REASONCODE] => None 
            [PROTECTIONELIGIBILITY] => Ineligible 
            */
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = 0;
            if ($nvpResArray['ACK'] == 'SuccessWithWarning') {
                $opts->result->message = $nvpResArray['ACK'] . ": " . $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
//                $billing_options->result->message = $nvpResArray['ACK'] . ": " . $nvpResArray[0]['L_SHORTMESSAGE0'] . ": " . $nvpResArray[0]['L_LONGMESSAGE0']; ;
            } else {
                $opts->result->message = $nvpResArray['ACK'];
            }
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $opts->result->timestamp = $nvpResArray['TIMESTAMP'];
            $opts->result->note = $nvpResArray['NOTE']; //FIXME, what can we do with the note returned?
            $opts->result->transId = $nvpResArray['PAYMENTINFO_0_TRANSACTIONID'];
            $opts->result->paymenttype = $nvpResArray['PAYMENTINFO_0_PAYMENTTYPE'];
            $opts->result->amt = $nvpResArray['PAYMENTINFO_0_AMT'];
            $opts->result->fee_amt = $nvpResArray['PAYMENTINFO_0_FEEAMT'];
            $opts->result->settle_amt = $nvpResArray['PAYMENTINFO_0_SETTLEAMT'];
            $opts->result->payment_status = $nvpResArray['PAYMENTINFO_0_PAYMENTSTATUS'];
            $opts->result->pending_reason = $nvpResArray['PAYMENTINFO_0_PENDINGREASON'];
            $opts->result->reason_code = $nvpResArray['PAYMENTINFO_0_REASONCODE'];
//            $billing_options->result->transactionID = $nvpResArray['PAYMENTINFO_0_TRANSACTIONID'];
//            $transaction_state = $nvpResArray['PAYMENTINFO_0_PAYMENTSTATUS'];
//            $trax_state = "complete";//FIXME only true if mode is 'sale'
            $trax_state = $opts->result->payment_status;
            if ($trax_state == 'Pending' && $opts->result->pending_reason == 'authorization') {
                $trax_state = 'authorized';  // authorized awaiting capture
                $billingcost = 0;
            } elseif ($trax_state == 'Completed') {
                $trax_state = 'complete';  // captured
                $billingcost = $order->grand_total;
            }
        } else {  // PayPal error response
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = $nvpResArray[0]['ERRORCODE'];
//            $billing_options->result->errorCode = $nvpResArray[0]['L_ERRORCODE0'];
            if (!$opts->result->errorCode)
                $opts->result->errorCode = "1010";
            $opts->result->message = $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
            $opts->result->payment_status = 'error';
//            $billing_options->result->message = $nvpResArray[0]['L_SHORTMESSAGE0'] . ": " . $nvpResArray[0]['L_LONGMESSAGE0']; ;
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $transaction_state = "Failure";
//            $trax_state = "error";
            $trax_state = $opts->result->payment_status;
        }
        //eDebug($billing_options,true);                                                               
//        $billingmethod->update(array('billing_options' => serialize($billing_options), 'transaction_state' => $transaction_state));
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));
        $this->createBillingTransaction($billingmethod, number_format($billingcost, 2, '.', ''), $opts->result, $trax_state);
        return $opts->result;

    }

    function delayed_capture($billingmethod, $amount , $order) {
        $opts = expUnserialize($billingmethod->billing_options);
        $config = expUnserialize($this->config);

        if ($config['testmode']) {
            $uname = $config['testusername'];
            $pwd = $config['testpassword'];
            $sig = $config['testsignature'];
        } else {
            $uname = $config['username'];
            $pwd = $config['password'];
            $sig = $config['signature'];
        }

        $data = array(
            // required parameters
            'METHOD'                         => 'DoCapture',
            'USER'                           => $uname,
            'PWD'                            => $pwd,
            'SIGNATURE'                      => $sig,
            'VERSION'                        => paypalExpressCheckout::PAYPAL_API_VERSION,
            'AUTHORIZATIONID'                => $opts->result->transId,
            'AMT'                            => number_format($amount, 2, '.', ''),
            'COMPLETETYPE'                   => 'Complete',  // or 'NotComplete'
            // optional parameters
            'CURRENCYCODE'                   => ECOM_CURRENCY,
            'INVNUM'                         => $order->invoice_id,
            'NOTE'                           => '',
        );

        $nvpResArray = $this->paypalApiCall($data);

        if (!empty($nvpResArray['curl_error'])) {  //curl error
            $opts->result->errorCode = $nvpResArray['curl_errno']; //Response reason code
            $opts->result->message = $nvpResArray['curl_error'];

            $transaction_state = "Temporary Failure";
            $trax_state = "error";
        } else if ($nvpResArray['ACK'] == 'Success' || $nvpResArray['ACK'] == 'SuccessWithWarning') {
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = 0;
            if ($nvpResArray['ACK'] == 'SuccessWithWarning') {
                $opts->result->message = $nvpResArray['ACK'] . ": " . $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
            } else {
                $opts->result->message = $nvpResArray['ACK'];
            }
            $opts->result->transId = $nvpResArray['TRANSACTIONID'];
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $opts->result->timestamp = $nvpResArray['ORDERTIME'];
            $opts->result->paymenttype = $nvpResArray['PAYMENTTYPE'];
            $opts->result->amt = $nvpResArray['AMT'];
            $opts->result->fee_amt = $nvpResArray['FEEAMT'];
            $opts->result->settle_amt = $nvpResArray['SETTLEAMT'];
            $opts->result->payment_status = $nvpResArray['PAYMENTSTATUS'];
            $opts->result->pending_reason = $nvpResArray['PENDINGREASON'];
            $transaction_state = $nvpResArray['PAYMENTSTATUS'];
            $trax_state = $opts->result->payment_status;
            if ($trax_state == 'Completed') {
                if ($amount != $order->grand_total) { //FIXME what about multiple captures?
                    $trax_state = 'authorized';  // awaiting additional capture
                } else {
                    $trax_state = 'complete';  // completed capture
                }
            }
        } else {  // PayPal error response
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = $nvpResArray[0]['ERRORCODE'];
//            $billing_options->result->errorCode = $nvpResArray[0]['L_ERRORCODE0'];
            if (!$opts->result->errorCode)
                $opts->result->errorCode = "1010";
            $opts->result->message = $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
            $opts->result->payment_status = 'error';
//            $billing_options->result->message = $nvpResArray[0]['L_SHORTMESSAGE0'] . ": " . $nvpResArray[0]['L_LONGMESSAGE0']; ;
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $transaction_state = "Failure";
//            $trax_state = "error";
            $trax_state = $opts->result->payment_status;
        }

        //eDebug($billing_options,true);
//        $billingmethod->update(array('billing_options' => serialize($billing_options), 'transaction_state' => $transaction_state));
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));
        $this->createBillingTransaction($billingmethod, number_format($amount, 2, '.', ''), $opts->result, $trax_state);
        return $opts->result;
    }

    function void_transaction($billingmethod, $order) {
        $opts = expUnserialize($billingmethod->billing_options);
        $config = expUnserialize($this->config);

        if ($config['testmode']) {
            $uname = $config['testusername'];
            $pwd = $config['testpassword'];
            $sig = $config['testsignature'];
        } else {
            $uname = $config['username'];
            $pwd = $config['password'];
            $sig = $config['signature'];
        }

        $data = array(
            // required parameters
            'METHOD'                         => 'DoVoid',
            'USER'                           => $uname,
            'PWD'                            => $pwd,
            'SIGNATURE'                      => $sig,
            'VERSION'                        => paypalExpressCheckout::PAYPAL_API_VERSION,
            'AUTHORIZATIONID'                => $opts->result->transId,
            // optional parameters
            'NOTE'                           => '',
        );

        $nvpResArray = $this->paypalApiCall($data);

        if (!empty($nvpResArray['curl_error'])) {  //curl error
            $opts->result->errorCode = $nvpResArray['curl_errno']; //Response reason code
            $opts->result->message = $nvpResArray['curl_error'];

            $transaction_state = "Temporary Failure";
            $trax_state = "error";
        } else if ($nvpResArray['ACK'] == 'Success' || $nvpResArray['ACK'] == 'SuccessWithWarning') {
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = 0;
            if ($nvpResArray['ACK'] == 'SuccessWithWarning') {
                $opts->result->message = $nvpResArray['ACK'] . ": " . $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
            } else {
                $opts->result->message = $nvpResArray['ACK'];
            }
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $opts->result->transId = $nvpResArray['AUTHORIZATIONID'];
//            $billing_options->result->payment_status = $nvpResArray['PAYMENTSTATUS'];  //FIXME we probably need a payment_status
//            $billing_options->result->pending_reason = $nvpResArray['PENDINGREASON'];
            $transaction_state = "voided";
            $trax_state = "voided";
        } else {  // PayPal error response
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = $nvpResArray[0]['ERRORCODE'];
//            $billing_options->result->errorCode = $nvpResArray[0]['L_ERRORCODE0'];
            if (!$opts->result->errorCode)
                $opts->result->errorCode = "1010";
            $opts->result->message = $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
            $opts->result->payment_status = 'error';
//            $billing_options->result->message = $nvpResArray[0]['L_SHORTMESSAGE0'] . ": " . $nvpResArray[0]['L_LONGMESSAGE0']; ;
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $transaction_state = "Failure";
//            $trax_state = "error";
            $trax_state = $opts->result->payment_status;
        }

        //eDebug($billing_options,true);
//        $billingmethod->update(array('billing_options' => serialize($billing_options), 'transaction_state' => $transaction_state));
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));
        $this->createBillingTransaction($billingmethod, 0, $opts->result, $trax_state);
        return $opts->result;
    }

    // credit (refund) transaction
    function credit_transaction($billingmethod, $amount, $order) {
        $opts = expUnserialize($billingmethod->billing_options);
        $config = expUnserialize($this->config);

        if ($config['testmode']) {
            $uname = $config['testusername'];
            $pwd = $config['testpassword'];
            $sig = $config['testsignature'];
        } else {
            $uname = $config['username'];
            $pwd = $config['password'];
            $sig = $config['signature'];
        }

        if ($amount > $order->grand_total) {
            $amount = $order->grand_total;
        }
        if ($amount == $order->grand_total) {
            $refundType = urlencode('Full');
        } else {
            $refundType = urlencode('Partial');
        }

        $data = array(
            // required parameters
            'METHOD'                         => 'RefundTransaction',
            'USER'                           => $uname,
            'PWD'                            => $pwd,
            'SIGNATURE'                      => $sig,
            'VERSION'                        => paypalExpressCheckout::PAYPAL_API_VERSION,
            'TRANSACTIONID'                  => $opts->result->transId,
            'REFUNDTYPE'                     => $refundType,
            'AMT'                            => $amount,
            // optional parameters
            'CURRENCYCODE'                   => ECOM_CURRENCY,
            'NOTE'                           => gt('Transaction Refunded'),
        );

        $nvpResArray = $this->paypalApiCall($data);

        if (!empty($nvpResArray['curl_error'])) {  //curl error
            $opts->result->errorCode = $nvpResArray['curl_errno']; //Response reason code
            $opts->result->message = $nvpResArray['curl_error'];

            $transaction_state = "Temporary Failure";
            $trax_state = "error";
        } else if ($nvpResArray['ACK'] == 'Success' || $nvpResArray['ACK'] == 'SuccessWithWarning') {
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = 0;
            if ($nvpResArray['ACK'] == 'SuccessWithWarning') {
                $opts->result->message = $nvpResArray['ACK'] . ": " . $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
            } else {
                $opts->result->message = $nvpResArray['ACK'];
            }
            $opts->result->transId = $nvpResArray['REFUNDTRANSACTIONID'];
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $opts->result->fee_amt = $nvpResArray['FEEREFUNDAMT'];
            $opts->result->gross_amt = $nvpResArray['GROSSREFUNDAMT'];
            $opts->result->net_amt = $nvpResArray['NETREFUNDAMT'];
            $opts->result->amt = $nvpResArray['TOTALREFUNDEDAMT'];
            $opts->result->info = $nvpResArray['REFUNDINFO'];
//            $billing_options->result->payment_status = $nvpResArray['PAYMENTSTATUS'];  //FIXME we probably need a payment_status
//            $billing_options->result->pending_reason = $nvpResArray['PENDINGREASON'];
            $transaction_state = "refunded";
            $trax_state = "refunded";
        } else {  // PayPal error response
            $opts->result->status = $nvpResArray['ACK'];
            $opts->result->errorCode = $nvpResArray[0]['ERRORCODE'];
//            $billing_options->result->errorCode = $nvpResArray[0]['L_ERRORCODE0'];
            if (!$opts->result->errorCode)
                $opts->result->errorCode = "1010";
            $opts->result->message = $nvpResArray[0]['SHORTMESSAGE'] . ": " . $nvpResArray[0]['LONGMESSAGE'];
            $opts->result->payment_status = 'error';
//            $billing_options->result->message = $nvpResArray[0]['L_SHORTMESSAGE0'] . ": " . $nvpResArray[0]['L_LONGMESSAGE0']; ;
            $opts->result->correlationID = $nvpResArray['CORRELATIONID'];
            $transaction_state = "Failure";
//            $trax_state = "error";
            $trax_state = $opts->result->payment_status;
        }

        //eDebug($billing_options,true);
//        $billingmethod->update(array('billing_options' => serialize($billing_options), 'transaction_state' => $transaction_state));
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $trax_state));
        $this->createBillingTransaction($billingmethod, -(number_format($amount, 2, '.', '')), $opts->result, $trax_state);
        return $opts->result;



//        // eDebug($billingmethod, true);
//        $billing_options = unserialize($billingmethod->billing_options);
//        $billing_transaction_options = unserialize($billingmethod->billingtransaction[0]->billing_options);
////        $config = expUnserialize($this->config);
//
//        // Set request-specific fields.
////        $transactionID = urlencode($billing_options->result->transactionID);
//        $transactionID = urlencode($billing_options->result->transId);
//        if ($amount > $order->grand_total) {
//            $amount = $order->grand_total;
//        }
//        if ($amount == $order->grand_total) {
//            $refundType = urlencode('Full');
//        } else {
//            $refundType = urlencode('Partial');
//        }
//        $memo = "Transaction Refunded"; // required if Partial.
//        $currencyID = urlencode(ECOM_CURRENCY); // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
//
//        // Add request-specific fields to the request string.
//        $nvpStr = "&TRANSACTIONID=$transactionID&CURRENCYCODE=$currencyID";
//
//        if (isset($memo)) {
//            $nvpStr .= "&NOTE=$memo";
//        }
//
//        $nvpStr .= "&REFUNDTYPE=$refundType";
//        if (strcasecmp($refundType, 'Partial') == 0) {
//            if (!isset($amount)) {
//                exit('Partial Refund Amount is not specified.');
//            } else {
//                $nvpStr .= "&AMT=$amount";
//            }
//            if (!isset($memo)) {
//                exit('Partial Refund Memo is not specified.');
//            }
//        }
//
//        // Execute the API operation; see the PPHttpPost function above.
//        $httpParsedResponseAr = $this->PPHttpPost('RefundTransaction', $nvpStr);
//
//        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
//            //update the billing method option
//            $billing_options->result->payment_status = 'Refunded';
//            unset($billing_options->result->pending_reason);
//
//            //Create another billing transaction option
//            $billing_transaction_options->result->payment_status = 'Refunded';
//            unset($billing_transaction_options->result->pending_reason);
//            $billingmethod->update(array('billing_options' => serialize($billing_options), 'transaction_state' => 'refunded'));
//
//            $billing_options->result->correlationID = urldecode($httpParsedResponseAr['CORRELATIONID']);
//            $this->createBillingTransaction($billingmethod, urldecode($httpParsedResponseAr['NETREFUNDAMT']), $billing_options->result, 'refunded');
//            flash('message', gt('Refund Completed Successfully.'));
//            redirect_to(array('controller' => 'order', 'action' => 'show', 'id' => $billingmethod->orders_id));
//        } else {
//            exit(gt('Refund Transaction failed') . ': ' . $httpParsedResponseAr["L_LONGMESSAGE0"]);
//        }
    }

//    function authorization($billingmethod, $opts, $order) {
//
//    }

//    function re_authorization($billingmethod, $opts, $order) {
//
//    }

    /**
     * Clean up after ourselves
     *
     * @return boolean
     */

    /**
     * Point to the location of the config template.
     *
     * @return string The location of the config.tpl
     * TODO: this is hard coded. why? needs to pick up this like a controller does
     */
//    function configForm() {
//        $form = BASE . 'framework/modules/ecommerce/billingcalculators/views/paypalExpressCheckout/configure.tpl';
//        return $form;
//    }

    /**
     * process config form
     *
     * @param mixed $values
     *
     * @return array
     */
    function parseConfig($values) {
        $config_vars = array(
            'incontext',
            'username',
            'password',
            'signature',
            'testmode',
            'testusername',
            'testpassword',
            'testsignature',
            'process_mode',
            'email_customer',
            'email_admin',
            'notification_addy'
        );
        foreach ($config_vars as $varname) {
            $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
        }

        return $config;
    }

    /**
     * This is called when a billing method is deleted. It can be used to clean up if you have any custom user_data storage.
     *
     * @param string $where
     *
     * @return bool
     */
    function delete($where = '') {
        return;
    }

    /**
     * A utility a call to Paypal's api CURL
     *
     * @param array $apiParams an Associative array of the name-value pairs that will be sent as url params to the paypal api
     *
     * @return array An associative array containing the PayPal response or a curl error.
     */
    function paypalApiCall($apiParams) {
        $config = expUnserialize($this->config);

        if ($config['testmode']) {
            // Testing

            /**
             * this is the server URL which you have to connect for submitting your API request
             *
             * @var string
             */
            $api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            flash('message', gt('This Transaction is in TEST MODE'));
        } else {
            // LIVE

            $api_endpoint = 'https://api-3t.paypal.com/nvp';
        }

        // convert the api params to a name value pair string
        $nvpstr = "";
        while (list($key, $value) = each($apiParams)) {
            $nvpstr .= $key . '=' . urlencode(str_replace(',', '', $value)) . '&';
        }

        // take the last & out for the string
        $nvpstr = substr($nvpstr, 0, -1);

        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //setting the nvpstr as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpstr);

        //getting response from server
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $ret = array(
                'curl_errno' => curl_errno($ch),
                'curl_error' => curl_error($ch),
            );
        } else {
            $ret = $this->deformatNVP($response);
        }

        curl_close($ch);

        return $ret;
    }

    /**
     * A utility function that will take the Name-Value-Pair string returned by PayPal API and create a multi-deminsional array matching up related data where appropriate
     * Note that this is Paypal speciific as we need to do some funky things to match related data.
     *
     * @param string $nvpstr The nvp string returned by paypal.
     *
     * @return array
     */
    function deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();

        while (strlen($nvpstr)) {
            //postion of Key
            $keypos = strpos($nvpstr, '=');
            //position of value
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }

        // now we'll group the related NVPs into their own arrays and make a multidimensional array out of the whole thing
        // Take note that the Key of the new array will be L_0, L_1... L_n The L_ forces the array to have a string index. 
        // If we let it have numeric indicies array_merge_recursive() wouldn't work as expected. Since "non-related data" is 
        // also in the multidimensional you'll be treating this thing as a associative array anyway so really this is easier 
        $multiArr = array();
        foreach ($nvpArray as $k => $v) {
            // check if it has a number at the end of the key
            if (preg_match('/[0-9]+$/', $k, $matches)) {
                // rip off the "L_" from the beginning of the key and the matched number from the end
                // make a new array using "l_(matched number)" as the index.
                // merge the new array to the multidimensional array
                $multiArr = array_merge_recursive($multiArr, array("L_$matches[0]" => array(preg_replace("/(L_)|$matches[0]/", "", $k) => $v)));
            } else {
                // if the key doesn't have a number at the end we don't need to do anythin special to try to match up any related data as above. 
                // Simply stick it on the multidimensional array
                $multiArr[$k] = $v;
            }
            //print_r($multiArr);
        }

        // array_merge_recursive() needed a string index to work. now we don't actually want an array index of "L_0, L_1... L_n" we just wan the number
        foreach ($multiArr as $k => $v) {
            if (preg_match('/L_/', $k, $matches)) {
                $multiArr[preg_replace('/L_/', "", $k)] = $v;
                unset($multiArr[$k]);
            }
        }

        return $multiArr;
    }

    function getPaymentAuthorizationNumber($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return isset($ret->result->token) ? $ret->result->token : '';
    }

    function getPaymentReferenceNumber($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        if (isset($ret->result)) {
//            return $ret->result->correlationID;
//            return $ret->result->transactionID;
            return isset($ret->result->transId) ? $ret->result->transId : '';
        } else {
//            return $ret->correlationID;
//            return $ret->transactionID;
            return isset($ret->transId) ? $ret->transId : '';
        }
    }

    function getPaymentStatus($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return isset($ret->result->payment_status) ? $ret->result->payment_status : '';
    }

    function getAVSAddressVerified($billingmethod) {
        return 'X';
    }

    function getAVSZipVerified($billingmethod) {
        return 'X';
    }

    function getCVVMatched($billingmethod) {
        return 'X';
    }

    /**
     * Send HTTP POST Request
     *
     * @param $methodName_
     * @param $nvpStr_
     *
     * @internal param \The $string API method name
     * @internal param \The $string POST Message fields in &name=value pair format
     * @return    array    Parsed HTTP Response body
     */  //FIXME Deprecated now in favor of above standard
    function PPHttpPost($methodName_, $nvpStr_) {
        $environment = 'sandbox';
        $config = expUnserialize($this->config);
        // Set up your API credentials, PayPal end point, and API version.
        $API_UserName = urlencode($config['username']);
        $API_Password = urlencode($config['password']);
        $API_Signature = urlencode($config['signature']);
        $API_Endpoint = "https://api-3t.paypal.com/nvp";
        if ("sandbox" === $environment || "beta-sandbox" === $environment) {
            $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
        }
        $version = urlencode(paypalExpressCheckout::PAYPAL_API_VERSION);

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $httpParsedResponseAr;
    }

}

?>