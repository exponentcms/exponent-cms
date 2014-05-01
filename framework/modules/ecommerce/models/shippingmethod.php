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
class shippingmethod extends expRecord {
	public $table = 'shippingmethods';

	public static function getCurrentShippingMethod() {
        global $order;
        $smid = empty($order->orderitem[0]->shippingmethods_id) ? null : $order->orderitem[0]->shippingmethods_id;
        return new shippingmethod($smid);
    }

	public function setAddress($address) {
		$address = is_object($address) ? $address : new address($address);
		$this->addresses_id = $address->id;
		unset($address->id);
		$this->update($address);
	}	
	
	function afterSave() {
		$this->updateOrderitems($this->id);
	}
	
	function updateOrderitems() {
		global $order;
		// update the shippingmethod id for each orderitem..again, this is only here until we implement split shipping.
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
}

?>