<?php

##################################################
#
# Copyright (c) 2004-2019 OIC Group, Inc.
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

class companyController extends expController {
	public $useractions = array(
        'showall'=>'Show all'
    );
	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        //'files',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    static function displayname() { return gt("e-Commerce Manufacturer Listings"); }
    static function description() { return gt("Displays product manufacturer listings"); }
	static function hasSources() { return false; }

	function showall() {
        expHistory::set('viewable', $this->params);
        $page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>1,
            'limit'=>(isset($this->params['limit']) && $this->config['limit'] != '') ? $this->params['limit'] : 10,
            'order'=>isset($this->params['order']) ? $this->params['order'] : 'rank',
            'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : ''),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('Manufacturer')=>'title',
                gt('Website')=>'website'
            ),
        ));

        assign_to_template(array(
            'page'=>$page,
            'items'=>$page->records
        ));
    }

    function show()
    {
        global $db, $user, $router;
        //eDebug($this->params,true);

        expHistory::set('viewable', $this->params);

        $count_sql_start = 'SELECT COUNT(DISTINCT p.id) as c FROM ' . $db->tableStmt('product') . ' p ';

        $sql_start  = 'SELECT DISTINCT p.* FROM ' . $db->tableStmt('product') . ' p ';
        //$sql = 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ON p.id = sc.product_id ';
        $sql = 'WHERE ';
        if (!$user->isAdmin()) $sql .= '(p.active_type=0 OR p.active_type=1) AND ' ;
        //$sql .= 'sc.storecategories_id IN (';
        //$sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->category->lft.' AND '.$this->category->rgt.')';
        $sql .=  'p.companies_id=' . $this->params['id'];
        $sql .=  ' AND p.parent_id = 0';

        $count_sql = $count_sql_start . $sql;
        $sql = $sql_start . $sql;

        //eDebug($sql);
        $order = 'id'; //$this->config['orderby'];
        $dir = 'DESC'; //$this->config['orderby_dir'];
        //eDebug($this->config);

        $page = new expPaginator(array(
            'model_field'=>'product_type',
            'sql'=>$sql,
            'count_sql'=>$count_sql,
            'limit'=>$this->config['pagination_default'],
            'order'=>$order,
            'dir'=>$dir,
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('Model #')=>'model',
                gt('Product Name')=>'title',
                gt('Price')=>'base_price'
            ),
        ));

        //$ancestors = $this->category->pathToNode();
        //$categories = ($this->parent == 0) ? $this->category->getTopLevel(null,false,true) : $this->category->getChildren(null,false,true);
        //eDebug($page);
        //$rerankSQL = "SELECT DISTINCT p.* FROM ".DB_TABLE_PREFIX."_product p JOIN ".DB_TABLE_PREFIX."_product_storeCategories sc ON  p.id = sc.product_id WHERE sc.storecategories_id=".$this->category->id." ORDER BY rank ASC";
        //eDebug($router);
        $defaultSort = $router->current_url;
        assign_to_template(array(
            'record'=>new company($this->params['id']),
            'page'=>$page,
            'defaultSort'=>$defaultSort
        ));
    }

    //TODO this is a misnomer as we only accept an id NOT a title and duplicates the show() method
    function showByTitle()
    {
        global $db, $user, $router;
        //eDebug($this->params,true);

        expHistory::set('viewable', $this->params);

        $count_sql_start = 'SELECT COUNT(DISTINCT p.id) as c FROM ' . $db->tableStmt('product') . ' p ';

        $sql_start  = 'SELECT DISTINCT p.* FROM ' . $db->tableStmt('product') . ' p ';
        //$sql = 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ON p.id = sc.product_id ';
        $sql = 'WHERE ';
        if (!$user->isAdmin()) $sql .= '(p.active_type=0 OR p.active_type=1) AND ' ;
        //$sql .= 'sc.storecategories_id IN (';
        //$sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->category->lft.' AND '.$this->category->rgt.')';
        $sql .=  'p.companies_id=' . $this->params['id'];

        $count_sql = $count_sql_start . $sql;
        $sql = $sql_start . $sql;

        //eDebug($sql);
        $order = 'id'; //$this->config['orderby'];
        $dir = 'DESC'; //$this->config['orderby_dir'];
        //eDebug($this->config);

        $page = new expPaginator(array(
            'model_field'=>'product_type',
            'sql'=>$sql,
            'count_sql'=>$count_sql,
            'limit'=>$this->config['pagination_default'],
            'order'=>$order,
            'dir'=>$dir,
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('Model #')=>'model',
                gt('Product Name')=>'title',
                gt('Price')=>'base_price'
            ),
        ));

        //$ancestors = $this->category->pathToNode();
        //$categories = ($this->parent == 0) ? $this->category->getTopLevel(null,false,true) : $this->category->getChildren(null,false,true);
        //eDebug($page);
        //$rerankSQL = "SELECT DISTINCT p.* FROM ".DB_TABLE_PREFIX."_product p JOIN ".DB_TABLE_PREFIX."_product_storeCategories sc ON  p.id = sc.product_id WHERE sc.storecategories_id=".$this->category->id." ORDER BY rank ASC";
        //eDebug($router);
        $defaultSort = $router->current_url;
        assign_to_template(array(
            'record'=>new company($this->params['id']),
            'page'=>$page,
            'defaultSort'=>$defaultSort
        ));
    }
}

?>