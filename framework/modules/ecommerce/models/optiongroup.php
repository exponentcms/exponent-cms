<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
class optiongroup extends expRecord {
	public $has_many = array('option');

    public $default_sort_field = 'rank';
    public $default_sort_direction = 'ASC';
    public $input_needed = false;

//    protected $attachable_item_types = array();

	public $validates = array(
		'presence_of'=>array(
			'title'=>array('message'=>'Name is a required field.'),
		));

    public function __construct($params=null, $get_assoc=true, $get_attached=true) {
        global $db;

        parent::__construct($params, $get_assoc, $get_attached);
        if (!empty($this->id)) {
            $this->timesImplemented = $db->countObjects('optiongroup', 'optiongroup_master_id='.$this->id);
        } else {
            $this->timesImplemented = 0;
        }

        //sort the options based on the master sort order
        if(!empty($this->id))
        {
            foreach ($this->option as &$opt)
            {
                $om = new option_master($opt->option_master_id);
                $opt->rank = $om->rank;
                if (!empty($opt->show_input))
                    $this->input_needed = true;
            }
            usort($this->option, array("optiongroup_master", "sortOptions"));
        }
        if (!empty($this->product_id))
            $this->grouping_sql = " AND product_id='".$this->product_id."'";
    }

    function update($params=array()) {
        $this->grouping_sql = " AND product_id='".$this->product_id."'";
        parent::update($params);
    }

    public function beforeSave() {
        $this->grouping_sql = " AND product_id='".$this->product_id."'";
        parent::beforeSave();
    }

    public function save($validate=false, $force_no_revisions = false)
    {
        global $db;

        $obj = new stdClass();
        $obj->id = $this->id;
        $obj->optiongroup_master_id = $this->optiongroup_master_id;
        $obj->product_id = $this->product_id;
        $obj->title = $this->title;
        $obj->allow_multiple = $this->allow_multiple;
        $obj->required = $this->required;
        $obj->rank = $this->rank;

        eDebug( $obj->rank);
        if(empty($obj->rank))
        {
            $obj->rank = $db->max('optiongroup','rank', null, "product_id=" . $this->product_id) + 1 ;
        }
        eDebug( $obj->rank);
        if (empty($obj->id))
        {
            $this->id = $db->insertObject($obj, 'optiongroup');
        }
        else {
            $db->updateObject($obj, 'optiongroup');
        }

    }

    public function hasEnabledOptions()
    {
        if(empty($this->option)) return false;
        foreach ($this->option as $o)
        {
            if ($o->enable) return true;
        }
        return false;
    }

    static function sortOptiongroups($a,$b)
    {
        if ($a->rank < $b->rank) return -1;
        else if ($a->rank > $b->rank) return 1;
        else if ($a->rank == $b->rank) return 0;
    }

}

?>