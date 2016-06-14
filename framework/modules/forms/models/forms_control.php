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
 * @subpackage Models
 * @package    Modules
 */

class forms_control extends expRecord {
//	public $table = 'text';
//    public $has_one = array(
//        'forms'
//    );
    public $default_sort_field = 'rank';
    public $rank_was_changed = false;


//    protected $attachable_item_types = array(
//        'content_expFiles'=>'expFile'
//    );

#	public $validates = array(
#		'presence_of'=>array(
#			'body'=>array('message'=>'Body is a required field.'),
#		));

    /**
     * __construct forms_control item...needs special grouping we can have duplicates across modules

     * @param array $params
     */
    public function __construct($params=array()) {
        parent::__construct($params);
        if (!empty($this->forms_id)) {
            $this->grouping_sql = " AND forms_id='".$this->forms_id."'";
        }
    }

    /**
     * Gets a form control
     *
     * @param string $where
     *
     * @return null|object|void
     */
    public function getControl($where="1") {
        global $db;

        return $db->selectObject('forms_control', $where);
    }

    /**
     * Counts form controls
     *
     * @param string $where
     *
     * @return int
     */
    public function countControls($where="1") {
        global $db;

        return $db->countObjects('forms_control', $where);
    }

    /**
     * beforeValidation we can have duplicate forms_control across modules
     */
    public function beforeValidation() {
        $this->grouping_sql = " AND forms_id='".$this->forms_id."'";
        parent::beforeValidation();
    }

    public function beforeSave() {
        $this->grouping_sql = " AND forms_id='".$this->forms_id."'";
        // this is where item ranks are set/calculated/updated
        parent::beforeSave();
    }

    public function afterSave()
    {
        global $db;

        $this->grouping_sql = " AND forms_id='" . $this->forms_id . "'";
        parent::afterSave();
        //first page control MUST be the first control (rank = 1)
        $pager = $this->find('first', "forms_id='" . $this->forms_id . "' AND data LIKE '%pagecontrol%'", 'rank');
        // if we have at least one pagecontrol and it's not rank=1, move it to the top
        if (!empty($pager)) {
             if ($pager->rank != 1) {
                // increment all other controls below it
                $db->increment($this->tablename, 'rank', 1, 'rank<' . $pager->rank . $this->grouping_sql);
                 // change first pagecontrol rank to 1
                $pager->rank = 1;
                $pager->save();
            } elseif ($this->find('count', "forms_id='" . $this->forms_id . "' AND rank = 1") > 1) {
                 $db->increment($this->tablename, 'rank', 1, 'rank>=' . $pager->rank . $this->grouping_sql);
                 $pager->rank = 1;
                 $pager->save();
            }
        }
    }

    public function rerank_control($newrank) {
        global $db;

        $db->switchValues($this->tablename, 'rank', $newrank, $this->rank, "forms_id='" . $this->forms_id . "'");
    }
    
}

?>