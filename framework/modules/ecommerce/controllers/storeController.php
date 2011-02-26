<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Created by Adam Kessler @ 05/28/2008
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

class storeController extends expController {
    public $basemodel_name = 'product';
    
    public $useractions = array(
        'showall'=>'Show all products & categories',
        'showall_featured_products'=>'Show all featured products',
        'upcoming_events'=>'Show all upcoming events',
        'showallSubcategories'=>'Show subcategories to the current category.',
        'showallManufacturers'=>'Show products by manufacturer',
        'quicklinks'=>'Quick Links for Users',
        'showTopLevel'=>'Show Top Level Store Categories',
        'search_by_model_form'=>'Product Search - By Model',
		'events_calendar'=>'Show events in a calendar'
    );
    
    // hide the configs we don't need
    public $remove_configs = array(
        'comments',
        'ealerts',
        'files',
        'rss',
        'aggregation',
        'tags'
    );
    
    //protected $permissions = array_merge(array("test"=>'Test'), array('copyProduct'=>"Copy Product"));
    protected $add_permissions = array('copyProduct'=>"Copy Product",'delete_children'=>"Delete Children", 'import'=>'Import Products', 'export'=>'Export Products','findDupes'=>'Fix Duplicate SEF Names');
     
    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Ecommerce Store Front"; }
    function description() { return "Use this module to display products and categories of you Ecommerce store"; }
    function author() { return "OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return true; }
    function canImportData() { return true; }
    function canExportData() { return true; }

    function __construct($src=null,$params=array()) {
        global $db, $router, $section, $user;
        parent::__construct($src=null,$params);
        
        // we're setting the config here globably
        $this->grabConfig();          
        //eDebug($this->config);
        if (expTheme::inAction() && !empty($router->url_parts[1]) && ($router->url_parts[0]=="store"&&$router->url_parts[1]=="showall")) {
            if (isset($router->url_parts[array_search('title',$router->url_parts)+1]) && is_string($router->url_parts[array_search('title',$router->url_parts)+1])) {
                $default_id = $db->selectValue('storeCategories', 'id', "sef_url='".$router->url_parts[array_search('title',$router->url_parts)+1]."'");
                $active = $db->selectValue('storeCategories', 'is_active', "sef_url='".$router->url_parts[array_search('title',$router->url_parts)+1]."'");
                if (empty($active) && $user->is_acting_admin!=1) {
                    redirect_to(array("section"=>SITE_DEFAULT_SECTION));
                }
                expSession::set('catid',$default_id);
            }
        } elseif (expTheme::inAction() && !empty($router->url_parts[1]) && ($router->url_parts[0]=="store" && ($router->url_parts[1]=="show" || $router->url_parts[1]=="showByTitle"))) {
            if (isset($router->url_parts[array_search('id',$router->url_parts)+1])&&($router->url_parts[array_search('id',$router->url_parts)+1]!=0)) {
                $default_id = $db->selectValue('product_storeCategories', 'storecategories_id', "product_id='".$router->url_parts[array_search('id',$router->url_parts)+1]."'");
                expSession::set('catid',$default_id);
            } else {
                $prod_id = $db->selectValue('product', 'id', "sef_url='".$router->url_parts[array_search('title',$router->url_parts)+1]."'");
                $default_id = $db->selectValue('product_storeCategories', 'storecategories_id', "product_id='".$prod_id."'");
                expSession::set('catid',$default_id);
            }
        } elseif ($this->config['show_first_category'] || (!expTheme::inAction() && $section==SITE_DEFAULT_SECTION)) {
            $default_id = $db->selectValue('storeCategories', 'id', 'lft=1');
            expSession::set('catid',$default_id);
        } elseif (!$this->config['show_first_category'] && !expTheme::inAction()) {
            expSession::set('catid',0);
        } else {
            $default_id = 0;
        }

        // figure out if we need to show all categories and products or default to showing the first category.
        // elseif (!empty($this->config['category'])) {
        //     $default_id = $this->config['category'];
        // } elseif (ecomconfig::getConfig('show_first_category')) {
        //     $default_id = $db->selectValue('storeCategories', 'id', 'lft=1');
        // } else {
        //     $default_id = 0;
        // }

        $this->parent = expSession::get('catid');
        $this->category = new storeCategory($this->parent);
        // we're setting the config here for the category
        $this->grabConfig($this->category);          
    }

    function showall() {
        global $db, $user, $router;
        
        expHistory::set('viewable', $this->params);
        
        if (empty($this->category->is_events)) {
            $count_sql_start = 'SELECT COUNT(DISTINCT p.id) FROM '.DB_TABLE_PREFIX.'_product p ';
            
            
            $sql_start  = 'SELECT DISTINCT p.* FROM '.DB_TABLE_PREFIX.'_product p ';            
            $sql = 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ON p.id = sc.product_id ';
            $sql .= 'WHERE ';
            if ( !($user->is_admin || $user->is_acting_admin) ) $sql .= '(p.active_type=0 OR p.active_type=1) AND ' ;
            $sql .= 'sc.storecategories_id IN (';
            $sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->category->lft.' AND '.$this->category->rgt.')';         
            
            $count_sql = $count_sql_start . $sql;
            $sql = $sql_start . $sql;
            
            $order = 'sc.rank'; //$this->config['orderby'];
            $dir = 'ASC'; $this->config['orderby_dir'];
            //eDebug($this->config);
        } else {
            $sql_start  = 'SELECT DISTINCT p.*, er.event_starttime, er.signup_cutoff FROM '.DB_TABLE_PREFIX.'_product p ';
            $count_sql_start = 'SELECT COUNT(DISTINCT p.id), er.event_starttime, er.signup_cutoff FROM '.DB_TABLE_PREFIX.'_product p ';
            $sql .= 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ON p.id = sc.product_id ';
            $sql .= 'JOIN '.DB_TABLE_PREFIX.'_eventregistration er ON p.product_type_id = er.id ';
            $sql .= 'WHERE sc.storecategories_id IN (';
            $sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->category->lft.' AND '.$this->category->rgt.')'; 
            if ($this->category->hide_closed_events) {
                $sql .= ' AND er.signup_cutoff > '.time();
            }        
            
            $count_sql = $count_sql_start . $sql;
            $sql = $sql_start . $sql;
                   
            $order = 'event_starttime';
            $dir = 'ASC';
        }
        
        if($this->category->find('count') > 0) {   
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
        } else {
            $page = new expPaginator(array(
                'model_field'=>'product_type',
                'sql'=>'SELECT * FROM '.DB_TABLE_PREFIX.'_product WHERE 1',
                'limit'=>$this->config['pagination_default'],
                'order'=>$order,
                'dir'=>$dir,
                'controller'=>$this->params['controller'],
                'action'=>$this->params['action'],
                'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price'),
                ));
        }

        $ancestors = $this->category->pathToNode();   
        $categories = ($this->parent == 0) ? $this->category->getTopLevel(null,false,true) : $this->category->getChildren(null,false,true);
        
        $rerankSQL = "SELECT DISTINCT p.* FROM ".DB_TABLE_PREFIX."_product p JOIN ".DB_TABLE_PREFIX."_product_storeCategories sc ON  p.id = sc.product_id WHERE sc.storecategories_id=".$this->category->id." ORDER BY rank ASC";
        //eDebug($router);
        $defaultSort = $router->current_url;
        assign_to_template(array('page'=>$page, 'defaultSort'=>$defaultSort, 'ancestors'=>$ancestors, 'categories'=>$categories, 'current_category'=>$this->category,'rerankSQL'=>$rerankSQL));
    }
    
    function grabConfig($category=null){
        
        // grab the configs for the category
        if (is_object($category)) 
        {
            $ctcfg->mod = "storeCategory";
            $ctcfg->src = "@store-".$category->id;
            $ctcfg->int = "";            
            $catConfig = new expConfig($ctcfg);
        }         
      
        // since router maps strip off src and we need that to pull configs, we won't get the configs
        // of the page is router mapped. We'll ensure we do here:
        $cfg->mod = "ecomconfig";
        $cfg->src = "@globalstoresettings";
        $cfg->int = "";
        $config = new expConfig($cfg);

        $this->config = (empty($catConfig->config) || @$catConfig->config['use_global']==1) ? $config->config : $catConfig->config;        
    }
    
    function upcoming_events() {
        $sql  = 'SELECT DISTINCT p.*, er.event_starttime, er.signup_cutoff FROM '.DB_TABLE_PREFIX.'_product p ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_eventregistration er ON p.product_type_id = er.id ';
        $sql .= 'WHERE 1 AND er.signup_cutoff > '.time();
      
        $limit = empty($this->config['event_limit']) ? 10 : $this->config['event_limit'];
        $order = 'event_starttime';
        $dir = 'ASC';
        
        $page = new expPaginator(array(
            'model_field'=>'product_type',
            'sql'=>$sql,
            'limit'=>$limit,
            'order'=>$order,
            'dir'=>$dir,
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price'),
            ));
        
        assign_to_template(array('page'=>$page));
    }
	
    function events_calendar() {
    	global $db;
		
        expHistory::set('viewable', $this->params);
		
		if (!defined("SYS_DATETIME")) include_once(BASE."subsystems/datetime.php");
        if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
		
		$time = isset($this->params['time']) ? $this->params['time'] : time();
        assign_to_template(array('time'=>$time));
		
		$monthly = array();
        $counts = array();
        
        $info = getdate($time);
        $nowinfo = getdate(time());
        if ($info['mon'] != $nowinfo['mon']) $nowinfo['mday'] = -10;
        // Grab non-day numbers only (before end of month)
        $week = 0;
        $currentweek = -1;
        
        $timefirst = mktime(12,0,0,$info['mon'],1,$info['year']);
        $infofirst = getdate($timefirst);
		
        if ($infofirst['wday'] == 0) {
            $monthly[$week] = array(); // initialize for non days
            $counts[$week] = array();
        }
        for ($i = 1 - $infofirst['wday']; $i < 1; $i++) {
            $monthly[$week][$i] = array();
            $counts[$week][$i] = -1;
        }
        $weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
        
        // Grab day counts (deprecated, handled by the date function)
        // $endofmonth = exponent_datetime_endOfMonthDay($time);
        
        $endofmonth = date('t', $time);
		
        
        for ($i = 1; $i <= $endofmonth; $i++) {
            $start = mktime(0,0,0,$info['mon'],$i,$info['year']);
            if ($i == $nowinfo['mday']) $currentweek = $week;
           
		    $dates = $db->selectObjects("eventregistration","`eventdate` = $start");
            $monthly[$week][$i] = storeController::_getEventsForDates($dates);
			
            $counts[$week][$i] = count($monthly[$week][$i]);
            if ($weekday >= 6) {
                $week++;
                $monthly[$week] = array(); // allocate an array for the next week
                $counts[$week] = array();
                $weekday = 0;
            } else $weekday++;
        }
        // Grab non-day numbers only (after end of month)
        for ($i = 1; $weekday && $i < (8-$weekday); $i++) {
            $monthly[$week][$i+$endofmonth] = array();
            $counts[$week][$i+$endofmonth] = -1;
        }
        
		assign_to_template(array(
		    'currentweek'=>$currentweek,
			'monthly'=>$monthly,
			'counts'=>$counts,
			'nextmonth'=>$timefirst+(86400*45),
			'prevmonth'=>$timefirst-(86400*15),
			'now'=>$timefirst
		));
    }
	
	/*
	 * Helper function for the Calendar view
	 */
	function _getEventsForDates($edates,$sort_asc = true) {		
        if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
        if ($sort_asc && !function_exists('exponent_sorting_byEventStartAscending')) {
            function exponent_sorting_byEventStartAscending($a,$b) {
                return ($a->eventstart < $b->eventstart ? 1 : -1);
            }
        }
        if (!$sort_asc && !function_exists('exponent_sorting_byEventStartDescending')) {
            function exponent_sorting_byEventStartDescending($a,$b) {
                return ($a->eventstart < $b->eventstart ? 1 : -1);
            }
        }
        
        global $db;
        $events = array();
        foreach ($edates as $edate) {
        	if (!isset($this->params['cat'])) {
	            if (isset($this->params['title']) && is_string($this->params['title'])) {
	                $default_id = $db->selectValue('storeCategories', 'id', "sef_url='".$this->params['title']."'");
	            } elseif (!empty($this->config['category'])) {
	                $default_id = $this->config['category'];
	            } elseif (ecomconfig::getConfig('show_first_category')) {
	                $default_id = $db->selectValue('storeCategories', 'id', 'lft=1');
	            } else {
	                $default_id = 0;
	            }
	        }
	        
	        $parent = isset($this->params['cat']) ? intval($this->params['cat']) : $default_id;
	        
	        $category = new storeCategory($parent);
	        
            $sql  = 'SELECT DISTINCT p.*, er.event_starttime, er.signup_cutoff FROM '.DB_TABLE_PREFIX.'_product p ';
            $sql .= 'JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ON p.id = sc.product_id ';
            $sql .= 'JOIN '.DB_TABLE_PREFIX.'_eventregistration er ON p.product_type_id = er.id ';
            $sql .= 'WHERE sc.storecategories_id IN (';
            $sql .= 'SELECT id FROM exponent_storeCategories WHERE rgt BETWEEN '.$category->lft.' AND '.$category->rgt.')'; 
            if ($category->hide_closed_events) {
                $sql .= ' AND er.signup_cutoff > '.time();
            }
			$sql .= ' AND er.id = '.$edate->id;      
                    
            $order = 'event_starttime';
            $dir = 'ASC';
			
            $o = $db->selectObjectBySql($sql);
            $o->eventdate = $edate->eventdate;
            $o->eventstart += $edate->event_starttime;
            $o->eventend += $edate->event_endtime;
            $events[] = $o;
        }
        if ($sort_asc == true) {
            usort($events,'exponent_sorting_byEventStartAscending');
        } else {
            usort($events,'exponent_sorting_byEventStartDescending');
        }
        return $events;
    }
    
    function categoryBreadcrumb() {
        global $db, $router;
        
        //eDebug($this->category);

        /*if(isset($router->params['action']))
        {
            $ancestors = $this->category->pathToNode();       
        }else if(isset($router->params['section']))
        {
            $current = $db->selectObject('section',' id= '.$router->params['section']);
            $ancestors[] = $current;
            if( $current->parent != -1 || $current->parent != 0 )
            {                   
                while ($db->selectObject('section',' id= '.$router->params['section']);)
                    if ($section->id == $id) {
                        $current = $section;
                        break;
                    }
                }
            }
            eDebug($sections);
            $ancestors = $this->category->pathToNode();       
        }*/      
        
        $ancestors = $this->category->pathToNode();       
        // eDebug($ancestors);
        assign_to_template(array('ancestors'=>$ancestors));
    }

    function showallUncategorized() {
        expHistory::set('viewable', $this->params);
        
        $sql  = 'SELECT p.* FROM '.DB_TABLE_PREFIX.'_product p JOIN '.DB_TABLE_PREFIX.'_product_storeCategories ';
        $sql .= 'sc ON p.id = sc.product_id WHERE sc.storecategories_id = 0 AND parent_id=0';
        
        $page = new expPaginator(array(
            'model_field'=>'product_type',
            'sql'=>$sql,
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price'),
            ));
            
        assign_to_template(array('page'=>$page, 'moduletitle'=>'Uncategorized Products'));
    }
    
    function manage() {
        expHistory::set('managable', $this->params);
        $page = new expPaginator(array(
            'model'=>'product',
            'where'=>'parent_id=0',
            'order'=>'title',
            'columns'=>array('Type'=>'product_type', 'Model #'=>'model', 'Product Name'=>'title','Price'=>'base_price')
            ));
        assign_to_template(array('page'=>$page));
    }
    
    function showallByManufacturer() {
        global $template;
        
        expHistory::set('viewable', $this->params);
        
        $page = new expPaginator(array(
            'model'=>'product',
            'where'=>'companies_id='.$this->params['id'] . ' AND parent_id=0',
            'default'=>'Product Name',
            'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price')
            ));
        
        $company = new company($this->params['id']);
        
        assign_to_template(array('page'=>$page, 'company'=>$company));
    }

    function showallManufacturers() {
        global $db;
        expHistory::set('viewable', $this->params);
        $sql = 'SELECT comp.* FROM '.DB_TABLE_PREFIX.'_companies as comp JOIN '.DB_TABLE_PREFIX.'_product AS prod ON prod.companies_id = comp.id WHERE parent_id=0 GROUP BY comp.title ORDER BY comp.title;';
        $manufacturers = $db->selectObjectsBySql($sql);
        assign_to_template(array('manufacturers'=>$manufacturers));
    }
    
    function show() {
        global $db, $order, $template, $user;
        
        $classname = $db->selectValue('product', 'product_type', 'id='.$this->params['id']);
        $product = new $classname($this->params['id'], true, true);
        
        if ($product->active_type == 1)
        {
            $product_type->user_message = "This product is temporarily unavailable for purchase.";   
        }elseif ($product->active_type == 2 && !($user->is_admin || $user->is_acting_admin))
        {
            flash("error", $product->title ." is curently unavailable.");
            expHistory::back();   
        }elseif ($product->active_type == 2 && ($user->is_admin || $user->is_acting_admin))
        {
            $product_type->user_message = $product->title ." is curently marked as unavailable for purchase or display.  Normal users will not see this product.";
        }
        expHistory::set('viewable', $this->params);    
        
        // $parent = isset($this->params['cat']) ? intval($this->params['cat']) : $default_id;
        // $category = new storeCategory($parent);
        // $this->grabConfig($category);
                                                                      
        $product_type = new $product->product_type($product->id, false, false);
        //eDebug($product_type);    
        //if we're trying to view a child product directly, then we redirect to it's parent show view
        if (!empty($product->parent_id)) redirect_to(array('controller'=>'store','action'=>'showByTitle','title'=>$product->sef_url));
        
        foreach ($product->crosssellItem as &$csi) {
            $csi->getAttachableItems();
        }
        
      //   eDebug($product); 
         
         
        $tpl = $product_type->getForm('show');
        
        if (!empty($tpl)) $template = new controllerTemplate($this, $tpl);
        $this->grabConfig();     
        //eDebug($product);
        assign_to_template(array('config'=>$this->config, 'product'=>$product, 'last_category'=>$order->lastcat));
    }

    
    function showByTitle() {
        global $order, $template, $user;
        //need to add a check here for child product and redirect to parent if hit directly by ID
        expHistory::set('viewable', $this->params);
        $product = new product(addslashes($this->params['title']));
        $product_type = new $product->product_type($product->id);
        //eDebug($product_type);
        //if we're trying to view a child product directly, then we redirect to it's parent show view
        //bunk URL, no product found
        if(empty($product->id))
        {
            redirect_to(array('controller'=>'notfound','action'=>'page_not_found','title'=>$this->params['title']));            
        }
        if (!empty($product->parent_id)) redirect_to(array('controller'=>'store','action'=>'showByTitle','title'=>$product->sef_url));
        if ($product->active_type == 1)
        {
            $product_type->user_message = "This product is temporarily unavailable for purchase.";   
        }elseif ($product->active_type == 2 && !($user->is_admin || $user->is_acting_admin))
        {
            flash("error", $product->title ." is curently unavailable.");
            expHistory::back();   
        }elseif ($product->active_type == 2 && ($user->is_admin || $user->is_acting_admin))
        {
            $product_type->user_message = $product->title ." is curently marked as unavailable for purchase or display.  Normal users will not see this product.";
        }
        foreach ($product_type->crosssellItem as &$csi) {
            $csi->getAttachableItems();
        }
        //eDebug($product->crosssellItem);
        
        $tpl = $product_type->getForm('show');
        //eDebug($product);
        if (!empty($tpl)) $template = new controllerTemplate($this, $tpl);
        $this->grabConfig();     
        assign_to_template(array('config'=>$this->config, 'product'=>$product_type, 'last_category'=>$order->lastcat));
    }

    function showByModel() {
        global $order, $template, $db;
        
        expHistory::set('viewable', $this->params);
        $product = new product();
        $model = $product->find("first", 'model="'.$this->params['model'].'"');
        //eDebug($model);
        $product_type = new $model->product_type($model->id);
        //eDebug($product_type);
        $tpl = $product_type->getForm('show');
        if (!empty($tpl)) $template = new controllerTemplate($this, $tpl);
        //eDebug($template);
        $this->grabConfig();     
        assign_to_template(array('config'=>$this->config, 'product'=>$product_type, 'last_category'=>$order->lastcat));
    }

    
    function showallSubcategories() {
        global $db;
        
        expHistory::set('viewable', $this->params);
        $parent = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : exponent_sessions_get('last_ecomm_category');
        $category = new storeCategory($parent);
        $categories = $category->getEcomSubcategories();
        $ancestors = $category->pathToNode();
        assign_to_template(array('categories'=>$categories, 'ancestors'=>$ancestors, 'category'=>$category));   
    }

    function showall_featured_products() {
        $order = 'title';
        $dir = 'ASC';
        
        $page = new expPaginator(array(
                'model_field'=>'product_type',
                'sql'=>'SELECT * FROM '.DB_TABLE_PREFIX.'_product WHERE is_featured=1',
                'limit'=>ecomconfig::getConfig('pagination_default'),
                'order'=>$order,
                'dir'=>$dir,
                'controller'=>$this->params['controller'],
                'action'=>$this->params['action'],
                'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price'),
                ));
                
        assign_to_template(array('page'=>$page));   
    }
    
    function showTopLevel() {
        $category = new storeCategory(null,false,false);
        //$categories = $category->getEcomSubcategories();
        $categories = $category->getTopLevel();
        $ancestors = $this->category->pathToNode();   
        $curcat = $this->category;

        assign_to_template(array('categories'=>$categories,'curcat'=>$curcat,'topcat'=>@$ancestors[0]));
    }
    
    function billing_config() {

    }
    
    function addContentToSearch() {
        global $db, $router;
        $model = new $this->basemodel_name();
        
        $total = $db->countObjects($model->table);
        
        $count = 1;
        for($i=0;$i<$total;$i+=100) {
            $orderby = 'id LIMIT '.($i+1).', 100';
            $content = $db->selectArrays($model->table, 'parent_id=0', $orderby);            
           
            foreach ($content as $cnt) {
                $origid = $cnt['id'];
                $prod = new product($cnt['id']);
                unset($cnt['id']);
                //$cnt['title'] = $cnt['title'].' - SKU# '.$cnt['model'];
                $cnt['title'] = (isset($prod->expFile['mainimage'][0]) ? '<img src="'.URL_FULL.'thumb.php?id='.$prod->expFile['mainimage'][0]->id.'&w=40&h=40&zc=1" style="float:left;margin-right:5px;" />':'') .$cnt['title']. (!empty($cnt['model']) ? ' - SKU#: '.$cnt['model']:'');
                $search_record = new search($cnt, false, false);
                $search_record->posted = empty($cnt['created_at']) ? null : $cnt['created_at'];
                $search_record->view_link = $router->makeLink(array('controller'=>$this->baseclassname, 'action'=>'showByTitle', 'title'=>$cnt['sef_url']));
                $search_record->ref_type = $this->basemodel_name;
                $search_record->ref_module = 'store';
                $search_record->category = 'Products';
    
                $search_record->original_id = $origid;
                //$search_record->location_data = serialize($this->loc);
                $search_record->save();
                
                $count += 1;
            }
        }
    }
    
    
    function search_by_model_form() {
        //do nothing...just show the view.
    }
    
    function search_by_model() {
        // get the search terms
        $terms = $this->params['search_string'];

        $sql = "model like '%".$terms."%'";

        $page = new expPaginator(array(
			'model'=>'product',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'where'=>$sql,
			'order'=>'title',
			'dir'=>'DESC',
			'columns'=>array('Model #'=>'model','Product Name'=>'title','Price'=>'base_price'),
			));
        
        assign_to_template(array('page'=>$page, 'terms'=>$terms));
    }
    
    function edit() {
        global $db;
        expHistory::set('editable', $this->params);
        
        // first we need to figure out what type of ecomm product we are dealing with
        if (!empty($this->params['id'])) {
            // if we have an id lets pull the product type from the products table.
            $product_type = $db->selectValue('product', 'product_type', 'id='.$this->params['id']);
        } else {
            if (empty($this->params['product_type'])) redirect_to(array('controller'=>'store', 'action'=>'picktype')); 
            $product_type = $this->params['product_type'];             
        }
        
        if (!empty($this->params['id']))
        { 
            $record = new $product_type($this->params['id']);     
            if (!empty($this->user_input_fields) && !is_array($record->user_input_fields)) $record->user_input_fields = expUnserialize($record->user_input_fields);   
        }else{ 
            $record = new $product_type(); 
            $record->user_input_fields = array();
        } 
        
//        if (!empty($this->params['parent_id']))
         
        // get the product options and send them to the form
        $editable_options = array();
        //$og = new optiongroup();
        $mastergroups = $db->selectExpObjects('optiongroup_master', null, 'optiongroup_master');
        //eDebug($mastergroups,true);
        foreach($mastergroups as $mastergroup) {
            // if this optiongroup_master has already been made into an option group for this product
            // then we will grab that record now..if not, we will make a new one.
            $grouprec = $db->selectArray('optiongroup', 'optiongroup_master_id='.$mastergroup->id.' AND product_id='.$record->id);
            //if ($mastergroup->id == 9) eDebug($grouprec,true);
            //eDebug($grouprec);
            if (empty($grouprec)) {
                $grouprec['optiongroup_master_id'] = $mastergroup->id;
                $grouprec['title'] = $mastergroup->title;
                $group = new optiongroup($grouprec);
            } else {
                $group = new optiongroup($grouprec['id']);
            }

            $editable_options[$group->title] = $group;
          
            if (empty($group->option)) {
                foreach ($mastergroup->option_master as $optionmaster) {
                    $opt = new option(array('title'=>$optionmaster->title, 'option_master_id'=>$optionmaster->id), false, false);
                    $editable_options[$group->title]->options[] = $opt;
                }                 
            
            } else {
                if (count($group->option) == count($mastergroup->option_master)) {                
                    $editable_options[$group->title]->options = $group->option;
                } else {
                    // check for any new options or deleted since the last time we edited this product
                    foreach ($mastergroup->option_master as $optionmaster) {
                        $opt_id = $db->selectValue('option', 'id', 'option_master_id='.$optionmaster->id." AND product_id=".$record->id);
                        if (empty($opt_id)) {
                            $opt = new option(array('title'=>$optionmaster->title, 'option_master_id'=>$optionmaster->id), false, false);                            
                        } else {
                            $opt = new option($opt_id);
                        }
                        
                        $editable_options[$group->title]->options[] = $opt;
                    }
                }
            }
            //eDebug($editable_options[$group->title]);        
        }
        //die();
        
       uasort($editable_options,  array("optiongroup", "sortOptiongroups"));
                     
        // get the shipping options and their methods
        $shipping = new shipping();
        foreach ($shipping->available_calculators as $calcid=>$name) {
            $calc = new $name($calcid);
            $shipping_services[$calcid] = $calc->title;
            $shipping_methods[$calcid] = $calc->availableMethods();
        }
        
#        eDebug($shipping_services);
#        eDebug($shipping_methods);

//eDebug($record);
        //if new record and it's a child, then well set the child rank to be at the end
        if (empty($record->id) && $record->isChild()) 
        {               
            $record->child_rank = $db->max('product','child_rank',null,'parent_id=' . $record->parent_id) + 1;
        }
        //eDebug($record,true);
        
        $view='';
        $parent = null;
        if ((isset($this->params['parent_id']) && empty($record->id)))
        {
            //NEW child product
            $view = 'child_edit';
            $parent = new $product_type($this->params['parent_id'], false, true); 
            $record->parent_id = $this->params['parent_id'];
        }elseif ((!empty($record->id) && $record->parent_id!=0)) {
             //EDIT child product
            $view = 'child_edit';
            $parent = new $product_type($record->parent_id, false, true); 
        }else{
            $view = 'edit';
        }
        
        assign_to_template(array(
            'record'=>$record, 
            'parent'=>$parent,
            'form'=>$record->getForm($view), 
            'optiongroups'=>$editable_options, 
            'shipping_services'=>$shipping_services,
            'shipping_methods'=>$shipping_methods,
            //'status_display'=>$status_display->getStatusArray()
        ));
    }
    
    function copyProduct() {
        global $db;
    
        //expHistory::set('editable', $this->params);
        
        // first we need to figure out what type of ecomm product we are dealing with
        if (!empty($this->params['id'])) {
            // if we have an id lets pull the product type from the products table.
            $product_type = $db->selectValue('product', 'product_type', 'id='.$this->params['id']);
        } else {
            if (empty($this->params['product_type'])) redirect_to(array('controller'=>'store', 'action'=>picktype));
            $product_type = $this->params['product_type'];
        }
        
        $record = new $product_type($this->params['id']);
        // get the product options and send them to the form
        $editable_options = array();
        
        $mastergroups = $db->selectExpObjects('optiongroup_master', null, 'optiongroup_master');
        foreach($mastergroups as $mastergroup) {
            // if this optiongroup_master has already been made into an option group for this product
            // then we will grab that record now..if not, we will make a new one.
            $grouprec = $db->selectArray('optiongroup', 'optiongroup_master_id='.$mastergroup->id.' AND product_id='.$record->id);
            //eDebug($grouprec);
            if (empty($grouprec)) {
                $grouprec['optiongroup_master_id'] = $mastergroup->id;
                $grouprec['title'] = $mastergroup->title;
                $group = new optiongroup($grouprec);
            } else {
                $group = new optiongroup($grouprec['id']);
            }

            $editable_options[$group->title] = $group;

            if (empty($group->option)) {
                foreach ($mastergroup->option_master as $optionmaster) {
                    $opt = new option(array('title'=>$optionmaster->title, 'option_master_id'=>$optionmaster->id), false, false);
                    $editable_options[$group->title]->options[] = $opt;
                }
            } else {
                if (count($group->option) == count($mastergroup->option_master)) {                
                    $editable_options[$group->title]->options = $group->option;
                } else {
                    // check for any new options or deleted since the last time we edited this product
                    foreach ($mastergroup->option_master as $optionmaster) {
                        $opt_id = $db->selectValue('option', 'id', 'option_master_id='.$optionmaster->id." AND product_id=".$record->id);
                        if (empty($opt_id)) {
                            $opt = new option(array('title'=>$optionmaster->title, 'option_master_id'=>$optionmaster->id), false, false);                            
                        } else {
                            $opt = new option($opt_id);
                        }
                        
                        $editable_options[$group->title]->options[] = $opt;
                    }
                }
            }
        }
        
        // get the shipping options and their methods
        $shipping = new shipping();
        foreach ($shipping->available_calculators as $calcid=>$name) {
            $calc = new $name($calcid);
            $shipping_services[$calcid] = $calc->title;
            $shipping_methods[$calcid] = $calc->availableMethods();
        }
        
        $record->original_id = $record->id;
        $record->original_model = $record->model;
        $record->id = NULL;
        $record->sef_url = NULL;
        $record->previous_id = NULL; 
        
        if ($record->isChild()) 
        {            
            $record->child_rank = $db->max('product','child_rank',null,'parent_id=' . $record->parent_id) + 1;
        }
        
        assign_to_template(array(
            'record'=>$record, 
            'parent'=>new $product_type($record->parent_id, false, true),
            'form'=>$record->getForm($record->parent_id==0?'edit':'child_edit'),
            'optiongroups'=>$editable_options, 
            'shipping_services'=>$shipping_services,
            'shipping_methods'=>$shipping_methods
        ));
    }
    
    function picktype() {
        $prodfiles = storeController::getProductTypes();
        $products = array();
        // foreach($prodfiles as $filepath=>$classname) {
        //     $prodObj = new $classname();
        //     $products[$classname] = $prodObj->product_name;
        // }
        $products['product'] = 'Product';
        assign_to_template(array('product_types'=>$products));
    }
    
    function update() {
        global $db;
       // eDebug($this->params['optiongroups'],true);
        //eDebug($this->params,true);
        $product_type = isset($this->params['product_type']) ? $this->params['product_type'] : 'product';
        $record = new $product_type();
        
        
        // find required shipping method if needed
        if ($this->params['required_shipping_calculator_id'] > 0) {
            $record->required_shipping_method = $this->params['required_shipping_methods'][$this->params['required_shipping_calculator_id']];
        } else {
            $this->params['required_shipping_calculator_id'] = 0;
        }
        
        //extra fields
        foreach ($this->params['extra_fields_name'] as $xkey=>$xfield)
        {               
            if (!empty($xfield) /*&& !empty($this->params['extra_fields_value'][$xkey])*/) $record->extra_fields[] = array('name'=>$xfield, 'value'=>$this->params['extra_fields_value'][$xkey]); 
        }
        if (is_array($record->extra_fields)) $record->extra_fields = serialize($record->extra_fields);
        else unset($record->extra_fields);
        
        //user input fields                                                                     
        if (isset($this->params['user_input_use']) && is_array($this->params['user_input_use'])){        
            foreach ($this->params['user_input_use'] as $ukey=>$ufield)
            {  
                //eDebug($ufield);
                $record->user_input_fields[] = array('use'=>$this->params['user_input_use'][$ukey], 'name'=>$this->params['user_input_name'][$ukey], 'is_required'=>$this->params['user_input_is_required'][$ukey], 'min_length'=>$this->params['user_input_min_length'][$ukey],'max_length'=>$this->params['user_input_max_length'][$ukey],'description'=>$this->params['user_input_description'][$ukey]);
            }
            $record->user_input_fields = serialize($record->user_input_fields);
        }else{
            $record->user_input_fields = serialize(array());    
        }
        
        //check if we're saving a newly copied product and if we create children also
        $originalId = isset($this->params['original_id']) && isset($this->params['copy_children']) ? $this->params['original_id'] : 0;
        $originalModel = isset($this->params['original_model']) && isset($this->params['copy_children']) ? $this->params['original_model'] : 0;
        
        if (!empty($record->parent_id)) $record->sef_url = '';  //if child, set sef_url to nada
        $record->update($this->params);
        //eDebug($this->params);
        //eDebug($record, true);
               
        if (isset($record->id)) {
            
            $record->saveCategories($this->params['storeCategory']); 
            //eDebug ($this->params['optiongroups'],true);
            if (!empty($this->params['optiongroups'])) {
                //eDebug("OrigId:" . $originalId);
                foreach ($this->params['optiongroups'] as $title=>$group) {
                    if (isset($this->params['original_id']) && $this->params['original_id'] != 0) $group['id'] = '';  //for copying products  
                    //eDebug($group);
                    $optiongroup = new  optiongroup($group);
                    $optiongroup->product_id = $record->id;                                
                    $optiongroup->save();
                    
                    //eDebug($optiongroup,true);
                    foreach ($this->params['optiongroups'][$title]['options'] as $opt_title=>$opt) {
                        if (isset($this->params['original_id']) && $this->params['original_id'] != 0) $opt['id'] = ''; //for copying products
                       // eDebug($opt);
                        $opt['product_id'] = $record->id;
                        $opt['is_default'] = false;
                        $opt['title'] = $opt_title;
                        $opt['optiongroup_id'] = $optiongroup->id;
                        if (isset($this->params['defaults'][$title]) && $this->params['defaults'][$title] == $opt['title']) {
                            $opt['is_default'] = true;
                        }
                        
                        $option = new option($opt);                    
                        $option->save();
                    }
                }
            }

            
            if (!empty($this->params['relatedProducts']) && (empty($originalId) || !empty($this->params['copy_related']))) {
                $relprods = $db->selectObjects('crosssellItem_product',"product_id=".$record->id);
                $db->delete('crosssellItem_product','product_id='.$record->id);
                foreach ($this->params['relatedProducts'] as $key=>$prodid) {
                    $ptype = new product($prodid);
                    $tmp->product_id = $record->id;
                    $tmp->crosssellItem_id = $prodid;
                    $tmp->product_type = $ptype->product_type;
                    $db->insertObject($tmp,'crosssellItem_product');
                    
                   // if (isset($this->params['relateBothWays']) && in_array($prodid,$this->params['relateBothWays']))
                    if (isset($this->params['relateBothWays'][$prodid])) {
                        $tmp->crosssellItem_id = $record->id;
                        $tmp->product_id = $prodid;
                        $tmp->product_type = $ptype->product_type;
                        $db->insertObject($tmp,'crosssellItem_product');
                    }
                    //}
                }
            }
            
            if (!empty($originalId) && !empty($this->params['copy_children']))
            {
                $origProd = new $product_type($originalId);
                $children = $origProd->find('all', 'parent_id=' . $originalId);
                foreach ($children as $child)
                {
                    unset($child->id);
                    $child->parent_id = $record->id;
                    $child->title = $record->title;
                    $child->sef_url = '';
                    if (isset($this->params['adjust_child_price']) && isset($this->params['new_child_price']) && is_numeric($this->params['new_child_price']))
                    {
                        $child->base_price = $this->params['new_child_price'];
                    }
                    if (!empty($originalModel))
                    {
                        /*eDebug($originalModel);
                        eDebug($record->model);
                        eDebug($child->model);*/
                        $child->model = str_ireplace($originalModel, $record->model, $child->model);    
                        //eDebug($child->model);  
                    }                                                                           
                    $child->save();
                }
            }
        }        
        //eDebug($record);
        $record->addContentToSearch();
        expHistory::back();
    }
    
    function delete() {
        global $db;
        
        if (empty($this->params['id'])) return false;
        $product_type = $db->selectValue('product', 'product_type', 'id='.$this->params['id']);
        $product = new $product_type($this->params['id'], true, false);
        //eDebug($product_type);  
        //eDebug($product, true);
        //if (!empty($product->product_type_id)) {
        //$db->delete($product_type, 'id='.$product->product_id);
        //}
        
        $db->delete('option','product_id='.$product->id." AND optiongroup_id IN (SELECT id from ".DB_TABLE_PREFIX."_optiongroup WHERE product_id=".$product->id.")");
        $db->delete('optiongroup', 'product_id='.$product->id);
        //die();
        $db->delete('product_storeCategories', 'product_id='.$product->id.' AND product_type="'.$product_type.'"');
        if ($product->hasChildren())
        {
            $this->deleteChildren();    
        }    
        
        $product->delete();
        
        flash('message', 'Product deleted successfully.');
        expHistory::back();
    }
    
    function quicklinks() {
        //we need to get the total items in the cart so that if the user at least 1 item in order to check out.
        
        $itemcount = 1;
        //eDebug($itemcount);
        assign_to_template(array("itemcount"=>$itemcount));
    }
    
    static public function getProductTypes() {        
	    $paths = array(
	        BASE.'framework/modules/ecommerce/products/datatypes',
	    );
	
	    $products = array();
	    foreach ($paths as $path) {
	        if (is_readable($path)) {
                $dh = opendir($path);
                while (($file = readdir($dh)) !== false) {
                    if (is_readable($path.'/'.$file) && substr($file, -4) == '.php') {
	                    $classname = substr($file, 0, -4);
	                    $products[$path.'/'.$file] = $classname;
                    }
                }
            }
        }
        
        return $products;
    }
    
    function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;
        
        // figure out what metadata to pass back based on the action we are in.
        $action = $_REQUEST['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'');
        switch($action) {
            case 'show':
            case 'showall': //category page
                //$cat = new storeCategory(isset($_REQUEST['title']) ? $_REQUEST['title']: $_REQUEST['id']);
                $cat = $this->category;
                if (!empty($cat)) {
                    $metainfo['title'] = empty($cat->meta_title) ? $cat->title : $cat->meta_title;
                    $metainfo['keywords'] = empty($cat->meta_keywords) ? $cat->title : strip_tags($cat->meta_keywords);
                    $metainfo['description'] = empty($cat->meta_description) ? strip_tags($cat->body) : strip_tags($cat->meta_description);
                }              
            break;
            case 'showByTitle':
                $prod = new product(isset($_REQUEST['title']) ? $_REQUEST['title']: $_REQUEST['id']);
                if (!empty($prod)) {
                    $metainfo['title'] = empty($prod->meta_title) ? $prod->title : $prod->meta_title;
                    $metainfo['keywords'] = empty($prod->meta_keywords) ? $prod->title : strip_tags($prod->meta_keywords);
                    $metainfo['description'] = empty($prod->meta_description) ? strip_tags($prod->body) : strip_tags($prod->meta_description);
                }              
            break;
            default:
                $metainfo = array('title'=>$this->displayname()." - ".SITE_TITLE, 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
        }
        
        // Remove any quotes if there are any.
        $metainfo['title'] =  $this->parseAndTrim($metainfo['title'],1);
        $metainfo['description'] = str_replace('"', '', $this->parseAndTrim($metainfo['description']));
        $metainfo['keywords'] = str_replace('"', '', $this->parseAndTrim($metainfo['keywords']));
        return $metainfo;
    }
    
    private function parseAndTrim($str, $unescape=false)
    {   //“Death from above”? ®
        //echo "1<br>"; eDebug($str);    
        $str = str_replace("<br>"," ",$str);
        $str = str_replace("</br>"," ",$str);
        $str = str_replace("<br/>"," ",$str);
        $str = str_replace("<br />"," ",$str);
        $str = str_replace("’","&rsquo;",$str);
        $str = str_replace("‘","&lsquo;",$str);
        $str = str_replace("®","&#174;",$str);
        $str = str_replace("–","-", $str);
        $str = str_replace("—","&#151;", $str); 
        $str = str_replace("”", "&rdquo;", $str);
        $str = str_replace("“", "&ldquo;", $str);
        $str = str_replace("\r\n"," ",$str); 
        $str = str_replace("¼","&#188;",$str);
        $str = str_replace("½","&#189;",$str);
        $str = str_replace("¾","&#190;",$str);
        if ($unescape) $str = stripcslashes(trim(str_replace("™", "&trade;", $str)));  
        else $str = mysql_escape_string(trim(str_replace("™", "&trade;", $str))); 
        //echo "2<br>"; eDebug($str,die);
        return $str;
    }

    public function deleteChildren()
    {
        //eDebug($data[0],true);
        //if($id!=null) $this->params['id'] = $id;
        //eDebug($this->params,true);        
        $product = new product($this->params['id']);
        //$product = $product->find("first", "previous_id =" . $previous_id);
        //eDebug($product, true);
        if (empty($product->id)) // || empty($product->previous_id)) 
        {
            flash('error', 'There was an error deleting the child products.');
            expHistory::back(); 
        }
        $childrenToDelete = $product->find('all','parent_id='.$product->id);
        foreach ($childrenToDelete as $ctd)
        {
            //fwrite($lfh, "Deleting:" . $ctd->id . "\n");                             
            $ctd->delete();
        }
    }
    
    function cleanSEF($sef_val)
    {
        $ret = str_ireplace('.','',str_ireplace("'", '', str_ireplace(' ', '-', strtolower(trim($sef_val)))));
        $ret = str_ireplace('/','',str_ireplace("(", '', str_ireplace(')', '', $ret)));
        return $ret;
    }
 
    public function search() {
        global $db, $user;
        $sql = "select DISTINCT(p.id) as id, p.title, model, sef_url, f.id as fileid  from " . $db->prefix . "product as p INNER JOIN " . 
        $db->prefix . "content_expFiles as cef ON p.id=cef.content_id INNER JOIN " . $db->prefix . 
        "expFiles as f ON cef.expFiles_id = f.id WHERE ";
        if ( !($user->is_admin || $user->is_acting_admin) ) $sql .= '(p.active_type=0 OR p.active_type=1) AND ' ;
        $sql .= " match (p.title,p.model,p.body) against ('" . $this->params['query'] . 
        "*' IN BOOLEAN MODE) AND p.parent_id=0  GROUP BY p.id "; 
        $sql .= "order by match (p.title,p.model,p.body) against ('" . $this->params['query'] . "*') desc LIMIT 10";
        $res = $db->selectObjectsBySql($sql);
        //eDebug($sql);
        $ar = new expAjaxReply(200, gettext('Here\'s the items you wanted'), $res);
        $ar->send();
    }
}

?>
