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
 * This is the class expRss
 *
 * @subpackage Core-Models
 * @package Framework
 */
/** @define "BASE" "../../../" */

class expRss extends expRecord {
    public $table = 'expRss';
    protected $attachable_item_types = array(
        //'content_expFiles'=>'expFile', 
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );
    
    public function __construct($params=array()) {
        global $db;
        if (isset($params['module']) && isset($params['src'])) {
            $id = $db->selectValue($this->table, 'id', "module='".expModules::getControllerName($params['module'])."' AND src='".$params['src']."'");
            parent::__construct($id, false, false);
        } else {
            parent::__construct($params, false, false);
        }
    }
    
    // we are going to override the build and beforeSave functions to
    // make sure the name of the controller is in the right format
    public function build($params=array()) {
        parent::build($params);
        $this->module = expModules::getControllerName($this->module);
    }
    
	// override the update function in order to make sure we don't save duplicate entries
	// as save called from expController does not have an id set.
	public function update($params=array()){
		
		parent::update($params);
	}
	
    public function beforeSave() {
        $this->module = expModules::getControllerName($this->module);
        parent::beforeSave();
    }
    
    public function getFeedItems() {
        require_once(BASE.'external/feedcreator.class.php');

        // get all the feeds available to this expRss object
        $feeds = $this->getFeeds();
        
        $items = array();
        // loop over and build out a master list of rss items
        foreach ($feeds as $feed) {
            $controllername = expModules::getControllerClassname($feed->module);
            $controller = new $controllername($feed->src);
            $controller->loc = makeLocation($feed->module, $feed->src);
            $items = array_merge($items, $controller->getRSSContent());
        }
        
        return $items;
    }
    
    public function getFeeds() {
        $where = '';
        if (!empty($this->module)) $where .= "module='".$this->module."'";
        if (!empty($this->src)) {
            $where .= empty($where) ? '' : ' AND ';
            $where .= "src='".$this->src."'";
        }
        
        return $this->find('all', $where);
    }
}

?>