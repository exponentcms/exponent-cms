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

class orderitem extends expRecord {
	public $table = 'orderitems';
	public $has_one = array('shippingmethod');
	public $opts = array();
	
	function __construct($params=array(), $get_assoc = true, $get_attached = false) {
		global $db, $user;
		
		if (!empty($params['id']) || is_numeric($params)) {
			parent::__construct($params, $get_assoc, $get_attached);
			$prodtype = $this->product_type;
			$this->product = new $prodtype($this->product_id,false,true);
		} elseif (isset($params['product_id']) && isset($params['product_type'])) {	
			// see if this is an existing item in the cart
			if(isset($params['orderid'])) $order = new order($params['orderid']);
            else $order = order::getUserCart();
			
            //adding lookup on price to acocomdate quantity discounts
			$where = 'orders_id='.$order->id.' AND product_id='.$params['product_id'].' AND products_price='.$params['products_price']." AND product_type='".$params['product_type']."'";
			$where .= empty($params['options']) ? '' : " AND options='".$params['options']."'";
			$where .= empty($params['user_input_fields']) ? '' : " AND user_input_fields='".$params['user_input_fields']."'";
            
			$item = $db->selectObject($this->table, $where);
            
			$this->product = new $params['product_type']($params['product_id'],false,true);
			if (empty($item)) {
				parent::__construct(array(
        	        'orders_id'=>$order->id,
                	'user_id'=>$user->id,
                    'product_id'=>$this->product->id,
                    'product_type'=>$params['product_type'],
	                'products_name'=>$this->product->title,
        	        'products_price'=>$this->product->getBasePrice(),
                	//'quantity'=>$this->product->getDefaultQuantity()
                ), $get_assoc, $get_attached);
            } else {
	            parent::__construct($item->id, $get_assoc, $get_attached);
            	//$this->quantity = $this->product->incrementQuantity($this->quantity);
            }
	    } else {
		    parent::__construct(null, $get_assoc, $get_attached);
	    }
	    if (isset($this->options)) {
	        $this->opts = expUnserialize($this->options);
	    }
	    if (!empty($this->user_input_fields)) {
	        //$this->user_input_fields = expUnserialize($this->user_input_fields);
	    }
	}

    public function getCartSummary() {
        // if (!empty($this->user_input_fields)) {
        //     return "<span style='font-style:italic'>SKU:" . $this->product->model . "</span><br/>"; // . 
        //     // "<p style='font-size:10px; line-height: 110%'>" . $this->getUserInputFields() . "</p>";
        // }else{
            return empty($this->product->model) ? "" : "<div style='font-style:italic'>SKU:" . $this->product->model . "</div>";
        // }
    }
    
    public function getUserInputFields($style='br') {
        if (!empty($this->user_input_fields))
        {
            //eDebug(expUnserialize($this->user_input_fields,true));
            if ($style=='br') $ret = '<br/>';
            else if ($style=='list') $ret='<ul>';
            foreach (expUnserialize($this->user_input_fields) as $uifarray)
            {
                foreach ($uifarray as $uifkey=>$uif)
                {
                    if ($style=='list') $ret.="<li>" . $uifkey.": ".$uif."</li>";    
                    if ($style=='br') $ret.=$uifkey.": ".$uif."<br/>";    
                }                                                             
            } 
            if ($style=='list') $ret.='</ul>';
            if ($ret == '<br/>') $ret = '';
            return $ret;
        }
    }
    
    public function getOption($opt) {
        $option = new option($opt[0]);
        $optgrp = new optiongroup($option->optiongroup_id);                                                      
        return $optgrp->title . ": " . $option->title;          
    }
    
    /*function getPriceWithOptions() {  
        //$opts = expUnserialize($this->options);
        //eDebug($this,true) ;
        $price = $this->product->getBasePrice(); 
        if (count($this->opts))
        {
            foreach ($this->opts as $opt) {
                $selected_option = new option($opt[0]);
                $cost = $selected_option->modtype == '$' ? $selected_option->amount :  $this->product->getBasePrice() * ($selected_option->amount * .01);
                $cost = $selected_option->updown == '+' ? $cost : $cost * -1;                      
                $price += $cost;
                //$options[] = array($selected_option->id,$selected_option->title,$selected_option->modtype,$selected_option->updown,$selected_option->amount);
            }
        }
        return $price;
        
    }*/
    
    function merge($params) {        
        // check to see if this item was in the old cart we are merging..if so we will 
        // up tick the quantity...otherwise we will just add the item to the cart.
        $existing_item = $this->find('first', "orders_id=".$params['orders_id']." AND product_id=".$this->product_id." AND product_type='".$this->product_type."' AND options='".$this->options."'");
        if (empty($existing_item)) {
            $this->update(array('orders_id'=>$params['orders_id'], 'user_id'=>$params['user_id']));
        } else {
            $existing_item->update(array('quantity'=>$existing_item->quantity + $this->quantity));
        }
        
    }
    
	public function getTotal() {
	    // because these variable names are so long we're going to set these for 
	    // shorthand/readability purposes
	    $prod = $this->product;
	    $quantity_amount = $prod->quantity_discount_num_items;
	    
	    if ($quantity_amount < 1 || $this->quantity <= $quantity_amount) {
	        $total = $this->products_price * $this->quantity;
	    } else {
	        // if this is set we only apply the discount to the products over the limit.
	        if ($prod->quantity_discount_apply) {
                $disc_priced = $this->quantity - $prod->quantity_discount_num_items;
                $orig_priced = $this->quantity - $disc_priced;
            } else {
                $disc_priced = $this->quantity;
                $orig_priced = 0;
            }
	        
	        $total =  $this->products_price * $orig_priced ;
	        if ($prod->quantity_discount_amount_mod == "$") {
	            $total += ($this->products_price - $prod->quantity_discount_amount) * $disc_priced;
	        } elseif ($prod->quantity_discount_amount_mod == "%") {
	            $subtotal = $this->products_price * $disc_priced;
	            $total += ($subtotal - (($this->products_price * $disc_priced) * ($prod->quantity_discount_amount * .01)));
	        }
	    }
	    return $total;
	}
	
	public function getExtraData() {
        //eDebug($this,true);
        return ($this->extra_data);
	    //$product = new $this->product_type($this->product_id);
	    //return $product->formatExtraData($this);
	}
	
	public function getFormattedExtraData($style='list') {
		$ret = '';
		if ($style == 'list') {
			$ret ='<ul>';
			foreach(expUnserialize($this->extra_data) as $key => $item) {
				$ret .= "<li>{$key} : {$item}</li>";
			}
			$ret .='<ul>';
		}
		return $ret;
	}
    
    public function getProductsName()
    {
        $name = $this->products_name;        
        if (is_array($this->product->extra_fields))
        {
               $name.="<br/>";
               foreach ($this->product->extra_fields as $f)
               {
                   $name .= " " . $f['value'];
               }
        }
        return $name;    
        
    }
    
    public function getShippingSurchargeMessage()
    {
        $sc = $this->product->getSurcharge();
        if ($sc > 0) return "<span class='surcharge'>* This item has an extra freight surcharge of $" . number_format($sc,2) .' each.</span>';
        else return '';
    }
    
    public function getLineItemTotal() {
        return $this->quantity * $this->products_price;
    }
    
    /*public function getStatus(){
        return "Status";
    }*/
}
?>
