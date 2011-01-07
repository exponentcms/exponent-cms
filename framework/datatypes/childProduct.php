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

class childProduct extends product {
    public $table = 'product';
    public $has_one = array();
    public $has_many = array(); 
    public $has_many_self = array(); 
    public $has_and_belongs_to_many = array(); 
    public $has_and_belongs_to_self = array(); 
    public $has_many_self_id = 'parent_id';
                                                                            
    public $product_name = 'Child Product'; 
    
    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile', 
    );
    
    public $do_not_validate = array('sef_url');
    
    public function __construct($params=array(), $get_assoc=false, $get_attached=true ) {
        global $db;
        parent::__construct($params, $get_assoc, $get_attached);
        //if (!empty($this->id)) $this->extra_fields = expUnserialize($this->extra_fields);
        $this->price = $this->getBasePrice();
    }
    
    
}
?>
