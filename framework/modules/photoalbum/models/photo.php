<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

class photo extends expRecord {
    //public $table = 'photo';
    public $validates = array(
        'presence_of'=>array(
            'title'=>array('message'=>'Title is a required field.'),
            //'body'=>array('message'=>'Body is a required field.'),
        ));
    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile',
	    'content_expCats'=>'expCat',
    	'content_expTags'=>'expTag'
    );

    public function addNextPrev($where=1) {
        global $db;

        $maxrank = $db->max($this->tablename,'rank','',$where);

//        $this->next = $db->selectValue($this->tablename,'sef_url',$where." AND rank=".($this->rank+1));
        $next = $this->find('first',$where." AND rank>".($this->rank));
        if (!empty($next)) $this->next = $next->sef_url;

//        $this->prev = $db->selectValue($this->tablename,'sef_url',$where." AND rank=".($this->rank-1));
        $prev = $this->find('first',$where." AND rank<".($this->rank));
        if (!empty($prev)) $this->prev = $prev->sef_url;

//        if ($this->rank==$maxrank) {
//            $where = $where." AND rank=1";
//            $this->next = $db->selectValue($this->tablename,'sef_url',$where);
//        }
        if (empty($this->next)) {
            $next = $this->find('first',$where,'rank ASC');
            $this->next = $next->sef_url;
        }

//        if ($this->rank==1) {
//            $where = $where." AND rank=".$maxrank;
//            $this->prev = $db->selectValue($this->tablename,'sef_url',$where,'rank');
//        }
        if (empty($this->prev)) {
            $prev = $this->find('first',$where,'rank DESC');
            $this->prev = $prev->sef_url;
        }
    }

}

?>