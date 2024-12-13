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
 * This is the class expRating
 *
 * @subpackage Models
 * @package Core
 */
class expRating extends expRecord {
	public $table = 'expRatings';
	public $attachable_table = 'content_expRatings';
//    protected $attachable_item_types = array(
//        //'content_expFiles'=>'expFile',
//        //'content_expTags'=>'expTag',
//        //'content_expComments'=>'expComment',
//        //'content_expSimpleNote'=>'expSimpleNote',
//    );

    public function afterDelete() {
        global $db;

	    // get and delete all attachments to this object
	    $db->delete('content_expRatings','expratings_id='.$this->id);
    }

}

?>