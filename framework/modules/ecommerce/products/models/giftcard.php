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
 * @package Modules
 */

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
//        'content_expCats'=>'expCat',
//        'content_expComments'=>'expComment',
//        'content_expDefinableFields'=> 'expDefinableField',
        'content_expFiles'=>'expFile',
//        'content_expRatings'=>'expRating',
//        'content_expSimpleNote'=>'expSimpleNote',
//        'content_expTags'=>'expTag',
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
		expSession::set('params', $params);
		//get the configuration
        $cfg = new stdClass();
		$cfg->mod = "ecomconfig";
        $cfg->src = "@globalstoresettings";
        $cfg->int = "";
        $config = new expConfig($cfg);
        $this->config = (empty($catConfig->config) || @$catConfig->config['use_global']==1) ? $config->config : $catConfig->config;  //FIXME $catConfig doesn't exist
		$min_amount = $this->config['minimum_gift_card_purchase'];
		$custom_message_product = $this->config['custom_message_product'];
	    
		if(empty($params['product_id'])) {
			flash('error', gt("Please specify the style of the gift card you want to purchase."));
			expHistory::back();
		}

	    if (empty($params['card_amount']) && empty($params['card_amount_txt'])) {
				flash('error', gt("You need to specify the card amount for the gift card."));
				expHistory::back();
	    } else {
			// eDebug($params, true);
	        $item = new orderitem($params);	        
	        $sm = $order->getCurrentShippingMethod();
	        
	        $item->shippingmethods_id = $sm->id;
			
			if(isset($params['card_amount_txt'])) {
				$params['card_amount_txt'] = preg_replace("/[^0-9.]/","",$params['card_amount_txt']);
			}
			
			if(!empty($params['card_amount_txt']) && $params['card_amount_txt'] > 0) {
				$item->products_price = preg_replace("/[^0-9.]/","",$params['card_amount_txt']);
			} else {
				$item->products_price = preg_replace("/[^0-9.]/","",$params['card_amount']);
			}
			
			if($item->products_price < $min_amount) {
				flash('error', gt("The minimum amount of gift card is")." ".$min_amount);
				expHistory::back();
			}
			
	        $item->products_name = expCore::getCurrencySymbol() . $params['card_amount'].' '. $this->title . " Style Gift Card";
			
			if(!empty($params['toname'])) {
				$ed['To'] = isset($params['toname']) ? $params['toname'] : '';
			}
			
			if(!empty($params['fromname'])) {
				$ed['From'] = isset($params['fromname']) ? $params['fromname'] : '';
			}
			
			if(!empty($params['msg'])) {
				$ed['Message'] = isset($params['msg']) ? $params['msg'] : '';
				$item->products_price += $custom_message_product;
				$item->products_name = $item->products_name . " (with message)";
			}
			
	        $item->extra_data = serialize($ed);

	        // we need to unset the orderitem's ID to force a new entry..other wise we will overwrite any
	        // other giftcards in the cart already
	        $item->id = null;
	        $item->quantity = $this->getDefaultQuantity();
		    $item->save();
		    return true;
	    }
	}
	
	public function hasUserInputFields()  {
		return true;
    }
	
	public function hasOptions() {
		return false;
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
	
	function getBasePrice($orderitem=null) {
		return $this->products_price;
    }
	
	public function update($params=array()) {
		// eDebug($params, true);
		parent::update($params); 
	}
	
	function getDefaultQuantity() {
		//TMP: Make this actually do something.
		return 1;
	}
	
	function getSurcharge() {   
		return '';
	}
	
	 public function removeItem($item) {
        return true;
    }
	
	function updateCart() {
		// nothing to do for this callback.
	}

	function checkout() {
		// nothing to do for this callback.
	}
	
	public function process($item) {
		
	}
	
	
}

?>