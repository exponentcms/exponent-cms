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
 * @deprecated in favor of simpleNotes
 */
class product_notes extends expRecord
{
    public $table = 'product_notes';
    public $has_one = array('product');
    public $default_sort_field = 'rank';
    public $default_sort_direction = 'ASC';

    public function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc, $get_attached);
        if (!empty($this->product_id))
            $this->grouping_sql = " AND product_id='".$this->product_id."'";
    }

    function update($params=array()) {
        $this->grouping_sql = " AND product_id='".$this->product_id."'";
        parent::update($params);
    }

    public function beforeValidation() {
        $this->grouping_sql = " AND product_id='".$this->product_id."'";
        parent::beforeValidation();
    }

    public function beforeSave() {
        $this->grouping_sql = " AND product_id='".$this->product_id."'";
        parent::beforeSave();
    }


}

?>