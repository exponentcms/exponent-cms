<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class order_statusController extends expController {
    //public $basemodel_name = '';
    //public $useractions = array('manage'=>'Manage Status Codes');

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Ecommerce Status Codes"; }
    function description() { return "Manage Ecommerce status codes"; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return false; }
    function hasViews() { return true; }
    function hasContent() { return false; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }
    
    public function manage() {
        expHistory::set('viewable', $this->params);
        
        $page = new expPaginator(array(
			'model'=>'order_status',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'where'=>1,
			'order'=>'rank',
			//'columns'=>array('Name'=>'title')
			));

		assign_to_template(array('page'=>$page));
    }
    
    public function manage_messages() {
        expHistory::set('managable', $this->params);
        
        $page = new expPaginator(array(
			'model'=>'order_status_messages',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'where'=>1,
			'order'=>'body',
			//'columns'=>array('Name'=>'title')
			));

        //eDebug($page);
		assign_to_template(array('page'=>$page));
    }
    
    public function edit_message() {
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $msg = new order_status_messages($id);
        assign_to_template(array('record'=>$msg));
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
    
    public function showall() {
        redirct_to(array('controller'=>'order_status', 'action'=>'manage'));
    }
    
    public function show() {
        redirct_to(array('controller'=>'order_status', 'action'=>'manage'));
    }
    
}

?>
