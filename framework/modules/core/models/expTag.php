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
 * This is the class expTag
 *
 * @subpackage Models
 * @package Core
 */
class expTag extends expRecord {
	public $table = 'expTags';
	public $attachable_table = 'content_expTags';
    protected $attachable_item_types = array(
        //'content_expFiles'=>'expFile', 
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );

    public function afterDelete() {
        global $db;

	    // get and delete all attachments to this object
	    $db->delete('content_expTags','exptags_id='.$this->id);
    }

    /*
     * Return comma-separated list of all tags in system
     *
     */
    public static function getAllTags() {
        global $db;

        $tags = $db->selectObjects('expTags', '1', 'title ASC');
        $taglist = '';
        foreach ($tags as $tag) {
            $taglist .= "'" . $tag->title . "',";
        }
        return $taglist;
    }

    /*
     * Return array of all expTag records in system
     */
    public static function selectAllTagContentType() {
        global $db;

        return $db->selectColumn('content_expTags','content_type',null,null,true);
    }

    public static function deleteTag($content_type,$content_id) {
        global $db;

        $db->delete('content_expTags', 'content_type="'.$content_type.'" AND content_id='.$content_id);
    }
}

?>
