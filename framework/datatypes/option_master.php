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

class option_master extends expRecord {
	public $validates = array(
		'presence_of'=>array(
			'title'=>array('message'=>'Name is a required field.'),
			'optiongroup_master_id'=>array('message'=>'You must pick an option group for this option.')),
        'alphanumericality_of'=>array(
            'title'=>array('message'=>'Option name must only contain alphnumeric characters, spaces, hypens, or dashes.')
		));
		
	public function __construct($params=null, $get_assoc=true, $get_attached=true) {
	    global $db;
	    parent::__construct($params, $get_assoc, $get_attached);	    
	    $this->timesImplemented = $db->countObjects('option', 'enable=1 AND option_master_id='.$this->id);
	}
    
    public function update($params)
    {
        global $db;
        //need to accomodate rank so can't call parent
        //eDebug($params, true);
        //$this->beforeSave();        
        $obj->optiongroup_master_id = $params['optiongroup_master_id'];
        $obj->title = $params['title'];
        
        $valObj = new option_master($params);
        $valObj->validate();
        
        //if we've made it here, the test validation worked so we can continue
        $obj->id = $params['id'];
        
        if (empty($params['id'])) 
        {
            $obj->rank = $db->max('option_master','rank', null, 'optiongroup_master_id=' . $params['optiongroup_master_id']) + 1 ;   
            $db->insertObject($obj, 'option_master');
        }
        else {
            $obj->rank = $params['rank'];
            $db->updateObject($obj, 'option_master');   
        } 
           
    }
	
}

?>
