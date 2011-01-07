<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Created by Adam Kessler @ 09/06/2007
#
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

class eventregistration extends product {
	public $table = 'product';
	public $has_one = array();
	public $has_and_belongs_to_many = array('storeCategory');

    public $product_name = 'Event Registration';
    public $product_type = 'eventregistration';
    public $requiresShipping = false; 
	public $requiresBilling  = true; 
    public $isQuantityAdjustable = false;
    
	public function __construct($params=array(), $get_assoc=true, $get_attached=true) {
		parent::__construct($params, $get_assoc, $get_attached);		
		
		// trick this record into looking to the eventregistration table
		// and adding it to our data as if it were in the product table with the rest
		// of the product data.
	    $origid = $this->id;  // save the id from the product table
	    $this->table = $this->product_type;	    
    	parent::__construct($this->product_type_id, false, false);
    	$this->id = $origid; // put the product table id back.
    	$this->table = 'product';
    	$this->tablename = 'product';    	
	}
	
	public function update($params=array()) {	    
	    global $db;
	    
	    if (isset($params['id'])) {
	        $product = new product($params['id']);
	    }
	    
	    // Save the event info to the eventregistration table	 
#	    $event = new expRecord();
#	    $event->tablename = 'eventregistration';
	    $event->eventdate = datetimecontrol::parseData('eventdate', $params);
	    $event->event_starttime = datetimecontrol::parseData('event_starttime', $params) + $event->eventdate;
	    $event->event_endtime = datetimecontrol::parseData('event_endtime', $params) + $event->eventdate;
	    $event->signup_cutoff = datetimecontrol::parseData('signup_cutoff', $params);
	    $event->id = empty($product->product_type_id) ? null : $product->product_type_id;
	    if (!empty($event->id)) { 
            $db->updateObject($event, 'eventregistration');
        } else {
            $event->id = $db->insertObject($event, 'eventregistration');
        }
	    
	    $params['product_type_id'] = $event->id;
	    
	    parent::update($params);
	}

    public function spacesLeft() {
        return $this->quantity - $this->number_of_registrants;
    }
    
    public function cartSummary($item) {
        $view = new controllerTemplate($this, $this->getForm('cartSummary'));
	    $view->assign('product', $this);
	    $view->assign('item', $item);
	    
	    // grab all the registrants
	    $registrants = expUnserialize($item->extra_data);
	    
	    //assign the number registered to the view
	    $number = count($registrants);
	    $view->assign('number', $number);
	    
	    // assign the list of names to the view.
	    foreach ($registrants as $reg){
		$people .= $reg['name'] . ',';
	    }
	    $people = substr($people, 0, -1);
	    $view->assign('people', $people);
        return $view->render('cartSummary');
    }
    
    public function process($item) {   
        global $db;     
        // save the names of the registrants to the eventregistration table too
        $product = new eventregistration($item->product_id);
        $registrants = expUnserialize($product->registrants);
        $order_registrations = expUnserialize($item->extra_data);
        $product->registrants = is_array($registrants)? array_merge($registrants, $order_registrations) : $order_registrations; //: array_merge($registrants, $order_registrations);

        // create an object to update the event table.
        $event = null;
        $event->id = $product->product_type_id;
        $event->number_of_registrants = count($product->registrants);
        $event->registrants = serialize($product->registrants);
        
	    $db->updateObject($event, 'eventregistration');
        return true;
    }
    
	function addToCart($params) {
	    global $db, $order;	    
	    if (isset($params['registrants'])) {	        
	        // save the order item	        
		    for($x=0; $x<count($params['registrants']); $x++){
			     $ed[$x]['name']= $params['registrants'][$x];
			     $ed[$x]['email']= $params['registrant_emails'][$x];
			     $ed[$x]['phone']= $params['registrant_phones'][$x];
		    }
		    
		    // if the item is in the cart already use it, if not we'll create a new one
		    $item = $order->isItemInCart($params['product_id'], $params['product_type']);		    
		    if (empty($item->id)) $item = new orderitem($params);
		    
		    // if we already have this event in our cart then we need to merge the registrants
		    $registrants = array();
		    if (!empty($item->extra_data)) $registrants = expUnserialize($item->extra_data);		    
		    $registrants = array_merge($registrants, $ed);
	        $item->quantity = count($registrants);
	        $item->extra_data = serialize($registrants);
	        $item->save();
	        return true;
	    } else {
	        return false;
	    } 
	}
	
    public function isAvailable(){
	    return ($this->spacesLeft() !=0 && $this->signup_cutoff > time()) ? true : false;
    }
	
}
?>
