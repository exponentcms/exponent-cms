<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

class order_statusController extends expController {
    static function displayname() { return gt("e-Commerce Order Statuses"); }
    static function description() { return gt("Manage e-Commerce order status codes"); }
    static function hasSources() { return false; }
    static function hasContent() { return false; }
    
    public function manage() {
        expHistory::set('viewable', $this->params);
        
        $page = new expPaginator(array(
			'model'=>'order_status',
			'where'=>1,
            'limit'=>10,
			'order'=>'rank',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            //'columns'=>array('Name'=>'title')
        ));

		assign_to_template(array(
            'page'=>$page
        ));
    }
    
    public function manage_messages() {
        expHistory::set('manageable', $this->params);
        
        $page = new expPaginator(array(
			'model'=>'order_status_messages',
			'where'=>1,
            'limit'=>10,
			'order'=>'body',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
			//'columns'=>array('Name'=>'title')
        ));

        //eDebug($page);
		assign_to_template(array(
            'page'=>$page
        ));
    }
    
    public function edit_message() {
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $msg = new order_status_messages($id);
        assign_to_template(array(
            'record'=>$msg
        ));
        //$msg->update($this->params);
    }
    
    public function update_message() {
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $msg = new order_status_messages($id);
        $msg->update($this->params);
        expHistory::back();
    }
    
    public function delete_message() {
        if (empty($this->params['id'])) return false;
        $msg = new order_status_messages($this->params['id']);
        $msg->delete();
        expHistory::back();
    }
    
    public function toggle_closed() {
        global $db;

        $db->toggle('order_status', 'treat_as_closed', 'id='.$this->params['id']);
        expHistory::back();
    }
    
    public function toggle_default() {
        global $db;

        $order_status = new order_status($this->params['id']);
        $db->setUniqueFlag($order_status, 'order_status', 'is_default');
        expHistory::back();
    }
    
    public function showall() {
        redirect_to(array('controller'=>'order_status', 'action'=>'manage'));
//        $this->manage();
    }
    
    public function show() {
        redirect_to(array('controller'=>'order_status', 'action'=>'manage'));
//        $this->manage();
    }
    
}

?>