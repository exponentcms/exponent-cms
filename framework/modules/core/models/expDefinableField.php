<?php
##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * This is the class expDefinableField
 *
 * @subpackage Models
 * @package Core
 */
class expDefinableField extends expRecord {
	public $table = 'expDefinableFields';
    public $default_sort_field = 'rank';
	public $attachable_table = 'content_expDefinableFields';
    protected $attachable_item_types = array();

    public function afterDelete() {
        global $db;

	    // get and delete all attachments to this object
	    $db->delete('content_expDefinableFields','expdefinablefields_id='.$this->id);
        $db->delete('content_expDefinableFields_value','expdefinablefields_id='.$this->id);
    }

}

?>
