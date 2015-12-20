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
class billingmethod extends expRecord {
    public $table = 'billingmethods';

    public $has_one = array('billingcalculator');
    public $has_many = array('billingtransaction');
    public $get_assoc_for = array('billingtransaction');
    
    static public $payment_types = array (
                'VisaCard' => 'Visa',  
                'AmExCard' => 'American Express', 
                'MasterCard' => 'Mastercard', 
                'DiscoverCard' => 'Discover', 
                'paypalExpressCheckout' => 'PayPal', 
                'passthru' => 'Passthru', 
                'worldpayCheckout' => 'WorldPay',
                'cash' => 'Cash',
                'paylater' => 'Billed'
            );

    function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc, $get_attached);
//        $this->billingtransaction = array_reverse($this->billingtransaction);

        // unpack the billing_options data
        $this->billing_options = empty($this->billing_options) ? array() : unserialize($this->billing_options);
    }
     
	public function setAddress($address) {
		$address = is_numeric($address) ? new address($address) : $address;
		$this->addresses_id = isset($address->id) ? $address->id : '';
		unset($address->id);
		$this->update($address);
	}	
}

?>