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
 * This is the class expRatingController
 *
 * @package Core
 * @subpackage Controllers
 */

class expRatingController extends expController {
    public $base_class = 'expRating';

    static function displayname() { return gt("Ratings Manager"); }
    static function description() { return gt("This module is for managing ratings on records"); }
    static function hasSources() { return false; }

	function __construct($src=null, $params=array()) {
        global $user;
	    parent::__construct($src, $params);
        $this->remove_permissions = ($user->isLoggedIn())?array('update','create'):array();
    }

    /**
     * Update rating...handled via ajax
     */
    function update() {
        global $db, $user;

        $this->params['content_type'] = preg_replace("/[^[:alnum:][:space:]]/u", '', $this->params['content_type']);
        $this->params['subtype'] = preg_replace("/[^[:alnum:][:space:]]/u", '', $this->params['subtype']);
        $this->params['id'] = $db->selectValue('content_expRatings','expratings_id',"content_id='".$this->params['content_id']."' AND content_type='".$this->params['content_type']."' AND subtype='".$this->params['subtype']."' AND poster='".$user->id."'");
        $msg = gt('Thank you for your rating');
        $rating = new expRating($this->params);
        if (!empty($rating->id)) $msg = gt('Your rating has been adjusted');
        // save the rating
        $rating->update($this->params);

        // attach the rating to the datatype it belongs to (blog, news, etc..);
        $obj = new stdClass();
		$obj->content_type = $this->params['content_type'];
		$obj->content_id = $this->params['content_id'];
		$obj->expratings_id = $rating->id;
		$obj->poster = $rating->poster;
		if(isset($this->params['subtype'])) $obj->subtype = $this->params['subtype'];
		$db->insertObject($obj, $rating->attachable_table);

        $ar = new expAjaxReply(200,$msg);
        $ar->send();

        // flash('message', $msg);
        // expHistory::back();
	}

}

?>