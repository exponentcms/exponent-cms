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

class companyController extends expController {
	public $useractions = array(
        'showall'=>'Show all'
    );
	public $remove_configs = array(
        'aggregation',
        'comments',
        //'files',
        'rss',
        'tags'
    ); // all options: ('aggregation', 'cats','comments','ealerts','files','pagination', 'rss','tags')

	function displayname() { return "Company Listings"; }
	function description() { return "This module shows company listings"; }
	function hasSources() { return false; }
	
	function showall() {
        expHistory::set('viewable', $this->params);
        $limit = isset($this->params['limit']) ? $this->params['limit'] : null;
        $order = isset($this->params['order']) ? $this->params['order'] : 'rank';
        $page = new expPaginator(array(
                    'model'=>$this->basemodel_name,
                    'where'=>1, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array('Title'=>'title', 'Link'=>'website'),
                    ));
        
        assign_to_template(array('page'=>$page, 'items'=>$page->records));
    }
    
    function show()
    {
        global $db, $user, $router;
        //eDebug($this->params,true);
        
        expHistory::set('viewable', $this->params);
        
        $count_sql_start = 'SELECT COUNT(DISTINCT p.id) FROM '.DB_TABLE_PREFIX.'_product p ';             
            
        $sql_start  = 'SELECT DISTINCT p.* FROM '.DB_TABLE_PREFIX.'_product p ';            
        //$sql = 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ON p.id = sc.product_id ';
        $sql = 'WHERE ';
        if ( !($user->is_admin || $user->is_acting_admin) ) $sql .= '(p.active_type=0 OR p.active_type=1) AND ' ;
        //$sql .= 'sc.storecategories_id IN (';
        //$sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->category->lft.' AND '.$this->category->rgt.')';         
        $sql .=  'p.companies_id=' . $this->params['id'];
        $sql .=  ' AND p.parent_id = 0';
        
        $count_sql = $count_sql_start . $sql;
        $sql = $sql_start . $sql;
        
        //eDebug($sql);
        $order = 'p.id'; //$this->config['orderby'];
        $dir = 'DESC'; $this->config['orderby_dir'];
        //eDebug($this->config);
       
        $page = new expPaginator(array(
                'model_field'=>'product_type',
                'sql'=>$sql,
                'count_sql'=>$count_sql,
                'limit'=>$this->config['pagination_default'],
                'order'=>$order,
                'dir'=>$dir,
                'controller'=>$this->params['controller'],
                'action'=>$this->params['action'],
                'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price'),
                )); 

        //$ancestors = $this->category->pathToNode();   
        //$categories = ($this->parent == 0) ? $this->category->getTopLevel(null,false,true) : $this->category->getChildren(null,false,true);
        //eDebug($page);
        //$rerankSQL = "SELECT DISTINCT p.* FROM ".DB_TABLE_PREFIX."_product p JOIN ".DB_TABLE_PREFIX."_product_storeCategories sc ON  p.id = sc.product_id WHERE sc.storecategories_id=".$this->category->id." ORDER BY rank ASC";
        //eDebug($router);
        $defaultSort = $router->current_url;
        assign_to_template(array('record'=>new company($this->params['id']), 'page'=>$page, 'defaultSort'=>$defaultSort));
    }
    
    function showByTitle()
    {
        global $db, $user, $router;
        //eDebug($this->params,true);
        
        expHistory::set('viewable', $this->params);
        
        $count_sql_start = 'SELECT COUNT(DISTINCT p.id) FROM '.DB_TABLE_PREFIX.'_product p ';             
            
        $sql_start  = 'SELECT DISTINCT p.* FROM '.DB_TABLE_PREFIX.'_product p ';            
        //$sql = 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ON p.id = sc.product_id ';
        $sql = 'WHERE ';
        if ( !($user->is_admin || $user->is_acting_admin) ) $sql .= '(p.active_type=0 OR p.active_type=1) AND ' ;
        //$sql .= 'sc.storecategories_id IN (';
        //$sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->category->lft.' AND '.$this->category->rgt.')';         
        $sql .=  'p.companies_id=' . $this->params['id'];
        
        $count_sql = $count_sql_start . $sql;
        $sql = $sql_start . $sql;
        
        //eDebug($sql);
        $order = 'p.id'; //$this->config['orderby'];
        $dir = 'DESC'; $this->config['orderby_dir'];
        //eDebug($this->config);
       
        $page = new expPaginator(array(
                'model_field'=>'product_type',
                'sql'=>$sql,
                'count_sql'=>$count_sql,
                'limit'=>$this->config['pagination_default'],
                'order'=>$order,
                'dir'=>$dir,
                'controller'=>$this->params['controller'],
                'action'=>$this->params['action'],
                'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price'),
                )); 

        //$ancestors = $this->category->pathToNode();   
        //$categories = ($this->parent == 0) ? $this->category->getTopLevel(null,false,true) : $this->category->getChildren(null,false,true);
        //eDebug($page);
        //$rerankSQL = "SELECT DISTINCT p.* FROM ".DB_TABLE_PREFIX."_product p JOIN ".DB_TABLE_PREFIX."_product_storeCategories sc ON  p.id = sc.product_id WHERE sc.storecategories_id=".$this->category->id." ORDER BY rank ASC";
        //eDebug($router);
        $defaultSort = $router->current_url;
        assign_to_template(array('record'=>new company($this->params['id']), 'page'=>$page, 'defaultSort'=>$defaultSort));
    }
}

?>
