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
 * @package Core
 */
class crosssellItem extends product {
    public $table = 'product';
    public $has_one = array();
    public $has_many = array(); 
    public $has_many_self = array(); 
    public $has_and_belongs_to_many = array(); 
    public $has_and_belongs_to_self = array(); 
    
    public $product_name = 'Crosssell Item'; 
    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile',
    );
    
    //public $has_and_belongs_to_self_id = 'parent_id';
    
    /*public $validates = array(
        'presence_of'=>array(
            'title'=>array('message'=>'Company Name is a required field.'),
            'body'=>array('message'=>'Company Description is a required field.'),
        ));*/
    
    public function __construct($params=array(), $get_assoc=false, $get_attached=true) {
        global $db;        
        parent::__construct($params, $get_assoc, $get_attached);
        $this->price = $this->getBasePrice();
        
        /*if (!empty($this->product_type))
        {
            $product_type = $this->product_type;    
            $this->product = new $product_type($this->crosssell_product_id, false, false);
        }*/
    }
    
    //overriding so we can call it; parent is protected
    public function getAttachableItems()
    {
        parent::getAttachableItems();
    }
}

?>
