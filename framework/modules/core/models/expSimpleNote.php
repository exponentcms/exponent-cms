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
 * This is the class expSimpleNote
 *
 * @subpackage Models
 * @package Core
 */
class expSimpleNote extends expRecord {
//    public $table = 'expSimpleNote';
    public $attachable_table = 'content_expSimpleNote';
//    protected $attachable_item_types = array(
//        //'content_expFiles'=>'expFile',
//        //'content_expTags'=>'expTag',
//        //'content_expComments'=>'expComment',
//        //'content_expSimpleNote'=>'expSimpleNote',
//    );

    public function afterDelete() {
        global $db;

	    // get and delete all attachments to this object
	    $db->delete('content_expSimpleNote','expsimplenote_id='.$this->id);
    }

}
?>
