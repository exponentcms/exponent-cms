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
 * This is the class expCat
 *
 * @subpackage Models
 * @package Core
 */

class expCat extends expRecord {
	public $table = 'expCats';
	public $attachable_table = 'content_expCats';
    protected $attachable_item_types = array(
        //'content_expFiles'=>'expFile', 
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );

    /**
     * __construct expCat item...needs special grouping we can have duplicates across modules

     * @param array $params
     */
    public function __construct($params=array()) {
        parent::__construct($params);
        $this->grouping_sql = " AND module='".$this->module."'";
    }

    /**
     * beforeValidation we can have duplicate expCats across modules
     */
    public function beforeValidation() {
        $this->grouping_sql = " AND module='".$this->module."'";
        $this->validates = array(
            'uniqueness_of'=>array(
                'sef_url'=>array(
                    'grouping_sql'=>" AND module='".$this->module."'"
                )
            ),
        );
        parent::beforeValidation();
    }

    public function beforeSave() {
        $this->grouping_sql = " AND module='".$this->module."'";
        parent::beforeSave();
    }

    /**
     * make an sef_url for expCat item allowing for duplicates in other modules
     */
//    public function makeSefUrl() {
//        global $db, $router;
//
//        if (!empty($this->title)) {
//			$this->sef_url = $router->encode($this->title);
//		} else {
//			$this->sef_url = $router->encode('Untitled');
//		}
//        $dupe = $db->selectValue($this->tablename, 'sef_url', 'sef_url="'.$this->sef_url.'" AND module="'.$this->module.'"');
//		if (!empty($dupe)) {
//			list($u, $s) = explode(' ',microtime());
//			$this->sef_url .= '-'.$s.'-'.$u;
//		}
//    }

    /**
   	 * rerank expCat items
   	 * @param $direction
   	 * @param string $where
   	 */
//   	public function rerank($direction, $where='') {
//       global $db;
//       if (!empty($this->rank)) {
//           $next_prev = $direction == 'up' ? $this->rank - 1 : $this->rank +1;
//           $where.= empty($this->location_data) ? null : "location_data='".$this->location_data."' AND module='".$this->module."'";
//           $db->switchValues($this->tablename, 'rank', $this->rank, $next_prev, $where);
//       }
//    }


}

?>
