<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

class eventregistration extends expRecord {
	public $table = 'product';
	public $has_one = array();
	public $has_and_belongs_to_many = array('storeCategory');

    public $product_name = 'Event Registration';
    public $product_type = 'eventregistration';
    public $requiresShipping = false; 
	public $requiresBilling  = true; 
    public $isQuantityAdjustable = false;
	
	
	 protected $attachable_item_types = array(
        'content_expFiles'=>'expFile',
		'content_expDefinableFields'=>'expDefinableField'
    );
    
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
	    // eDebug($params, true);
	    // Save the event info to the eventregistration table	 
#	    $event = new expRecord();
#	    $event->tablename = 'eventregistration';
	    $event->eventdate = strtotime($params['eventdate']);
	    $event->event_starttime = datetimecontrol::parseData('event_starttime', $params) + $event->eventdate;
	    $event->event_endtime = datetimecontrol::parseData('event_endtime', $params) + $event->eventdate;
	    $event->signup_cutoff = strtotime($params['signup_cutoff']);
	    $event->id = empty($product->product_type_id) ? null : $product->product_type_id;
	    if (!empty($event->id)) { 
            $db->updateObject($event, 'eventregistration');
        } else {
            $event->id = $db->insertObject($event, 'eventregistration');
        }
	    
	    $params['product_type_id'] = $event->id;
		// eDebug($params, true);
	// $product->expFile= $params['expFile'];
	    parent::update($params);
	}

    public function spacesLeft() {
        return $this->quantity - $this->number_of_registrants;
    }
    
    public function cartSummary($item) {
        $view = new controllertemplate($this, $this->getForm('cartSummary'));
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
	
	function getBasePrice($orderitem=null) {
        if ($this->use_special_price) {
            return $this->special_price;
        } else {
            return $this->base_price;
        }
    }
	
	function getDefaultQuantity() {
		//TMP: Make this actually do something.
		return 1;
	}
	
	function getSurcharge() {        
        $sc = 0;
        //take parent level surcharge, but override surcharge child product is set            
        if($this->surcharge == 0 && $this->parent_id != 0)
        {            
            $parentProd = new product($this->parent_id);
            $sc = $parentProd->surcharge;            
        }
        else
        {            
            $sc = $this->surcharge;
        }
        //eDebug($sc);
        return $sc;
    }
    
    public function process($item) {   
        global $db, $order;     
        // save the names of the registrants to the eventregistration table too
        $product = new eventregistration($item->product_id);
        $registrants = expUnserialize($product->registrants);
        $order_registrations = expUnserialize($item->extra_data);
        $product->registrants = is_array($registrants)? array_merge($registrants, $order_registrations) : $order_registrations; //: array_merge($registrants, $order_registrations);

        // create an object to update the event table.
        $event = new stdClass();
        $event->id = $product->product_type_id;
        $event->number_of_registrants = count($product->registrants);
        $event->registrants = serialize($product->registrants);
        
		
	    $db->updateObject($event, 'eventregistration');
		// eDebug(expSession::get('expDefinableField'), true);
		foreach(expSession::get('expDefinableField') as  $key => $value) {
			$obj = new stdClass();
			$obj->expDefinableFields_id = $key;
			$obj->content_id            = $item->product_id;
			$obj->connector_id          = $order->id;
			$obj->content_type          = "eventregistration";
			$obj->value                 = $value;
			$db->insertObject($obj, 'content_expDefinableFields_value');
		}
		//add unset here

        return true;
    }
    /*
	function addToCart($params, $orderid = null) {
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
	*/
	function addToCart($params, $orderid = null) {
	    if (empty($params['base_price'])) {
	        return false;
	    } else {
	        $item = new orderitem($params);	        
	        $item->products_price = preg_replace("/[^0-9.]/","",$params['base_price']);
	        
	        $product = new product($params['product_id']);
	        $item->products_name = $product->title;

	        // we need to unset the orderitem's ID to force a new entry..other wise we will overwrite any
	        // other giftcards in the cart already
	        $item->id = null;
	        $item->quantity = $this->getDefaultQuantity();
		    $item->save();
		    return true;
	    }
	}
	
    public function isAvailable(){
	    return ($this->spacesLeft() !=0 && $this->signup_cutoff > time()) ? true : false;
    }
	
	public function getControl($field) {
		$id      = $field->id;
		$control = $field->data;
		$type    = $field->type;
		$c       = new $type();
		$ctl     = unserialize($control);
		// eDebug($ctl, true);
		return $ctl->toHTML($ctl->caption, "definablefields[$id]");
	}
	
	public function getForm($form) {        
        $dirs = array(
            BASE.'themes/'.DISPLAY_THEME.'/modules/ecommerce/products/views/'.$this->product_type.'/',
            BASE.'framework/modules/ecommerce/products/views/'.$this->product_type.'/',
            BASE.'themes/'.DISPLAY_THEME.'/modules/ecommerce/products/views/product/',
            BASE.'framework/modules/ecommerce/products/views/product/',
        );
        
        foreach ($dirs as $dir) {
            if (file_exists($dir.$form.'.tpl')) return $dir.$form.'.tpl';    
        }
        
        return false;
    }
	
}

?>