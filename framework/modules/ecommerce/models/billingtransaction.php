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
class billingtransaction extends expRecord {
    public $has_one = array('billingcalculator'); 
	public $table = 'billingtransactions';

    public function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc, $get_attached);

        // unpack the billing_options data
        $this->billing_options = empty($this->billing_options) ? array() : expUnserialize($this->billing_options);
    }

    public function captureEnabled() 
    {
        return $this->billingcalculator->calculator->captureEnabled(); 
    }
    
    public function voidEnabled() 
    {
        return $this->billingcalculator->calculator->voidEnabled();
    }
    
    public function creditEnabled() 
    {
        return $this->billingcalculator->calculator->creditEnabled();
    }
    
    public function getRefNum()
    {
        //$opts = expUnserialize($this->billing_options);
        //return $opts->PNREF;
        if (empty($this->billingcalculator->calculator))
            $this->billingcalculator->calculator = new $this->billingcalculator->classname();
        $billingmethod = new billingmethod($this->billingmethods_id);
//        return $this->billingcalculator->calculator->getPaymentReferenceNumber($this->billing_options);
        return $this->billingcalculator->calculator->getPaymentReferenceNumber($billingmethod);
    }
}

?>