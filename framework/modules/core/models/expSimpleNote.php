<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

    /**
     * attach the note to the item it belongs to (product, order, etc..);
     */
    public function attachNote($content_type, $content_id, $subtype = null) {
        global $db;

        if ($this->id) {
            // attach the note to the datatype it belongs to (product, order, etc..);
            $obj = new stdClass();
            $obj->content_type = $content_type;
            $obj->content_id = $content_id;
            $obj->expsimplenote_id = $this->id;
            if(isset($subtype)) $obj->subtype = $subtype;
            $db->insertObject($obj, $this->attachable_table);
        }
    }

    public function afterDelete() {
        global $db;

	    // get and delete all attachments to this object
	    $db->delete('content_expSimpleNote','expsimplenote_id='.$this->id);
    }

    public static function noteCount($content_id, $content_type, $unapproved = false) {
        global $db;

        $sql  = 'SELECT count(com.id) as c FROM '.$db->prefix.'expSimpleNote com ';
        $sql .= 'JOIN '.$db->prefix.'content_expSimpleNote cnt ON com.id=cnt.expsimplenote_id ';
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
