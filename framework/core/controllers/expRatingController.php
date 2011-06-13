<?php
/**
 *  This file is part of Exponent
 * 
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expTagController class.
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2006 OIC Group, Inc.
 * @author Phillip Ball <phillip@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expTagController
 *
 * @subpackage Core-Controllers
 * @package Framework
 */

class expRatingController extends expController {

    public $base_class = 'expRating';
	//public $useractions = array('browse'=>'Browse content by tags');
	public $useractions = array();
	
	function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Ratings Manager"; }
    function description() { return "This module is for manageing ratings on records"; }
    function author() { return "OIC Group, Inc"; }
    function hasSources() { return false; }
    function hasViews() { return true; }
	function hasContent() { return true; }
	function supportsWorkflow() { return false; }
	function isSearchable() { return false; }
	
	function __construct($src=null, $params=array()) {
        global $user;
	    parent::__construct($src, $params);
        $this->remove_permissions = ($user->isLoggedIn())?array('update','create'):array();
    }
	
    function update() {
        global $db, $user;
        	
        $this->params['id'] = $db->selectValue('content_expRatings','expratings_id',"content_type='".$this->params['content_type']."' AND subtype='".$this->params['subtype']."' AND poster='".$user->id."'");
        $rating = new expRating($this->params);

        // save the comment
        $rating->update($this->params);
        // attach the comment to the datatype it belongs to (blog, news, etc..);
		$obj->content_type = $this->params['content_type'];
		$obj->content_id = $this->params['content_id'];
		$obj->expratings_id = $rating->id;
		$obj->poster = $rating->poster;
		if(isset($this->params['subtype'])) $obj->subtype = $this->params['subtype'];
		$db->insertObject($obj, $rating->attachable_table);

        $ar = new expAjaxReply(200, gettext('Thank you for your rating'));
        $ar->send();
		
        // flash('message', $msg);
        // 
        // expHistory::back();
	}
	
}
?>