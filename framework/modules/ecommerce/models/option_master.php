<?php

##################################################
#
# Copyright (c) 2004-2020 OIC Group, Inc.
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
        if (!empty($this->id)) {
            $this->timesImplemented = $db->countObjects('option', 'enable=1 AND option_master_id='.$this->id);
        } else {
            $this->timesImplemented = 0;
        }
        if (!empty($this->product_id))
            $this->grouping_sql = " AND optiongroup_master_id='".$this->optiongroup_master_id."'";
	}

    public function update($params=array())
    {
        global $db;

        $this->grouping_sql = " AND optiongroup_master_id='".$this->optiongroup_master_id."'";
        //need to accomodate rank so can't call parent
        //eDebug($params, true);
        //$this->beforeSave();
        $obj = new stdClass();
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

    public function beforeSave() {
        $this->grouping_sql = " AND optiongroup_master_id='".$this->optiongroup_master_id."'";
        parent::beforeSave();
    }

}

?>