<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
class billingtransaction extends expRecord {
    public $has_one = array('billingcalculator'); 
	public $table = 'billingtransactions';
    
    public function captureEnabled() 
    {
        return $this->billingcalculator->calculator->captureEnabled(); 
    }
    
    public function voidEnabled() 
    {
        return $this->billingcalculator->calculator->captureEnabled();
    }
    
    public function creditEnabled() 
    {
        return $this->billingcalculator->calculator->captureEnabled(); 
    }
    
    public function getRefNum()
    {
        //$opts = expUnserialize($this->billing_options);
        //return $opts->PNREF;
        if (empty($this->billingcalculator->calculator)) $this->billingcalculator->calculator = new $this->billingcalculator->classname();
        return $this->billingcalculator->calculator->getPaymentReferenceNumber($this->billing_options);
    }
}

?>