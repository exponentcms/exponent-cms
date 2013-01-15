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
 * @package    Modules
 */

class forms_record extends expRecord {
//	public $table = 'text';
//    public $has_one = array(
//        'forms'
//    );

//    protected $attachable_item_types = array(
//        'content_expFiles'=>'expFile'
//    );

#	public $validates = array(
#		'presence_of'=>array(
#			'body'=>array('message'=>'Body is a required field.'),
#		));

    public function __construct($params = null, $get_assoc = true, $get_attached = true) {
        if (!empty($params) && is_string($params)) {
            $this->table = 'forms_' . $params;
        }
        parent::__construct($params, $get_assoc, $get_attached);
    }

}

?>