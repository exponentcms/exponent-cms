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
 * @subpackage Controllers
 * @package Modules
 */

class order_typeController extends expController {

    function displayname() { return gt("Ecommerce Order Types"); }
    function description() { return gt("Manage Ecommerce Order Types"); }
    function hasSources() { return false; }
    function hasContent() { return false; }
    
    public function manage() {
        expHistory::set('viewable', $this->params);
        
        $page = new expPaginator(array(
			'model'=>'order_type',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'where'=>1,
            'limit'=>10,
			));

		assign_to_template(array(
            'page'=>$page
        ));
    }
    
    public function showall() {
        redirect_to(array('controller'=>'order_type', 'action'=>'manage'));
    }
    
    public function show() {
        redirect_to(array('controller'=>'order_type', 'action'=>'manage'));
    }
    
    public function update() {
        global $db;
        //reset others
        if ($this->params['is_default']){
            $o->is_default = false;
            $db->updateObject($o, 'order_type', 'is_default=1'); 
        }
        parent::update();
    }
}

?>