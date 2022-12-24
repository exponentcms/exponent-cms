<?php

##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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

class blog extends expRecord {

    protected $attachable_item_types = array(
        'content_expCats'=>'expCat',
        'content_expFiles'=>'expFile',
        'content_expTags'=>'expTag',
        'content_expComments'=>'expComment'
    );

    public $validates = array(
        'presence_of'=>array(
            'title'=>array('message'=>'Title is a required field.'),
            'body'=>array('message'=>'Post Content is a required field.'),
        )
    );

    public $supports_revisions = true;

//    public function __construct($params=null, $get_assoc=true, $get_attached=true) {
//        parent::__construct($params, $get_assoc, $get_attached);
//
//        if (!empty($this->publish)) {
//            $this->publish_date = $this->publish;
//        } elseif (!empty($this->edited_at)) {
//            $this->publish_date = $this->edited_at;
//        } elseif (!empty($this->created_at)) {
//            $this->publish_date = $this->created_at;
//        }
//
//    }

    function __construct($params = null, $get_assoc = true, $get_attached = true) {
        parent::__construct($params, $get_assoc, $get_attached);
        if (!empty($this->meta_fb))
            $this->meta_fb = expUnserialize($this->meta_fb);
        else
            $this->meta_fb = array();
        if (!empty($this->meta_fb['fbimage']) && !empty($this->meta_fb['fbimage'][0]))
            $this->meta_fb['fbimage'][0] = new expFile($this->meta_fb['fbimage'][0]);
        if (!empty($this->meta_tw))
            $this->meta_tw = expUnserialize($this->meta_tw);
        else
            $this->meta_tw = array();
        if (!empty($this->meta_tw['twimage']) && !empty($this->meta_tw['twimage'][0]))
            $this->meta_tw['twimage'][0] = new expFile($this->meta_tw['twimage'][0]);
    }

    public function beforeSave() {
   	    if (empty($this->publish) || $this->publish === 'on') {
   	        $this->publish = time();
   	    }
        parent::beforeSave();
   	}

//    public function find($range='all', $where=null, $order=null, $limit=null, $limitstart=0, $get_assoc=true, $get_attached=true, $except=array(), $cascade_except = false) {
//        global $db, $user;
//
//        if (is_numeric($range)) {
//            $where = $this->identifier.'='.(int)($range); // If we hit this then we are expecting just a simple id
//            $range = 'first';
//        }
//
//        $sql  = empty($where) ? 1 : $where;
//        //eDebug("Supports Revisions:" . $this->supports_revisions);
//        if ($this->supports_revisions && $range != 'revisions') $sql .= " AND revision_id=(SELECT MAX(revision_id) FROM `" . $db->prefix .$this->tablename."` WHERE $where)";
//        $sql .= empty($order) ? '' : ' ORDER BY '.$order;
////        $where .= " private=0 ";
//
//        if (strcasecmp($range, 'all') == 0 || strcasecmp($range, 'revisions') == 0) {
//            $sql .= empty($limit) ? '' : ' LIMIT '.$limitstart.','.$limit;
//            return $db->selectExpObjects($this->tablename, $sql, $this->classname, $get_assoc, $get_attached);
//        } elseif (strcasecmp($range, 'first') == 0) {
//            $sql .= ' LIMIT 0,1';
//            $records = $db->selectExpObjects($this->tablename, $sql, $this->classname, $get_assoc, $get_attached);
//            return empty($records) ? null : $records[0];
//        } elseif (strcasecmp($range, 'bytitle') == 0) {
//            $records = $db->selectExpObjects($this->tablename, "title='".$where."' OR sef_url='".$where."'", $this->classname, $get_assoc, $get_attached);
//            return empty($records) ? null : $records[0];
//        } elseif (strcasecmp($range, 'count') == 0) {
//            return $db->countObjects($this->tablename, $sql);
//        } elseif (strcasecmp($range, 'in') == 0) {
//            if (!is_array($where)) return array();
//            foreach ($where as $id) $records[] = new $this->classname($id);
//            return $records;
//        } elseif (strcasecmp($range, 'bytag') == 0) {
//            $sql  = 'SELECT DISTINCT m.id FROM '.DB_TABLE_PREFIX.'_'.$this->table.' m ';
//            $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expTags ct ';
//            $sql .= 'ON m.id = ct.content_id WHERE ct.exptag_id='.$where." AND ct.content_type='".$this->classname."'";
//            if ($this->supports_revisions) $sql .= " AND revision_id=(SELECT MAX(revision_id) FROM `" . $db->prefix .$this->tablename."` WHERE ct.exptag_id=".$where." AND ct.content_type='".$this->classname."'";
//            $tag_assocs = $db->selectObjectsBySql($sql);
//            $records = array();
//            foreach ($tag_assocs as $assoc) {
//                $records[] = new $this->classname($assoc->id);
//            }
//            return $records;
//        }
//    }

    public function update($params = array()) {
        if (isset($params['expFile']['fbimage'][0]) && is_numeric($params['expFile']['fbimage'][0]))
            $params['fb']['fbimage'][0] = $params['expFile']['fbimage'][0];
        unset ($params['expFile']['fbimage']);
        if (isset($params['fb'])) {
            $params['meta_fb'] = serialize($params['fb']);
            unset ($params['fb']);
        }
        if (isset($params['expFile']['twimage'][0]) && is_numeric($params['expFile']['twimage'][0]))
            $params['tw']['twimage'][0] = $params['expFile']['twimage'][0];
        unset ($params['expFile']['twimage']);
        if (isset($params['tw'])) {
            $params['meta_tw'] = serialize($params['tw']);
            unset ($params['tw']);
        }
        parent::update($params);
    }

}

?>