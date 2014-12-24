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

    public static function noteCount($content_id, $content_type, $unapproved = false) {
        global $db;

        $sql  = 'SELECT count(com.id) as c FROM '.DB_TABLE_PREFIX.'_expSimpleNote com ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expSimpleNote cnt ON com.id=cnt.expsimplenote_id ';
        $sql .= 'WHERE cnt.content_id='.$content_id." AND cnt.content_type='".$content_type."' ";
        if (!$unapproved) {
            $sql .= 'AND com.approved=1';
        } else {
            $sql .= 'AND com.approved=0';
        }
        return intval($db->countObjectsBySql($sql));
    }

}
?>
