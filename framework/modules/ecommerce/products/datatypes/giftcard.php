<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Created by Adam Kessler @ 09/06/2007
#
# This file is part of Acorn Web API
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

class giftcard extends expRecord {
	public $table = 'product';
	public $has_one = array('company');
	public $has_and_belongs_to_many = array('storeCategory');

    public $product_name = 'Gift Card';
    public $product_type = 'giftcard';
    public $requiresShipping =true; 
	public $requiresBilling = true; 
    public $isQuantityAdjustable = true;
    
	protected $attachable_item_types = array(
        'content_expFiles'=>'expFile', 
    );
	
	public function __construct($params=array(), $get_assoc=true, $get_attached=true) {
		parent::__construct($params, $get_assoc, $get_attached);
		$this->price = '';
	}

    public function cartSummary($item) {
        $view = new controllertemplate($this, $this->getForm('cartSummary'));
	    $view->assign('product', $this);
	    $view->assign('item', $item);
	    
	    // grab all the registrants
	    $message = expUnserialize($item->extra_data);
	    $view->assign('message', $message);
	    
        return $view->render('cartSummary');
    }
    
	function getPrice($orderitem=null) {
		return 1;
	}
	
	function addToCart($params) {
	    global $order;
	    
	    if (empty($params['dollar_amount'])) {
	        return false;
	    } else {
	        $item = new orderitem($params);	        
	        $sm = $order->getCurrentShippingMethod();
	        
	        $item->shippingmethods_id = $sm->id;
	        $item->products_price = preg_replace("/[^0-9.]/","",$params['dollar_amount']);
	        $item->products_name = $params['dollar_amount'].' '.$this->product_name;
	        $ed['to'] = isset($params['to']) ? $params['to'] : '';
	        $ed['from'] = isset($params['from']) ? $params['from'] : '';
	        $ed['msg'] = isset($params['msg']) ? $params['msg'] : '';
	        $item->extra_data = serialize($ed);

	        // we need to unset the orderitem's ID to force a new entry..other wise we will overwrite any
	        // other giftcards in the cart already
	        $item->id = null;
	        $item->quantity = $this->getDefaultQuantity();
		    $item->save();
		    return true;
	    }
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
	
	public function update($params=array()) {
		// eDebug($params, true);
		parent::update($params); 
	}
}
?>
