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
 * This is the class expRss
 *
 * @subpackage Models
 * @package Modules
 */
/** @define "BASE" "../../../" */

class expRss extends expRecord {
    public $table = 'expRss';
    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile',
//        //'content_expTags'=>'expTag',
//        //'content_expComments'=>'expComment',
//        //'content_expSimpleNote'=>'expSimpleNote',
    );

    public $rss_is_podcast = false;

    public function __construct($params=array()) {
        global $db;

        if (is_int($params) || is_string($params)) {
            parent::__construct($params, false, false);
        } elseif ((isset($params['module']) || isset($params['controller'])) && isset($params['src'])) {
            $mod = !empty($params['module']) ? $params['module'] : (!empty($params['controller']) ? $params['controller'] : null);
            $id = $db->selectValue($this->table, 'id', "module='".expModules::getControllerName($mod)."' AND src='".$params['src']."'");
            parent::__construct($id, false, false);
        } else {
            parent::__construct($params, false, false);
        }
        if (!empty($this->module)) {
            $cont = expModules::getController($this->module);
            $this->rss_is_podcast = !empty($cont->rss_is_podcast);
        }
        $this->getAttachableItems();
    }
    
    // we are going to override the build and beforeSave functions to
    // make sure the name of the controller is in the right format
    public function build($params=array()) {
        parent::build($params);
        if (!empty($this->module)) $this->module = expModules::getControllerName($this->module);
    }
    
    public function beforeSave() {
        if (!empty($this->module)) $this->module = expModules::getControllerName($this->module);
        parent::beforeSave();
    }
    
    public function getFeedItems($limit = 0) {
        require_once(BASE.'external/feedcreator.class.php');

        // get all the feeds available to this expRss object
        $feeds = $this->getFeeds();
        
        $items = array();
        // loop over and build out a master list of rss items
        foreach ($feeds as $feed) {
//            $controllername = expModules::getControllerClassname($feed->module);
//            $controller = new $controllername($feed->src);
            $controller = expModules::getController($feed->module, $feed->src);
            if (!empty($controller)) {
                $controller->loc = expCore::makeLocation($feed->module, $feed->src);
                $controller->params = $this->params;
                $items = array_merge($items, $controller->getRSSContent($limit));
            }
        }
        
        return $items;
    }
    
    public function getFeeds($where = '') {
        if (!empty($this->module)) $where .= "module='".$this->module."'";
        if (!empty($this->src)) {
            $where .= empty($where) ? '' : ' AND ';
            $where .= "src='".$this->src."'";
        }
        
        return $this->find('all', $where);
    }
}

?>