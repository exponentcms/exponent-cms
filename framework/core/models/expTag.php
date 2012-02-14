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
 * This is the class expTag
 *
 * @subpackage Core-Models
 * @package Framework
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
    
	
}
?>
