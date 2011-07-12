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

class product extends expRecord {
	public $table = 'product';
	public $has_one = array('company', 'product_status');
	public $has_many = array('optiongroup');
    public $has_many_self = array('childProduct');            
	public $has_and_belongs_to_many = array('storeCategory');
    public $has_and_belongs_to_self = array('crosssellItem');
    
    public $get_assoc_for = array('optiongroup'); 
                                                                            
    public $product_name = 'Product';
    public $product_type = 'product';
    public $requiresShipping =true; 
	public $requiresBilling = true; 
    public $isQuantityAdjustable = true;
    
    public $quantity_display = array(
            0=>'Always available even if out of stock.',
            1=>'* Available to order, but will display the message below if out of stock.',
            2=>'* Unavailable if out of stock and will display the message below.',
            3=>'Show as &quot;Call for Price&quot;.'
    );
    
    public $active_display = array(
            0=>'Active',
            1=>'Inactive but findable. <br>It will not be shown in product listings and the "Add to Cart" button is disabled but is still viewable directly. This can be advantageous with the page cacheing in the search engines.',
            2=>'Inactive and disabled. <br>Trying to view this product will produce an error indicating this product is currently not available.',
    );
    
    public $quantity_discount_items_modifiers = array('gte'=>'Equal to or more than', 'gt'=>'More than');
    public $quantity_discount_amount_modifiers = array('$'=>'$', '%'=>'%');
    
    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile', 
        'content_expRatings'=>'expRating', 
        'content_expComments'=>'expComment',
        'content_expSimpleNote'=>'expSimpleNote',
    );
    
    
	public function __construct($params=array(), $get_assoc=true, $get_attached=true) {
	    global $db;
		parent::__construct($params, $get_assoc, $get_attached);
		$this->extra_fields = expUnserialize($this->extra_fields);
		$this->price = $this->getBasePrice();
        $this->user_input_fields = expUnserialize($this->user_input_fields);
        /*if (!empty($this->childProduct))
        {
            foreach($this->childProduct as &$child) 
            {
                $child->expFile = $this->expFile;    
            }
        } */
        if (!empty($this->parent_id))
        {
            $parent = new product($this->parent_id, false, true);
            //eDebug($parent->expFile);
            $this->expFile = $parent->expFile;
            //eDebug($this); 
        }
        
        //sort the children by child_rank
        if ($this->hasChildren())
        {
            if (isset($this->childProduct)) usort($this->childProduct, array("product", "sortChildren"));
        }
	}
	
	function incrementQuantity($oldval) {
		return ++$oldval;
	}
	
	function updateQuantity($newval) {
        if($this->allow_partial)
        {
            return floatval($newval);    
        }
        else
        {
            return intval($newval);
        }		
	}
	
    function getBasePrice($orderitem=null) {
        if ($this->use_special_price) {
            return $this->special_price;
        } else {
            return $this->base_price;
        }
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
        
    function getDefaultQuantity() {
		//TMP: Make this actually do something.
		return 1;
	}

	function updateCart() {
		// nothing to do for this callback.
	}

	function checkout() {
		// nothing to do for this callback.
	}

    /*function hasOptions() {
        global $db;
        $og = $db->selectObject('option', 'enable=1 AND product_id='.$this->id);
        return empty($og) ? false : true;
    }*/
    
   /* function preAddToCart($params) {
        //if has options, or if has user input fields, we retrn false so cart can show the addToCart form
        //otherwise we send to addToCart directly, which double checks things
        if (count($this->optiongroup) > 0) return false;
        if (count($this->user_input_fields) >0) return false;
    
        $this->addToCart($this->params);    
    } */
    
	function addToCart($params, $orderid = null) {
        //eDebug("OID: " . $orderid,true);
        if ($orderid == null) global $order;
        else $order = new order($orderid);
        //eDebug($this);
        //eDebug($params,true);
        $params['qty'] = isset($params['qty']) ? $params['qty'] : 1;
        if (!isset($params['product_type'])) $params['product_type'] = 'product';
        
        $params['error'] = '';
        
        if (empty($params['children']))
        {   
            //$oiObj = new orderitem();
            //$oi = $oiObj->find('all','product_id='.$this->id);
            $qCheck = 0;//$this->quantity;
            //if (!empty($oi))
            //{
            foreach($order->orderitem as $orderItem)
                {
                    //eDebug($orderItem->quantity);
                    if($orderItem->product_id == $this->id) $qCheck += $orderItem->quantity;
                }
            //}
            $qty = $params['qty'];
            if (($this->quantity - $qCheck) < $qty) {
                if ($this->availability_type == 2) {
                    flash('error', $this->title.' only has '.$this->quantity.' on hand. You can not add more than that to your cart.');
                    //return false;
                    expHistory::back();
                }
            }
            //check minimum quantity
            if (($qty + $qCheck) < $this->minimum_order_quantity)
            {
                 flash('message', $this->title.' has a minimum order quantity of '.$this->minimum_order_quantity.'. The quantity has been adjusted accordingly.');
                 $params['qty'] += $this->minimum_order_quantity - ($qty + $qCheck);
                 $qty = $params['qty'];                             
            }
        }else
        {
            foreach ($params['children'] as $idKey=>$childQty)
            {
                $cprod = new childProduct($idKey);
                //$oiObj = new orderitem();
                //$oi = $oiObj->find('all','product_id='.$idKey);
                $qCheck = 0;//$cprod->quantity;
                //if (!empty($oi))
                //{
                foreach($order->orderitem as $orderItem)
                {
                    //eDebug($orderItem->quantity);
                    if($orderItem->product_id == $idKey) $qCheck += $orderItem->quantity;
                }
                //}
                /*eDebug("Qty:".$childQty);
                eDebug("Product Quantity:".$cprod->quantity);
                eDebug("Qcheck:".$qCheck,true);*/
                if (($cprod->quantity - $qCheck) < $childQty) {
                    if ($cprod->availability_type == 2) {
                        flash('error', $this->title. ' - ' .$cprod->model. ' only has '.$cprod->quantity.' on hand. You can not add more than that to your cart.');
                        //return false;
                        expHistory::back();
                    }
                }
                //check minimum quantity
                if (($childQty + $qCheck) < $cprod->minimum_order_quantity)
                {
                     flash('message', $cprod->title.' has a minimum order quantity of '.$cprod->minimum_order_quantity.'. The quantity has been adjusted accordingly.');
                     $params['children'][$idKey] += $cprod->minimum_order_quantity - ($childQty + $qCheck);
                     //$qty = $params['qty'];                    
                }
            }
        }
        
        foreach ($this->optiongroup as $og) {
            if ($og->required) {
                $err = true;
                if (!empty($params['options'][$og->id]))
                {
                    foreach ($params['options'][$og->id] as $opt)
                    {
                        //eDebug($opt,true);
                        //make sure at least one is not empty to cover both single and mult selects
                         if (!empty($opt)) $err = false;
                    }
                }
                if ($err) $params['error'] .= 'You must select an option from the ' . $og->title . ' options below before you can add it to your cart. <br/>';                
            }
            //eDebug($og->title . ":" .$og->required);
        }
        
	    $user_input_info = array();
        //check user input fields
        //$this->user_input_fields = expUnserialize($this->user_input_fields);
        //eDebug($this,true);
        foreach ($this->user_input_fields as $uifkey=>$uif)
        {   
            if ($uif['is_required'] || (!$uif['is_required'] && strlen($params['user_input_fields'][$uifkey]) > 0)) 
            {
                if (strlen($params['user_input_fields'][$uifkey]) < $uif['min_length'])
                {
                    //flash('error', 'test');    
                    //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));  
                    $params['error'] .= $uif['name'].' field has a minimum requirement of ' . $uif['min_length'] . ' characters.<br/>';
                    
                }else if (strlen($params['user_input_fields'][$uifkey]) > $uif['max_length'] && $uif['max_length'] > 0)
                {
                    //flash('error', );    
                    //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));      
                    $params['error'] .= $uif['name'].' field has a maximum requirement of ' . $uif['max_length'] . ' characters.<br/>';
                } 
            }
            $user_input_info[] = array($uif['name']=>$params['user_input_fields'][$uifkey]);
        }
        
        if($orderid == null)
        {
            if ($params['error'] != '') {
                $this->displayForm('addToCart',$params);
                return false;   
            }
        }else
        {
            if ($params['error'] != '') {
                $this->displayForm('addToOrder',$params);
                return false;   
            }    
        }
        
        if (empty($params['children']))
        {
            $this->createOrderItem($this, $params, $user_input_info, $orderid);   
        }else{
            foreach ($params['children'] as $ckey=>$cqty)
            {
                $params['qty'] =  1;
                for ($qty=1; $qty<=$cqty; $qty++)  
                {
                    $child = new $params['product_type']($ckey);                     //$params['prod-quantity'][$ckey];
                    $this->createOrderItem($child, $params, $user_input_info, $orderid);
                    
                    /*foreach($this->childProduct as $child)
                    {
                        if ($child->id == $ckey) $this->createOrderItem($child, $params, $user_input_info);
                        break;   
                    }*/ 
                }  
                                     
            }
            //die();
        }        
		return true;
	}

    function displayForm($form, $params) {
        //eDebug($this->params);
        //$product_type = isset($this->params['product_type']) ? $this->params['product_type'] : 'product';
        //$product = new $product_type($this->params['product_id'],true,true);     
        //eDebug($product);   
        //if (!empty($product->user_input_fields)) $product->user_input_fields = expUnserialize($product->user_input_fields);
        //eDebug($product);
        $form = new controllerTemplate(new storeController(), $this->getForm($form));
        $form->assign('params', $params);
        $form->assign('product', $this);
        if (!empty($params['children']))
        {
            $form->assign('children', $params['children']);       
        }
        
        
        /*if (!empty($this->params['children'])) 
        {
            $form->assign('children', expUnserialize($this->params['children']));   
        }*/
        
        echo $form->render();
    }
    
    private function createOrderItem($product, $params, $user_input_info, $orderid)
    {
        //eDebug($params,true);
        global $db;
        if ($orderid == null) global $order;
        else $order = new order($orderid);
         
        $price = $product->getBasePrice();
        $options = array();  
        
        foreach ($this->optiongroup as $og) {
            $isOptionEmpty = true;
            if (!empty($params['options'][$og->id]))
            {
                foreach ($params['options'][$og->id] as $opt)
                {  
                     if (!empty($opt)) $isOptionEmpty = false;
                }
            }
            if (!$isOptionEmpty) {
                foreach ($params['options'][$og->id] as $opt_id) {
                    $selected_option = new option($opt_id);
                    $cost = $selected_option->modtype == '$' ? $selected_option->amount :  $this->getBasePrice() * ($selected_option->amount * .01);
                    $cost = $selected_option->updown == '+' ? $cost : $cost * -1;                      
                    $price = $price + $cost;
                    $options[] = array($selected_option->id,$selected_option->title,$selected_option->modtype,$selected_option->updown,$selected_option->amount);
                }
            }
        }
        
        //eDebug($params,true);
        // add the product to the cart.
        if ($orderid != null) 
        {
            if(empty($params['children'])) $price = $params['products_price'];
            else $price = $params['prod-price'][$product->id];
        }
        
        $params['product_id'] = $product->id;
        $params['options'] = serialize($options);
        $params['products_price'] = $price; 
        $params['user_input_fields'] = serialize($user_input_info); 
        $params['orderid'] = $orderid;
        /*$params['products_status'] = 
        $params['products_warehouse_location'] = 
        $params['products_model'] = $product->model;*/
        $item = new orderitem($params);
        //eDebug($item); 
        $item->products_price = $price;
        
        /*eDebug($item->quantity);
        eDebug($params);
        eDebug($product->minimum_order_quantity);*/
        
        $item->quantity += is_numeric($params['qty']) && $params['qty'] >= $product->minimum_order_quantity ? $params['qty'] : $product->minimum_order_quantity;
        if ($item->quantity < 1 ) $item->quantity = 1;
       // eDebug($item->quantity,true);
        //eDebug($params);
        //eDebug($item, true);
        //eDebug($item, true);
        $item->options = serialize($options);
        $item->user_input_fields = $params['user_input_fields'];
        $item->products_status = $product->product_status->title;
        if($product->parent_id == 0 || $product->warehouse_location != '') 
        {
            //eDebug("here1",true);
            $item->products_warehouse_location = $product->warehouse_location;    
        }
        else
        {            
            $item->products_warehouse_location = $db->selectValue('product','warehouse_location','id='.$product->parent_id);
        }
        
        $item->products_model = $product->model;
        
        $sm = $order->getCurrentShippingMethod();
        $item->shippingmethods_id = $sm->id;
        //eDebug($item,true);
        $item->save();
        return;   
    }
    
    public function removeItem($item) {
        return true;
    }
    
        
    public function process($item, $affects_inventory) {
        global $db;
        //only adjust inventory if the order type says it should, or we otherwise tell it to
        if($affects_inventory)
        {
            $this->quantity = $this->quantity - $item->quantity;
            //$this->save();
            $pobj->id = $this->id;
            $pobj->quantity = $this->quantity;
            $db->updateObject($pobj, 'product', 'id='.$this->id);    
        }
        return;        
    }
    
    public function optionDropdown($key, $display_price_as) {
	    $items = array();	    
	    
	    foreach ($this->optiongroup as $index=>$group) {
	        if ($group->title == $key) {	            
                foreach($group->option as $option) {
                    if ($option->enable == true) {
                        $text = $option->title;
                        
                        $price = '';
                        if (isset($option->amount)) {
                            if ($option->modtype == '%') {
                                $diff = ($this->getBasePrice() * ($option->amount * .01)) + $this->getBasePrice();
                            } else {
                                $diff = $option->amount;
                            }
                            
                            if ($display_price_as == 'total') {
                                $newprice = ($option->updown == '+') ? ($this->getBasePrice() + $diff) : ($this->getBasePrice() - $diff);
                                $price = ' ($'.number_format($newprice, 2).')';                         
                            } else {
                                if($diff > 0 )
                                {
                                    $diff = '$'.number_format($diff, 2);
                                    $price = ' ('.$option->updown.$diff.')';
                                }else
                                {
                                    $price = '';
                                }
                            }
                            
                        }                        
                        
                        $items[$option->id] = $text.$price;
                    }
                }
            }
        }
        return $items;
    }
    
    public function formatExtraData($item) {
        $viewname = $this->getForm('formatExtraData');
        if (!$viewname) return null;

        $view = new controllerTemplate($this, $viewname);
	    $view->assign('extra_data', expUnserialize($item->extra_data));
        return $view->render();
    }
    
    public function storeListing() {
        $viewname = $this->getForm('storeListing');
        if (!$viewname) return null;
        
        $view = new controllerTemplate($this, $viewname);
	    $view->assign('listing', $this);
        return $view->render();
    }
    
    public function cartSummary($item) {
        $viewname = $this->getForm('cartSummary');
        if (!$viewname) return null;
        
        $options = expUnserialize($item->options);
        $view = new controllerTemplate($this, $viewname);
	    $view->assign('product', $this);
	    $view->assign('item', $item);
	    $view->assign('options', $options);
        return $view->render('cartSummary');
    }
  
    public function getSEFURL()
    {
        if (!empty($this->sef_url)) return $this->sef_url; 
        $parent = new product($this->parent_id, false, false);
        return $parent->sef_url;
    }
    
    public function getForm($form) {        
        $dirs = array(
            BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/ecommerce/products/views/'.$this->product_type.'/',
            BASE.'framework/modules/ecommerce/products/views/'.$this->product_type.'/',
            BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/ecommerce/products/views/product/',
            BASE.'framework/modules/ecommerce/products/views/product/',
        );
        
        foreach ($dirs as $dir) {
            if (file_exists($dir.$form.'.tpl')) return $dir.$form.'.tpl';    
        }
        
        return false;
    }
    
    public function getViewDir() {        
        return 'modules/ecommerce/products/views/';
    }
    
    public function beforeSave() {
        if (is_array($this->extra_fields)) $this->extra_fields = serialize($this->extra_fields);
        parent::beforeSave();
        return true;
    }
    
    public function beforeDelete() {
        $this->deleteOptions();
        $this->deleteCrosssellItems();
        $this->deleteContentFromSearch();
    }
    
    private function deleteOptions()
    {
        global $db;
        $db->delete('option', 'product_id='.$this->id);
        $db->delete('optiongroup', 'product_id='.$this->id);    
    }
    
    private function deleteCrosssellItems()
    {
        global $db;
        $db->delete('crosssellItem_product', 'product_type="' . $this->product_type . '" AND (product_id='.$this->id . ' OR crosssellItem_id='.$this->id.')');        
    }
    
    protected function getAttachableItems() {
        if  ($this->classname != $this->product_type) $this->classname = $this->product_type;
        parent::getAttachableItems();
    }
    
    public function hasOptions()
    {           
        foreach ($this->optiongroup as $og)   
        {
            if (count($og->option)>0){
                foreach($og->option as $option) {
                    if ($option->enable == true) return true;
                }
            }
        }
        return false;
    }
    
    public function hasRequiredOptions()
    {
        foreach ($this->optiongroup as $og)   
        {
            if ($og->required) return true;
        }
        return false;
    }
    
    public function hasUserInputFields()
    {
        //eDebug($this->user_input_fields);
        if (!empty($this->user_input_fields) && count($this->user_input_fields) > 0) return true;
        else return false;   
    }
    
    public function isChild()
    {
         if ($this->parent_id == 0 ) return false;
         else return true;
    }
    
    //this is not guaranteed to be correct if the object was instantiated withOUT associated items,
    //so be careful where you call it
    public function hasChildren()
    {                                                                 
        global $db;
        if(isset($this->childProduct))
        {
            if (!empty($this->childProduct) && count($this->childProduct) == 0) return false;
            else return true;    
        }else{
            $sql = "SELECT id from " . DB_TABLE_PREFIX . "_product WHERE parent_id=" . $this->id;
            $count = $db->queryRows($sql);
            if ($count > 0) return true;
            else return false;
        }   
    }
    
    static function sortChildren($a,$b)
    {
        if ($a->child_rank < $b->child_rank) return -1;
        else if ($a->child_rank > $b->child_rank) return 1;
        else if ($a->child_rank == $b->child_rank) return 0; 
    }
    
    public function saveCategories($catArray,$catRankArray = null)
    {
        global $db;
        // if there are no categories specified we'll set this to the 0 category..meaning uncategorized'
        //eDebug($this->params['storeCategory']); 
        if (empty($catArray)) 
        {
            $db->delete('product_storeCategories', 'product_id='.$this->id);
            $catArray = array(0);
            $assoc->storecategories_id = 0;
            $assoc->product_id = $this->id;
            $assoc->product_type = $this->product_type;
            $assoc->rank = 0;
            $db->insertObject($assoc, 'product_storeCategories');
        }else{
            //we need to preserve the rank, so we need to check if we are in cateogories:
            $cats = $db->selectArrays('product_storeCategories', 'product_id='.$this->id);
            $curCats = array();
            foreach($cats as $c)
            {
                $curCats[] = $c['storecategories_id'];
            }
            //eDebug($curCats); //445 //1303D //1315
            //if it's in a category already we leave it
            //flip check the arrays - if not in one, we add. if vice versa, we delete:
            foreach ($catArray as $cat) {
                if (!in_array($cat,$curCats))
                {
                    //create new
                    $assoc->storecategories_id = $cat;
                    $assoc->product_id = $this->id;
                    $assoc->product_type = $this->product_type;
                    if($catRankArray != null && isset($catRankArray[$cat]) && $catRankArray[$cat]!='' && $catRankArray[$cat]!='0')
                    {
                        $assoc->rank = $catRankArray[$cat];
                    }else{
                        $assoc->rank = $db->max('product_storeCategories','rank', null, 'storecategories_id=' . $cat) + 1 ;    
                    } 
                    
                    $db->insertObject($assoc, 'product_storeCategories');    
                    //eDebug("Adding " . $cat);
                }else
                {
                    //update old
                    $assoc->storecategories_id = $cat;
                    $assoc->product_id = $this->id;
                    $assoc->product_type = $this->product_type;
                    if($catRankArray != null && isset($catRankArray[$cat]) && $catRankArray[$cat]!='' && $catRankArray[$cat]!='0')
                    {
                        $assoc->rank = $catRankArray[$cat];
                    }else{
                        $assoc->rank = $db->selectValue('product_storeCategories','rank', 'storecategories_id=' . $cat . ' AND product_id=' . $this->id);    
                    }                    
                    $db->updateObject($assoc, 'product_storeCategories','product_id=' . $this->id . ' AND storecategories_id=' . $cat);  
                    //eDebug("Adding " . $cat);
                }                    
            }
            foreach ($curCats as $delcat) {
                if (!in_array($delcat,$catArray))
                {
                    $db->delete('product_storeCategories', 'product_id='.$this->id . ' AND storecategories_id=' . $delcat);
                    //$db->decrement('product_storeCategories', 'rank', 1, ' AND storecategories_id=' . $delcat);
                }                    
            }     
            //die();           
        }
    }
    
    public function addContentToSearch()
    {
        global $db,$router;
        
        //only add top level products, not children
        if ($this->parent_id != 0 ) return true;
        
        if (!defined('SYS_SEARCH')) include_once(BASE.'subsystems/search.php');
        
        $exists = $db->selectObject('search',"category='Products' AND ref_module='store' AND original_id = " . $this->id);
        
        $search = null;
        $search->category = 'Products';
        $search->ref_module = 'store';
        $search->ref_type = 'product';
        $search->original_id = $this->id;
        $search->title = $this->title . " SKU: " . $this->model;
        //$search->view_link = $router->buildUrlByPageId($section->id);
        $link = $router->makeLink(array('controller'=>'store', 'action'=>'showByTitle', 'title'=>$this->sef_url));
        $search->view_link = $link; 
        $search->body = $this->body;
        $search->keywords = $this->keywords;
    
    //eDebug($exists);
    //eDebug($search,true);
        if(empty($exists)) $db->insertObject($search,'search');
        else 
        {
            $search->id = $exists->id;
            $db->updateObject($search,'search');    
        }

        return true;
    }
    
    private function deleteContentFromSearch()
    {
        global $db;
        $db->delete('search',"category='Products' AND ref_module='store' AND original_id = " . $this->id);
    }
    
    static public function canView($id)
    {
        global $db;
        if ($db->selectValue('product','active_type','id='.$id) == 2) return false;
        
        return true;
        //check if cateegory is 
    }
    
//    public function paginationCallback($item)
	public function paginationCallback(&$item) // (deprecated) moved call by reference to function, not caller
    {
        $score = $item->score;
        $item = $this;
        $item->score = $score;     
    }
}

?>
