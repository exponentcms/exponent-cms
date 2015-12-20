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
 * @subpackage Models
 * @package Modules
 */
class billingcalculator extends expRecord {
    public $table = 'billingcalculator';
    public $configdata = array();
    function name() {return $this->$payment_type;}
    function hasUserForm() {return true;}
    function hasConfig() {return true;}
    /**
     * Is this billing calculator selectable in the payment methods. It may not be if it is meant more as base class for other calculators to extend from
     *
     * @return boolean
     */
    function isSelectable() {return true;}
    function isOffsite() {return false;}
    public function captureEnabled() {return false; }
    public function voidEnabled() {return false; }
    public function creditEnabled() {return false; }
    public function isRestricted() { return false; }

//    public $use_title = '';
    public $payment_type = '';

    public function __construct($params=null, $get_assoc=true, $get_attached=true) {        
        parent::__construct($params, $get_assoc, $get_attached);

        // set the calculator
        if (!empty($this->calculator_name))
            $this->calculator = new $this->calculator_name();
        
        // grab the config data for this calculator
        $this->configdata = empty($this->config) ? array() : unserialize($this->config);
    }

    /**
     * Called for billing method selection screen, return true if it's a valid billing method.
     *
     * @param $method
     * @param $opts
     * @param $params
     * @param $order
     */
    function preprocess($method, $opts, $params, $order) {
        return;
    }

    function process($method, $opts, $params, $order) {
        return null;
    }

    function postProcess($order,$params)
    {
         return true;
    }

    /*
     * Capture an authorized transaction
     */
    function delayed_capture($billingmethod, $amount , $order)
    {
    }

    /*
     * Void the remainder of an authorized transaction
     */
    function void_transaction($billingmethod, $order)
    {
    }

    /*
     * Refund an already captured transaction
     */
    function credit_transaction($method, $amount, $order)
    {
    }

    function userForm($config_object = null, $user_data = null) {
        return '';
    }

    /**
     * Should return html to display user data.
     *
     */
    function userView($billingmethod) {
//        return '';

        // create a generic table of data
        $billinginfo = '<table id="ccinfo"' . (bs3()?' class=" table"':'') . ' border=0 cellspacing=0 cellpadding=0>';
        $billinginfo .= '<thead><tr><th colspan="2">' . gt('Paying by') . ' ' . $this->name() . '</th></tr></thead>';
        $billinginfo .= '<tbody>';
        $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt("Payment Method") . ': </td><td class="pmt-value">' . $this->getPaymentMethod($billingmethod) . '</td></tr>';
        $billinginfo .= '<tr class="even"><td class="pmt-label">' . gt("Payment Status") . ': </td><td class="pmt-value">' . $this->getPaymentStatus($billingmethod) . '</td></tr>';
        $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt("Payment Authorization #") . ': </td><td class="pmt-value">' . $this->getPaymentAuthorizationNumber($billingmethod) . '</td></tr>';
        $billinginfo .= '<tr class="even"><td class="pmt-label">' . gt("Payment Reference #") . ': </td><td class="pmt-value">' . $this->getPaymentReferenceNumber($billingmethod) . '</td></tr>';
        $data = $this->getAVSAddressVerified($billingmethod) . $this->getAVSZipVerified($billingmethod) . $this->getCVVMatched($billingmethod);
        if  (!empty($data)) {
            $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt("AVS Address Verified") . ': </td><td class="pmt-value">' . $this->getAVSAddressVerified($billingmethod) . '</td></tr>';
            $billinginfo .= '<tr class="even"><td class="pmt-label">' . gt("AVS ZIP Verified") . ': </td><td class="pmt-value">' . $this->getAVSZipVerified($billingmethod) . '</td></tr>';
            $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt("CVV # Matched") . ': </td><td class="pmt-value">' . $this->getCVVMatched($billingmethod) . '</td></tr>';
        }
        $billinginfo .= '</tbody>';
        $billinginfo .= '</table>';
        return $billinginfo;
    }

    /**
     * process user input. This function should return an object of the user input.
     * the returned object will be saved in the session and passed to post_process.
     * If need be this could use another method of data storage, as long post_process can get the data.
     *
     * @param $params
     */
    function userFormUpdate($params) {
    }

    function configForm() {
        if (bs3(true)) {
            $tpl = 'configure.bootstrap3.tpl';
            if (!file_exists(BASE . 'framework/modules/ecommerce/billingcalculators/views/' . $this->calculator_name . '/' . $tpl)) {
                $tpl = 'configure.tpl';
            }
        } else {
            $tpl = 'configure.tpl';
        }
        return BASE . 'framework/modules/ecommerce/billingcalculators/views/' . $this->calculator_name . '/' . $tpl;
    }

    function parseConfig($values) {
        return array();
    }

    function getPaymentAuthorizationNumber($billingmethod) {
        return '';
    }

    function getPaymentReferenceNumber($billingmethod) {
        return '';
    }

    function getPaymentStatus($billingmethod) {
        return '';
    }

    function getPaymentMethod($billingmethod) {
        return $this->name();
    }

    function getAVSAddressVerified($billingmethod) {
        return '';
    }

    function getAVSZipVerified($billingmethod) {
        return '';
    }

    function getCVVMatched($billingmethod) {
        return '';
    }

    function showOptions() {
        return;
    }

    function createBillingTransaction($method, $amount, $result, $trax_state)
    {
        $bt = new billingtransaction();
        $bt->billingmethods_id = $method->id;
        $bt->billingcalculator_id = $method->billingcalculator_id;
        $bt->billing_cost = $amount;
        $bt->billing_options  = serialize($result);  //FIXME this is only the 'results' property unlike $bm???
        $bt->extra_data = '';  //FIXME what is this used for?
        //FIXME we need a transaction_state of complete, authorized, authorization pending, error, void, or refunded; or paid or payment due
        $bt->transaction_state = $trax_state;
        //$bt->result = $result;    
        $bt->save();
    }
    
    /**
     * Return default billing calculator
     *
     */
    public static function getDefault() {
        global $db;

        $calc = $db->selectObject('billingcalculator','is_default=1');
        if (empty($calc)) $calc = $db->selectObject('billingcalculator','enabled=1');
        if ($calc->id) return $calc->id;
        else return false;
    }

    /**
     * Return billing calculator name
     *
     */
    public static function getCalcName($calc_id) {
        global $db;

        return $db->selectValue('billingcalculator','calculator_name','id='.$calc_id);
    }

    /**
     * Return billing calculator title
     *
     */
    public static function getCalcTitle($calc_id) {
        global $db;

        return $db->selectValue('billingcalculator','title','id='.$calc_id);
    }

}

?>