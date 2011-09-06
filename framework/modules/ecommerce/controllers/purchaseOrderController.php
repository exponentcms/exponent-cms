<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class purchaseOrderController extends expController {
	//protected $basemodel_name = '';
	//public $useractions = array('showall'=>'Show all');
	public $useractions = array();
	protected $add_permissions = array(
	//'showall'=>'Manage', 
    // 'show'=>'View Orders', 
    // 'setStatus'=>'Change Status', 
    // 'edit_payment_info'=>'Edit Payment Info','save_payment_info'=>'Save Payment Info',
    // 'edit_address'=>'Edit Address','save_address'=>'Save Address',
    // 'edit_order_item'=>'Edit Order Item','save_order_item'=>'Save Order Item',
    // 'add_order_item'=>'Add Order Item','save_new_order_item'=>'Save New Order Item',
    // 'edit_totals'=>'Edit Totals','save_totals'=>'Save Totals',
    // 'edit_invoice_id'=>'Edit Invoice Id','save_invoice_id'=>'Save Invoice Id', 
    // 'update_sales_reps'=>'Manage Sales Reps', 'quickfinder'=>'Do a quick order lookup', 
    // 'edit_shipping_method'=>'Edit Shipping Method', 'save_shipping_method'=>'Save Shipping Method',    
    // 'create_new_order'=>'Create A New Order', 'save_new_order'=>'Save a new order', 
    // 'createReferenceOrder'=>'Create Reference Order', 'save_reference_order'=>'Save Reference Order'      
    );
	
	function displayname() { return "Ecommerce Purchase Order Manager"; }
	function description() { return "Use this module to create and manage purchase orders for your ecommerce store."; }
	
	function manage () {
	    global $db;
	    
	    expHistory::set('viewable', $this->params);
	}
    
	function manage_vendors () {
	    global $db;
	    
	    expHistory::set('viewable', $this->params);
	}
    
	function edit () {
	    global $db;
	    assign_to_template(array('record'=>$this->params));
	}
    
}

?>
