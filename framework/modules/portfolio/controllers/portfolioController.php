<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class portfolioController extends expController {
    //public $basemodel_name = '';
    public $useractions = array(
        'showall'=>'Show all', 
        'showall_tags'=>"Tag Categories"
    );
    
    public $remove_configs = array('ealerts','tags','files','rss','comments');

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Portfolio"; }
    function description() { return "This module allows you to show off your work portfolio style."; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return true; }
    
    public function showall() {
        $where = $this->aggregateWhereClause();
        $order = 'rank';
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];

        $page = new expPaginator(array(
                    'model'=>'portfolio',
                    'where'=>$where, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'src'=>$this->loc->src,
                    'action'=>$this->params['action'],
                    'columns'=>array('Title'=>'title'),
                    ));
        
        assign_to_template(array('page'=>$page));
    }
    
    public function showall_tags() {
        $portfolio = new portfolio();
        $portfolios = $portfolio->find('all');
        $used_tags = array();
        foreach ($portfolios as $portfolio) {
            foreach($portfolio->expTag as $tag) {
                if (isset($used_tags[$tag->id])) {
                    $used_tags[$tag->id]->count += 1;
                } else {
                    $exptag = new expTag($tag->id);
                    $used_tags[$tag->id] = $exptag;
                    $used_tags[$tag->id]->count = 1;
                }
                
            }
        }
        
        assign_to_template(array('tags'=>$used_tags));
    }           
    
    public function update() {
        //FIXME:  Remove this code once we have the new tag implementation
        if (!empty($this->params['tags'])) {
            global $db;
            if (isset($this->params['id'])) {
                $db->delete('content_expTags', 'content_type="portfolio" AND content_id='.$this->params['id']);
            }
            
            $tags = explode(",", $this->params['tags']);
            
            foreach($tags as $tag) {
                $tag = strtolower(trim($tag));
                $expTag = new expTag($tag);
                if (empty($expTag->id)) $expTag->update(array('title'=>$tag));
                $this->params['expTag'][] = $expTag->id;
            }
        }
        // call expController update to save the blog article
        parent::update();
    }
}

?>
