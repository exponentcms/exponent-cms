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

/**
 * @subpackage Models
 * @package    Modules
 */
class shippingcalculator extends expRecord {
    public $table = 'shippingcalculator';
    public $icon = '';
    public $configdata = array();
    public $multiple_carriers = false;

//    public function hasUserForm() { return true; }
   	public function hasConfig() { return true; }
   	public function addressRequired() { return true; }
   	public function isSelectable() { return true; }
    public function labelsEnabled() {return false; }
    public function pickupEnabled() {return false; }
    public function trackerEnabled() {return false; }

    public $shippingmethods = array();

    public function __construct($params = null, $get_assoc = true, $get_attached = true) {
        parent::__construct($params, $get_assoc, $get_attached);

        // grab the config data for this calculator
        $this->configdata = empty($this->config) ? array() : unserialize($this->config);

        if (file_exists(BASE . 'framework/modules/ecommerce/shippingcalculators/icons/' . $this->classname . '.gif')) {
            $this->icon = PATH_RELATIVE . 'framework/modules/ecommerce/shippingcalculators/icons/' . $this->classname . '.gif';
        } else {
            $this->icon = PATH_RELATIVE . 'framework/modules/ecommerce/shippingcalculators/icons/default.png';
        }

    }

    /**
     * Does calculator meet criteria
     *
     * @param $shippingmethod
     *
     * @return bool
     */
    public function meetsCriteria($shippingmethod) { //FIXME probably needs to be passed order object
        return true;
    }

    /**
     * Main function to return (all possible) shipping rates for order
     *
     * @param $order
     *
     * @return array
     */
    public function getRates($order)
    {
        return array();
    }

    /**
     * Return path to calculator configuration form template
     *
     * @return string
     */
    public function configForm()
    {
        if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/configure.tpl')) {
            return BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/configure.tpl';
        } else {
            if (bs5() && file_exists(BASE . 'framework/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/' . 'configure.bootstrap5.tpl')) {
                $tpl = 'configure.bootstrap5.tpl';
            } elseif ((bs4() || bs5()) && file_exists(BASE . 'framework/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/' . 'configure.bootstrap4.tpl')) {
                $tpl = 'configure.bootstrap4.tpl';
            } elseif ((bs3(true) || bs4() || bs5()) && file_exists(BASE . 'framework/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/' . 'configure.bootstrap3.tpl')) {
                $tpl = 'configure.bootstrap3.tpl';
            } else {
                $tpl = 'configure.tpl';
            }
            return BASE . 'framework/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/' . $tpl;
        }
    }

    /**
     * Parse configuration data into an array
     * @param $values
     *
     * @return array
     */
    function parseConfig($values)
    {
        return array();
    }

    /**
     * Return array of available shipping method names
     *
     * @return array
     */
    function availableMethods() {
   	    return array();
   	}

    /**
     * Return array of packages
     *
     * @param $carrier
     *
     * @return array
     */
    function getPackages($carrier) {
        return array();
    }

    /**
     * Unused at this time
     *
     * @return int
     */
    public function getHandling() {
        return 0;
    }

    /**
     * Return default shipping calculator
     *
     * @return int
     */
    public static function getDefault() {
        global $db;

        $calc = $db->selectObject('shippingcalculator','is_default=1');
        if (empty($calc)) $calc = $db->selectObject('shippingcalculator','enabled=1');
        if (!empty($calc->id))
            return $calc->id;
        else
            return false;
    }

    /**
     * Return shipping calculator name
     *
     * @param $calc_id
     *
     * @return string
     */
    public static function getCalcName($calc_id) {
        global $db;

        return $db->selectValue('shippingcalculator','calculator_name','id='.$calc_id);
    }

    /**
     * Return shipping calculator title
     *
     * @param $calc_id
     *
     * @return string
     */
    public static function getCalcTitle($calc_id) {
        global $db;

        return $db->selectValue('shippingcalculator','title','id='.$calc_id);
    }

    // functions for handing order fulfillment via shipping labels and package pickup
    //  primarily for easypost shipping calculator

    function createLabel($shippingmethod) {
        return;
    }

    function buyLabel($shippingmethod) {

    }

    function getLabel($shippingmethod) {
        return false;
    }

    function cancelLabel($shippingmethod) {

    }

    function createPickup($shippingmethod, $pickupdate, $pickupenddate, $instructions) {

    }

    function buyPickup($shippingmethod, $type) {

    }

    function cancelPickup($shippingmethod) {

    }

    function handleTracking() {

    }

    function getPackageDetails($shippingmethod, $tracking_only=false) {
        return '';
    }

}

?>