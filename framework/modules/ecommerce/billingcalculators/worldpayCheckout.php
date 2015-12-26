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

//TODO: make into php5 class with access modifiers properties and all that jazz.
class worldpayCheckout extends billingcalculator {
    
    /**
     * The name that will be displayes in the payment methods selector admin screen.
     *
     * @return string Then name of the billing calculator
     */
    function name() {
        return gt('Worldpay Payment Gateway');
    }

//    public $use_title = 'Worldpay Payment Gateway';
    public $payment_type = 'Worldpay';

    /**
     * The description that will be displayed in the payment methods selector admin screen
     *
     * @return string A short description
     */
    function description() {
        return gt("Enabling this payment option will allow your customers to use their worldpay account to make purchases.");
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

    function preprocess($billingmethod, $opts, $params, $order) {
        if (!isset($params['transStatus'])) {
            // make sure we have some billing options saved.
            if (empty($billingmethod)) {
                return false;
            }

            $config = unserialize($this->config);
            $worldpay_url = 'https://secure-test.worldpay.com/wcc/dispatcher';

            if (isset($config['testmode'])) {
                $testmode = 100;
            } else {
                $testmode = 0;
            }

//			if (isset($config['authCurrency'])) {
//				$authCurrency = $config['authCurrency'];
//			} else {
//				$authCurrency = "USD";
//			}
            $authCurrency = ECOM_CURRENCY;

            $data = array(
                // required parameters
                'testMode'    => $testmode,
                'instId'      => $config['installationid'],
                'amount'      => number_format($order->grand_total, 2, '.', ''),
                'currency'    => $authCurrency,
                'cartId'      => $order->id,
                'MC_callback' => URL_FULL . 'external/worldpay/callback.php'
            );
            // convert the api params to a name value pair string
            $datapost = "";
            while (list($key, $value) = each($data)) {
                $datapost .= $key . '=' . urlencode(str_replace(',', '', $value)) . '&';
            }

            // take the last & out for the string
            $datapost = substr($datapost, 0, -1);
            $url = $worldpay_url . '?' . $datapost;
            header('location: ' . $url);
            exit();
        } else {
            $opts = expUnserialize($billingmethod->billing_options);  //FIXME already unserialized?? == $opts???
            if ($params['transStatus'] == 'Y') {
                $opts->result->errorCode = 0;
                $opts->result->message = "User has approved the payment at Worldpay";
                $opts->result->transId = $params['transId'];
                $opts->result->payment_status = "Pending";
//                $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => "Pending"));
                $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => "complete"));
                $this->createBillingTransaction($billingmethod, number_format($order->grand_total, 2, '.', ''), $opts->result, 'complete');  //FIXME is 'complete' and $grand_total proper?
                redirect_to(array('controller' => 'cart', 'action' => 'process'));
            } else {
                redirect_to(array('controller' => 'cart', 'action' => 'checkout'), true);
            }
        }
    }

//    function process($billingmethod, $opts, $params, $invoice_number) {
    function process($billingmethod, $opts, $params, $order) {

    }

//    function configForm() {
//        $form = BASE . 'framework/modules/ecommerce/billingcalculators/views/worldpayCheckout/configure.tpl';
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
//	    $config_vars = array('username', 'password', 'installationid', 'authCurrency', 'testmode', 'email_customer', 'email_admin', 'notification_addy');
        $config_vars = array('username', 'password', 'installationid', 'testmode', 'email_customer', 'email_admin', 'notification_addy');
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

    function getAVSZipVerified($billingmethod) {
        return 'X';
    }

    function getCVVMatched($billingmethod) {
        return 'X';
    }

}

?>