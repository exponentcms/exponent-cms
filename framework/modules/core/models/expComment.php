<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * This is the class expComment
 *
 * @subpackage Models
 * @package Core
 */

class expComment extends expRecord {
	public $table = 'expComments';
    public $attachable_table = 'content_expComments';

    /**
     * attach the comment to the item it belongs to (blog, news, etc..);
     *
     * @param $content_type
     * @param $content_id
     * @param null $subtype
     */
    public function attachComment($content_type, $content_id, $subtype = null) {
        global $db;

        if ($this->id) {
            // attach the comment to the datatype it belongs to (blog, news, etc..);
            $obj = new stdClass();
            $obj->expcomments_id = $this->id;
            $obj->content_id = $content_id;
            $obj->content_type = $content_type;
            if(isset($subtype))
                $obj->subtype = $subtype;
            else
                $obj->subtype = '';
            $db->insertObject($obj, $this->attachable_table);
        }
    }

    public function afterDelete() {
        global $db;

	    // get and delete all attachments to this object
	    $db->delete('content_expComments','expcomments_id='.$this->id);
    }

}

?>
