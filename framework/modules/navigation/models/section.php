<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

class section extends expRecord {
//	public $table = 'section';

    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile'
    );

#	public $validates = array(
#		'presence_of'=>array(
#			'body'=>array('message'=>'Body is a required field.'),
#		));

    function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc,$get_attached);
        if (empty($this->parent)) $this->parent = 0;
        $this->grouping_sql = " AND parent='".$this->parent."'";
    }

    function update($params=array()) {
        $this->grouping_sql = " AND parent='".$this->parent."'";
        if (empty($this->sef_name) && empty($params['sef_name'])) $params['sef_name'] = expCore::makeSefUrl($params['name'],'section');
        parent::update($params);
        expSession::clearAllUsersSessionCache('navigation');
//        expHistory::back();
    }

    public function beforeSave() {
        $this->grouping_sql = " AND parent='".$this->parent."'";
        parent::beforeSave();
    }

    function delete($where = '') {
        if ($this->parent == -1) {
            unset($this->rank);
            $where = '';
        } else {
            $where = "parent='".$this->parent."'";
        }
        parent::delete($where);
        navigationController::deleteLevel($this->id);
        expSession::clearAllUsersSessionCache('navigation');
//        expHistory::back();
    }

}

?>