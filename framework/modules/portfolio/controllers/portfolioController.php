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
 * @subpackage Controllers
 * @package Modules
 */

class portfolioController extends expController {
    //public $basemodel_name = '';
    public $useractions = array(
        'showall'=>'Show all', 
        'tags'=>"Tags",
        'slideshow'=>"Slideshow"
    );
    public $remove_configs = array(
        'comments',
        'ealerts',
        'rss'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

    function displayname() { return gt("Portfolio"); }
    function description() { return gt("This module allows you to show off your work portfolio style."); }
    function isSearchable() { return true; }

    public function showall() {
        $limit = (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10;
        if (!empty($this->params['view']) && ($this->params['view'] == 'showall_accordion' || $this->params['view'] == 'showall_tabbed')) {
            $limit = '0';
        }
        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
        $page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>$this->aggregateWhereClause(),
            'limit'=>$limit,
            'order'=>$order,
            'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
            'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Title')=>'title'
            ),
        ));

        assign_to_template(array(
            'page'=>$page,
            'rank'=>($order==='rank')?1:0
        ));
    }
    
    public function slideshow() {
        expHistory::set('viewable', $this->params);

        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
        //FIXME we need to change this to expPaginator to get category grouping
        $s = new portfolio();
        $slides = $s->find('all',$this->aggregateWhereClause(),$order);

        assign_to_template(array(
            'slides'=>$slides,
            'rank'=>($order==='rank')?1:0
        ));
    }

    /**
   	 * The aggregateWhereClause function creates a sql where clause which also includes aggregated module content
   	 *
   	 * @return string
   	 */
   	function aggregateWhereClause() {
        $sql = parent::aggregateWhereClause();
        $sql .= (!empty($this->config['only_featured']))?"AND featured=1":"";
        return $sql;
    }

}

?>