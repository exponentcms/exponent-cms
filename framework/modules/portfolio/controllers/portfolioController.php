<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
        'tags'=>"Tags",
        'slideshow'=>"Slideshow"
    );
    public $remove_configs = array(
        'ealerts',
        'tags',
        'rss',
        'comments'
    ); // all options: ('aggregation', 'cats','comments','ealerts','files','pagination', 'rss','tags')

    function displayname() { return "Portfolio"; }
    function description() { return "This module allows you to show off your work portfolio style."; }
    function isSearchable() { return true; }

    public function showall() {
        $where = $this->aggregateWhereClause();
        $where .= (!empty($this->config['only_featured']))?"AND featured=1":"";
        $order = 'rank';
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $usecategories = empty($this->config['usecategories']) ? false : $this->config['usecategories'];

        $page = new expPaginator(array(
                    'model'=>'portfolio',
                    'where'=>$where, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'categorize'=>$usecategories,
                    'controller'=>$this->baseclassname,
                    'src'=>$this->loc->src,
                    'action'=>$this->params['action'],
                    'columns'=>array('Title'=>'title'),
                    ));
        
        assign_to_template(array('page'=>$page));
    }
    
	public function tags() {
        $ports = $this->portfolio->find('all');
        $used_tags = array();
        foreach ($ports as $port) {
            foreach($port->expTag as $tag) {
                if (isset($used_tags[$tag->id])) {
                    $used_tags[$tag->id]->count += 1;
                } else {
                    $exptag = new expTag($tag->id);
                    $used_tags[$tag->id] = $exptag;
                    $used_tags[$tag->id]->count = 1;
                }
            }
        }
        
        $used_tags = expSorter::sort(array('array'=>$used_tags,'sortby'=>'title', 'order'=>'ASC', 'ignore_case'=>true));
	    assign_to_template(array('tags'=>$used_tags));
	}

    public function slideshow() {
        expHistory::set('viewable', $this->params);
        $where = $this->aggregateWhereClause();
        $where .= (!empty($this->config['only_featured']))?"AND featured=1":"";
        $order = 'rank';
        $s = new portfolio();
        $slides = $s->find('all',$where,$order);
                    
        assign_to_template(array('slides'=>$slides));
    }

	public function showall_by_tags() {
	    global $db;	    

	    // set history
	    expHistory::set('viewable', $this->params);
	    
	    // get the tag being passed
        $tag = new expTag($this->params['tag']);

        // find all the id's of the portfolios for this portfolio module
        $port_ids = $db->selectColumn('portfolio', 'id', $this->aggregateWhereClause());
        
        // find all the portfolios that this tag is attached to
        $ports = $tag->findWhereAttachedTo('portfolio');
        
        // loop the portfolios for this tag and find out which ones belong to this module
        $ports_by_tags = array();
        foreach($ports as $port) {
            if (in_array($port->id, $port_ids)) $ports_by_tags[] = $port;
        }

        // create a pagination object for the portfolios and render the action
		$order = 'created_at';
		$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
		
		$page = new expPaginator(array(
		            'records'=>$ports_by_tags,
		            'limit'=>$limit,
		            'order'=>$order,
		            'controller'=>$this->baseclassname,
		            'action'=>$this->params['action'],
		            'columns'=>array('Title'=>'title'),
		            ));
        $page->records = expSorter::sort(array('array'=>$page->records,'sortby'=>'rank', 'order'=>'ASC', 'ignore_case'=>true));

		assign_to_template(array('page'=>$page,'moduletitle'=>'Portfolio Pieces by tag "'.$this->params['tag'].'"'));
	}

    public function showall_by_cats() {
        global $db;

        // set history
        expHistory::set('viewable', $this->params);

        // get the tag being passed
        $cat = new expCat($this->params['cat']);

        // find all the id's of the portfolios for this portfolio module
        $port_ids = $db->selectColumn('portfolio', 'id', $this->aggregateWhereClause());

        // find all the portfolios that this cat is attached to
        $ports = $cat->findWhereAttachedTo('portfolio');

        // loop the portfolios for this cat and find out which ones belong to this module
        $ports_by_cats = array();
        foreach($ports as $port) {
            if (in_array($port->id, $port_ids)) $ports_by_cats[] = $port;
        }

        // create a pagination object for the portfolios and render the action
        $order = 'created_at';
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];

        $page = new expPaginator(array(
                    'records'=>$ports_by_cats,
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array('Title'=>'title'),
                    ));
        $page->records = expSorter::sort(array('array'=>$page->records,'sortby'=>'rank', 'order'=>'ASC', 'ignore_case'=>true));

        assign_to_template(array('page'=>$page,'moduletitle'=>'Portfolio Pieces by category "'.$this->params['cat'].'"'));
    }

}

?>
