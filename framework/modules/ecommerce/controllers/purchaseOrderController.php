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
 * @subpackage Controllers
 * @package Modules
 */

class purchaseOrderController extends expController {
	public $basemodel_name = 'purchase_order';
	protected $manage_permissions = array(
        'show_vendor'=>'Show Vendor Details',
    );

    static function displayname() { return gt("e-Commerce Purchase Order Manager"); }
    static function description() { return gt("Use this module to create and manage purchase orders for your ecommerce store."); }

	function manage () {
	    expHistory::set('viewable', $this->params);

		$vendor = new vendor();
		$vendors = $vendor->find('all');
		if(!empty($this->params['vendor'])) {
			$purchase_orders = $this->purchase_order->find('all', 'vendor_id=' . $this->params['vendor']);
		} else {
			$purchase_orders = $this->purchase_order->find('all');
		}

		assign_to_template(array(
            'purchase_orders'=>$purchase_orders,
            'vendors' => $vendors,
            'vendor_id' => @$this->params['vendor']
        ));
	}

	function edit () {
//	    global $db;
	    assign_to_template(array(
            'record'=>$this->params
        ));
	}

	function manage_vendors () {
	    expHistory::set('viewable', $this->params);
		$vendor = new vendor();

		$vendors = $vendor->find('all');
		assign_to_template(array(
            'vendors'=>$vendors
        ));
	}

	function show_vendor () {
		$vendor = new vendor();

		if(isset($this->params['id'])) {
			$vendor = $vendor->find('first', 'id =' .$this->params['id']);
			$vendor_title = $vendor->title;
			$state = new geoRegion($vendor->state);
			$vendor->state = $state->name;
			//Removed unnecessary fields
			unset(
                $vendor->title,
                $vendor->table,
                $vendor->tablename,
                $vendor->classname,
                $vendor->identifier
            );

			assign_to_template(array(
                'vendor_title' => $vendor_title,
                'vendor'=>$vendor
            ));
		}
	}

	function edit_vendor() {
		$vendor = new vendor();

		if(isset($this->params['id'])) {
			$vendor = $vendor->find('first', 'id =' .$this->params['id']);
			assign_to_template(array(
                'vendor'=>$vendor
            ));
		}
	}

	function update_vendor() {
		$vendor = new vendor();

		$vendor->update($this->params['vendor']);
        expHistory::back();
    }

	function delete_vendor() {
		global $db;

        if (!empty($this->params['id'])){
			$db->delete('vendor', 'id =' .$this->params['id']);
		}
        expHistory::back();
    }

	public function getPurchaseOrderByJSON() {

		if(!empty($this->params['vendor'])) {
			$purchase_orders = $this->purchase_order->find('all', 'vendor_id=' . $this->params['vendor']);
		} else {
			$purchase_orders = $this->purchase_order->find('all');
		}

		echo json_encode($purchase_orders);
	}

}

?>