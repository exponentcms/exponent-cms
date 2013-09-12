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

class help extends expRecord {
//	public $table = 'help';
    public $default_sort_field = 'rank';
	public $has_one = array('help_version');

    /**
     * __construct help item...needs special grouping we can have duplicates across help versions
     *
     * @param array $params
     */
    public function __construct($params=array()) {
        parent::__construct($params);
        $this->loc = expUnserialize($this->location_data);
        $this->grouping_sql = " AND help_version_id='".$this->help_version_id."'";
    }
    
    // public function beforeSave($params=array()) {  
    //     eDebug($this,1);
    //     //$this->save(true);  
    // }

    /**
     * beforeValidation we can have duplicate help items across help versions
     */
    public function beforeValidation() {
        $this->grouping_sql = " AND help_version_id='".$this->help_version_id."'";
        $this->validates = array(
            'uniqueness_of'=>array(
                'sef_url'=>array(
                    'grouping_sql'=>" AND help_version_id='".$this->help_version_id."'"
                )
            ),
        );
        parent::beforeValidation();
    }

    public function update($params = array()) {
        global $db;

        $this->grouping_sql = " AND help_version_id='".$this->help_version_id."'";
		if (isset($params['help_section'])) {
			// manipulate section & source to correct values
			$params['section'] = $db->selectValue('sectionref', 'section', 'module = "help" AND source="' . $params['help_section'] .'"');
			$params['src'] = $params['help_section'];
            $params['rank'] = 0;
		}
        parent::update($params);
    }

    /**
     * Save help item...we MUST also save the current section assigned
     *
     */
    public function beforeSave() {
        global $db;

        $this->grouping_sql = " AND help_version_id='".$this->help_version_id."'";
//		if (isset($this->params['help_section'])) {
//			// manipulate section & location_data to correct values
//			$this->section = $db->selectValue('sectionref', 'section', 'module = "help" AND source="' . $this->params['help_section'] .'"');
//            $loc = expCore::makeLocation('help',$this->params['help_section']);
//			$this->location_data = serialize($loc);
//		}

        parent::beforeSave();

        // circumvent the re-ranking problem
//        $oldrank = $this->rank;
//        if (empty($this->rank)) unset($this->rank);
//        parent::save(true);
//        if (!empty($oldrank)) $this->rank = $oldrank;
//        parent::save();
   }

	/**
	 * Make a unique help item sef-url within the help_version
	 */
//	public function makeSefUrl() {
//		global $router, $db;
//
//        if (isset($this->title)) {
//			$this->sef_url = $router->encode($this->title);
//		} else {
//			$this->sef_url = $router->encode('Untitled');
//		}
//        $dupe = $db->selectValue($this->tablename, 'sef_url', 'sef_url="'.$this->sef_url." AND help_version_id='".$this->help_version_id."'");
//		if (!empty($dupe)) {
//			list($u, $s) = explode(' ',microtime());
//			$this->sef_url .= '-'.$s.'-'.$u;
//		}
//	}

//	/**
//	 * validate help item sef_url within the help_version
//	 * @return bool
//	 */
//	public function validate() {
//        global $db;
//        // check for an sef url field.  If it exists make sure it's valid and not a duplicate
//        //this needs to check for SEF URLS being turned on also: TODO
//
//        if (property_exists($this, 'sef_url') && !(in_array('sef_url',$this->do_not_validate))) {
//            if (empty($this->sef_url)) $this->makeSefUrl();
//            $this->validates['is_valid_sef_name']['sef_url'] = array();
//            $this->validates['uniqueness_of']['sef_url'] = array();
//        }
//
//        // safeguard again loc data not being pass via forms...sometimes this happens when you're in a router
//        // mapped view and src hasn't been passed in via link to the form
//        if (isset($this->id) && empty($this->location_data)) {
//            $loc = $db->selectValue($this->tablename, 'location_data', 'id='.$this->id);
//            if (!empty($loc)) $this->location_data = $loc;
//        }
//
//        // run the validation as defined in the model
//        if (!isset($this->validates)) return true;
//        $messages = array();
//        $post = empty($_POST) ? array() : $_POST;
//        foreach ($this->validates as $validation=>$field) {
//            foreach($field as $key=>$value) {
//                $fieldname = is_numeric($key) ? $value : $key;
//                $opts = is_numeric($key) ? array() : $value;
//
//                // this is the expValidator::uniqueness_of code
//                $sql = "`".$fieldname."`='".$this->$fieldname." AND help_version_id='".$this->help_version_id."'";
//                if (!empty($this->id)) $sql .= ' AND id != '.$this->id;
//                $ret = $db->countObjects($this->tablename, $sql);
//                if ($ret > 0) {
//                    $ret = array_key_exists('message', $opts) ? $opts['message'] : ucwords($fieldname).' "'.$this->$fieldname.'" is already in use.';
//                } else {
//                    $ret = true;
//                }
//
//                if(!is_bool($ret)) {
//                    $messages[] = $ret;
//                    expValidator::setErrorField($fieldname);
//                    unset($post[$fieldname]);
//                }
//            }
//        }
//
//        if (count($messages) >= 1) expValidator::failAndReturnToForm($messages, $post);
//    }

    /**
   	 * rerank items
   	 * @param $direction
   	 * @param string $where
   	 */
//   	public function rerank($direction, $where='') {
//       global $db;
//       if (!empty($this->rank)) {
//           $next_prev = $direction == 'up' ? $this->rank - 1 : $this->rank +1;
//           $where.= empty($this->location_data) ? null : "location_data='".$this->location_data."' AND help_version_id='".$this->help_version_id."'";
//           $db->switchValues($this->tablename, 'rank', $this->rank, $next_prev, $where);
//       }
//    }

    /**
   	 * before saving item
   	 */
//   	public function beforeSave() {
//       global $user, $db;
//       // populate the magic fields
//       if (empty($this->id)) {
//           // timestamp the record
//           if (property_exists($this, 'created_at')) $this->created_at = time();
//           if (property_exists($this, 'edited_at')) $this->edited_at = time();
//           // record the user saving the record.
//           if (property_exists($this, 'poster')) $this->poster = empty($this->poster) ? $user->id : $this->poster;
//           // fill in the rank field if it exist
//           if (property_exists($this, 'rank')) {
//               if (empty($this->rank)) {
////                   $where = "1 ";
//                   $where = empty($this->location_data) ? "1 " : "location_data='".$this->location_data."' ";
//                   $where .= " AND help_version_id='".$this->help_version_id."'";
//                   //FIXME: $where .= empty($this->rank_by_field) ? null : "AND " . $this->rank_by_field . "='" . $this->$this->rank_by_field . "'";
//                   $groupby = empty($this->location_data) ? null : 'location_data';
////                   $groupby .= empty($this->rank_by_field) ? null : empty($groupby) ? null : ',' . $this->rank_by_field;
//                   $this->rank = $db->max($this->tablename, 'rank', $groupby, $where) +1;
//               } else {
//                   // check if this rank is already there..if so increment everything below it.
//                   $obj = $db->selectObject($this->tablename, 'rank='.$this->rank." AND help_version_id='".$this->help_version_id."'");
//                   if (!empty($obj)) {
//                       $db->increment($this->tablename,'rank',1,'rank>='.$this->rank." AND help_version_id='".$this->help_version_id."'");
//                   }
//               }
//           }
//
//           $this->beforeCreate();
//       } else {
//           // put the created_at time back the way it was so we don't set it 0
//           if (property_exists($this, 'created_at') && $this->created_at == 0) {
//               $this->created_at = $db->selectValue($this->tablename, 'created_at', 'id='.$this->id);
//           }
//
//           // put the original posters id back the way it was so we don't set it 0
//           if (property_exists($this, 'poster') && $this->poster == 0) {
//               $this->poster = $db->selectValue($this->tablename, 'poster', 'id='.$this->id);
//           }
//
//           //put the rank back to what it was so we don't set it 0
//           if (property_exists($this, 'rank') && $this->rank == 0) {
//               $this->rank = $db->selectValue($this->tablename, 'rank', 'id='.$this->id);
//           }
//
//           if (property_exists($this, 'edited_at')) $this->edited_at = time();
//           if (property_exists($this, 'editor')) $this->editor = $user->id;
//           $this->beforeUpdate();
//       }
//    }

    /**
     * delete item
     *
     * @param $module
     *
     * @return bool
     */
//   	public function delete($where = '') {
//       global $db;
//       if (empty($this->id)) return false;
//       $this->beforeDelete();
//       $db->delete($this->tablename,'id='.$this->id);
//       if (!empty($where)) $where .= ' AND ';
//       if (property_exists($this, 'rank')) $db->decrement($this->tablename,'rank',1, $where . 'rank>='.$this->rank." AND help_version_id='".$this->help_version_id."'");
//
//       // delete attached items
//       foreach($this->attachable_item_types as $content_table=>$type) {
//           $db->delete($content_table, 'content_type="'.$this->classname.'" AND content_id='.$this->id);
//       }
//       $this->afterDelete();
//    }

    public static function makeHelpLink($module) {
        // make sure the module name is in the right format.
        $module = expModules::getControllerName($module);
        
        // figure out which version we're on
//        $full_version = EXPONENT_VERSION_MAJOR.'.'.EXPONENT_VERSION_MINOR.'.'.EXPONENT_VERSION_REVISION;
        $full_version = expVersion::getVersion(true,false,false);

        $link  = HELP_URL;
        $link .= 'docs';
        $link .= '/'.$full_version;
        $link .= '/'.$module;
        
        return $link;
    }

    public static function getHelpParents($version_id) {
        global $db;

        return $db->selectColumn('help', 'parent', 'help_version_id="'.$version_id.'" AND parent!=0',null,true);
    }


}

?>