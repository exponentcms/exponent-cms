<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file thats holds the expComment class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expComment
 *
 * @subpackage Core-Datatypes
 * @package Framework
 */

class expComment extends expRecord {
	public $table = 'expComments';
    public $attachable_table = 'content_expComments';

    protected $attachable_item_types = array(
        //'content_expFiles'=>'expFile', 
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );
    
#	function __construct($params=null) {
#		global $user;
#		parent::__construct($params);
#		if ($user->id != 0 && $user->isLoggedIn()) {
#			$this->name = $user->firstname." ".$user->lastname;
#			$this->email = $user->email;
#		}
#		$this->content_id = isset($params['content_id']) ? $params['content_id'] : null;
#		$this->content_type = isset($params['content_type']) ? $params['content_type'] : null;
#	}

#	function save() {
#		global $db;
#		parent::save();
#		if (!empty($this->content_id) && !empty($this->content_type)) {
#			$details->comment_id = $this->id;
#			$details->content_id = $this->content_id;
#			$details->content_type = $this->content_type;
#			$db->insertObject($details, $this->attachable_table);
#		}
#	}
}
?> 
