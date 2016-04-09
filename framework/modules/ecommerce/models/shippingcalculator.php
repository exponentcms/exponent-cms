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

    public function meetsCriteria($shippingmethod) { //FIXME probably needs to be passed order object
        return true;
    }

    public function getRates($order)
    {
        return array();
    }

    public function configForm()
    {
        if (bs3(true)) {
            $tpl = 'configure.bootstrap3.tpl';
            if (!file_exists(BASE . 'framework/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/' . $tpl)) {
                $tpl = 'configure.tpl';
            }
        } else {
            $tpl = 'configure.tpl';
        }
        return BASE . 'framework/modules/ecommerce/shippingcalculators/views/' . $this->calculator_name . '/' . $tpl;
    }

    function parseConfig($values)
    {
        return array();
    }

    function availableMethods() {
   	    return array();
   	}

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
     */
    public static function getDefault() {
        global $db;

        $calc = $db->selectObject('shippingcalculator','is_default=1');
        if (empty($calc)) $calc = $db->selectObject('shippingcalculator','enabled=1');
        if (!empty($calc->id)) return $calc->id;
        else return false;
    }

    /**
     * Return shipping calculator name
     *
     */
    public static function getCalcName($calc_id) {
        global $db;

        return $db->selectValue('shippingcalculator','calculator_name','id='.$calc_id);
    }

    /**
     * Return shipping calculator title
     *
     */
    public static function getCalcTitle($calc_id) {
        global $db;

        return $db->selectValue('shippingcalculator','title','id='.$calc_id);
    }

    // functions for handing order fulfillment via shipping labels and package pickup
    //  primarily for easypost shipping calculator

    function createLabel($shippingmethod) {
        return false;
    }

    function buyLabel($shippingmethod) {

    }

    function getLabel($shippingmethod) {
        return false;
    }

    function cancelLabel($shippingmethod) {

    }

    function createPickup($shippingmethod, $pickupdate, $pickupenddate, $instructions) {
        return false;
    }

    function buyPickup($shippingmethod, $type) {

    }

    function cancelPickup($shippingmethod) {

    }

    function handleTracking() {

    }

    function getPackageDetails($shippingmethod, $tracking_only=false) {

    }

}

?>