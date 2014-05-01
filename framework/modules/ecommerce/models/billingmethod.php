<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * @package Core
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

    /*function __construct($params=null, $get_assoc=true, $get_attached=true) {
        global $db;
        parent::__construct(null, true,true);
        eDebug($this);
        $this->billingtransaction = array_reverse($this->billingtransaction);    
    }*/
     
	public function setAddress($address) {
		$address = is_numeric($address) ? new address($address) : $address;
		$this->addresses_id = isset($address->id) ? $address->id : '';
		unset($address->id);
		$this->update($address);
	}	
}

?>