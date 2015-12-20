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
class shippingmethod extends expRecord {
	public $table = 'shippingmethods';

    public $has_many = array('orderitem');  //FIXME does this fix the situation??

    function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc, $get_attached);

        // unpack the shipping_options data
        $this->shipping_options = empty($this->shipping_options) ? array() : expUnserialize($this->shipping_options);
    }

//	public static function getCurrentShippingMethod() {
//        global $order;
//
//        $smid = empty($order->orderitem[0]->shippingmethods_id) ? null : $order->orderitem[0]->shippingmethods_id;
//        return new shippingmethod($smid);
//    }

	public function setAddress($address) {
		$address = is_object($address) ? $address : new address($address);
		$this->addresses_id = $address->id;
		unset($address->id);
		$this->update($address);
	}	
	
	function afterSave() {
		$this->updateOrderitems($this->id);  //FIXME this has a global $order
	}
	
	function updateOrderitems() {
		global $order; //FIXME we do NOT want the global $order

		//FIXME update the shippingmethod id for each orderitem..again, this is only here until we implement split shipping.
        // once we have that we'll need to figure out which orderitems get which shippingmethod id.     
        foreach($order->orderitem as $item) {
            if (empty($item->shippingmethods_id)) {
                $item->update(array('shippingmethods_id'=>$this->id));
            }
        }
	}
	
	function requiresShipping($order) {
	    //global $order;
	    $orderitem = new orderitem();
        $items = $orderitem->find('all', 'orders_id='.$order->id." AND shippingmethods_id=".$this->id);
        foreach ($items as $item) {
            if ($item->product->requiresShipping) {
                return true;
            }
        }
        return false;
	}

    function attachCalculator() {
        global $db;

        $calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id='.$this->shippingcalculator_id);
        if (!empty($calcname))
            $this->calculator = new $calcname($this->shippingcalculator_id);
    }

}

?>