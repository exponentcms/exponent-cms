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
class optiongroup_master extends expRecord {
	public $has_many = array('option_master');
    //public $has_many_options = array('option_master'=>array('sort'=>'rank', 'sortdir'=>'ASC'));
	protected $attachable_item_types = array();
    
	public $validates = array(
		'presence_of'=>array(
			'title'=>array('message'=>'Option Group name is a required field.')),
        'alphanumericality_of'=>array(
            'title'=>array('message'=>'Option Group name must only contain alphnumeric characters, spaces, hypens, or dashes.')
		));
		
	public function __construct($params=null, $get_assoc=true, $get_attached=true) {
	    global $db;
	    parent::__construct($params, $get_assoc, $get_attached);	    
	    $this->timesImplemented = $db->countObjects('optiongroup', 'optiongroup_master_id='.$this->id);
        if(!empty($this->id))
        { 
            usort($this->option_master, array("optiongroup_master", "sortOptions"));
        }
	}
    
    static function sortOptions($a,$b)
    {
        if ($a->rank < $b->rank) return -1;
        else if ($a->rank > $b->rank) return 1;
        else if ($a->rank == $b->rank) return 0; 
    }  
}

?>