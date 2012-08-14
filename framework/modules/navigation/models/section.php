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
 * @package Modules
 */

class section extends expRecord {
//	public $table = 'section';

//    protected $attachable_item_types = array(
//        'content_expFiles'=>'expFile'
//    );

#	public $validates = array(
#		'presence_of'=>array(
#			'body'=>array('message'=>'Body is a required field.'),
#		));

    function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc,$get_attached);
        if (empty($this->parent)) $this->parent = 0;
    }

    function update($params=array()) {
        parent::update($params);
        expSession::clearAllUsersSessionCache('navigation');
//        expHistory::back();
    }

    function delete() {
        parent::delete();
        navigationController::deleteLevel($this->params['id']);
        expSession::clearAllUsersSessionCache('navigation');
//        expHistory::back();
    }

}

?>