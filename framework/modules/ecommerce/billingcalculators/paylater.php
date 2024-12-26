<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

/** @define "BASE" "../../../.." */
/**
 * @subpackage Calculators
 * @package    Modules
 */
class paylater extends billingcalculator {

    function name() {
        return gt('Bill Me');
    }

//    public $use_title = 'Bill Me';
    public $payment_type = 'Billed';

    function description() {
        return gt("Enabling this payment option will allow your customers to pay when picking up purchase.");
    }

    function hasConfig() {
        return false;
    }

    function hasUserForm() {
        return false;
    }

    //Called for billing method selection screen, return true if it's a valid billing method.
    function preprocess($billingmethod, $opts, $params, $order) {
        if ($opts->cash_amount < $order->grand_total)
            $opts->payment_due = $order->grand_total - $opts->cash_amount;
        //just save the opts
        $billingmethod->update(array('billing_options' => serialize($opts)));
    }

//    function process($billingmethod, $opts, $params, $invoice_number) {
    function process($billingmethod, $opts, $params, $order) {
        $opts = expUnserialize($billingmethod->billing_options);  //FIXME why aren't we passing $opts?
//        $object->errorCode = $opts->result->errorCode = 0;
        if (!isset($opts->result)) {
            $opts->result = new stdClass();
        }
        $opts->result->errorCode = 0;
//        $opts->result = $object;
        $opts->result->payment_status = gt("complete");
        $opts->result->transId = '';
        $opts->result->message = "User will pay later";
        if ($opts->cash_amount < $order->grand_total)
            $opts->result->payment_status = gt("payment due");
//        $billingmethod->update(array('billing_options' => serialize($opts),'transaction_state'=>$opts->result, $opts->result->payment_status));
        $billingmethod->update(array('billing_options' => serialize($opts), 'transaction_state' => $opts->result->payment_status));
        $this->createBillingTransaction($billingmethod, number_format(0, 2, '.', ''), $opts->result, $opts->result->payment_status);
        return $opts;
    }

    function userForm($config_object = null, $user_data = null) {
        $form = parent::userForm();

        $form .= '<h4>' . gt('Pay for this purchase later.') . '</h4>';

        $cash_amount = new hiddenfieldcontrol(0, 20, false, 20, "money", true);
        $cash_amount->filter = 'money';
        $cash_amount->id = "cash_amount";
        $form .= $cash_amount->toHTML(gt("Cash Amount"), "cash_amount");

        return $form;
    }

    //Should return html to display user data.
    function userView($billingmethod) {
        $opts = expUnserialize($billingmethod->billing_options);
        if (empty($opts))
            return false;
        $cash = !empty($opts->cash_amount) ? $opts->cash_amount : 0;
//        $billinginfo = gt("Paying Later") . ": " . expCore::getCurrencySymbol() . number_format($cash, 2, ".", ",");
//        if (!empty($opts->payment_due)) {
//            $billinginfo .= '<br>' . gt('Payment Due') . ': ' . expCore::getCurrencySymbol() . number_format($opts->payment_due, 2, ".", ",");
//        }
//        return $billinginfo;

        $billinginfo = '<table id="ccinfo"' . ((bs3()||bs4()||bs5())?' class="table"':'') . ' border=0 cellspacing=0 cellpadding=0>';
        $billinginfo .= '<thead><tr><th colspan="2">' . gt("Paying Later") . '</th></tr></thead>';
        $billinginfo .= '<tbody>';
        $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt("Payment Method") . '</td><td class="pmt-value">' . $this->getPaymentMethod($billingmethod) . '</td></tr>';
//        $billinginfo .= '<tr class="even"><td class="pmt-label">' . gt("Payment Status") . ': </td><td class="pmt-value">' . $this->getPaymentStatus($billingmethod) . '</td></tr>';
        $billinginfo .= '<tr class="even"><td class="pmt-label">' . gt("Amount Paid") . ': </td><td class="pmt-value">' . expCore::getCurrencySymbol() . number_format($cash, 2, ".", ",") . '</td></tr>';
        if  (!empty($opts->payment_due)) {
            $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt("Amount Due") . '</td><td class="pmt-value">' . expCore::getCurrencySymbol() . number_format($opts->payment_due, 2, ".", ",") . '</td></tr>';
        }
        $billinginfo .= '</tbody>';
        $billinginfo .= '</table>';
        return $billinginfo;
    }

    function userFormUpdate($params) {
//        global $order;

        if (substr($params['cash_amount'], 0, strlen(expCore::getCurrencySymbol())) == expCore::getCurrencySymbol()) {
            $params['cash_amount'] = substr($params['cash_amount'], strlen(expCore::getCurrencySymbol()));
        }
        // force full payment prior to checkout
//        if (expUtil::isNumberGreaterThan($order->grand_total, (float)($params["cash_amount"]), 2)) {
//            expValidator::failAndReturnToForm(gt("The total amount of your order is greater than the amount you have input.") . "<br />" . gt("Please enter exact or greater amount of your total."));
//        }
        $this->opts = new stdClass();
        $this->opts->cash_amount = $params["cash_amount"];
        return $this->opts;
    }

//    function getPaymentAuthorizationNumber($billingmethod) {
//        $ret = expUnserialize($billingmethod->billing_options);
//        return $ret->result->token;
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

}

?>