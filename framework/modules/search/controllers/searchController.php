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

class searchController extends expController {
    public $useractions = array(
        'show'=>'Show Search Form',
        'cloud'=>'Show Tag Cloud'
    );
    public $add_permissions = array(
        'spider'=>'Spider Site'
    );

    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

    function displayname() { return gt("Search Form"); }
    function description() { return gt("Add a form to allow users to search for content on your website."); }
    function hasSources() { return false; }
    function hasContent() { return false; }

    public function search() {
        // include CSS for results
        // auto-include the CSS for pagination links
	    expCSS::pushToHead(array(
		    "unique"=>"search-results",
		    "link"=>$this->asset_path."css/results.css",
		    )
		);
        
        $terms = $this->params['search_string'];
        
        // If magic quotes is on and the user uses modifiers like " (quotes) they get escaped. We don't want that in this case.
        if (get_magic_quotes_gpc()) {
            $terms = stripslashes($terms);
        }
        $terms = htmlspecialchars($terms);
        
        $search = new search();
        $page = new expPaginator(array(
            //'model'=>'search',
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'records'=>$search->getSearchResults($terms),
            //'sql'=>$sql,
            'order'=>'score',
            'dir'=>'DESC',
			));        

        assign_to_template(array('page'=>$page, 'terms'=>$terms));
    }
    
    public static function spider() {
        global $db;
	    $db->delete('search');
	    
	    $searchable_mods = array();
	    $unsearchable_mod = array();

	    foreach (expModules::modules_list() as $mod) {
		    $name = @call_user_func(array($mod,'name'));
		    if (class_exists($mod) && is_callable(array($mod,'spiderContent'))) {
			    if (call_user_func(array($mod,'spiderContent'))) {
				    $mods[$name] = 1;
			    }
		    } else {
			    //$mods[$name] = 0;
		    }
	    }

	    foreach (expModules::listControllers() as $ctlname=>$ctl) {
		    $controller = new $ctlname();		    
		    if (method_exists($controller,'isSearchable') && $controller->isSearchable()) {
			    $mods[$controller->name()] = $controller->addContentToSearch();
		    } else {
		        //$mods[$controller->name()] = 0;
		    }
	    }
	
	    uksort($mods,'strnatcasecmp');
	    assign_to_template(array('mods'=>$mods));
    }
        
    public function show() {
        //no need to do anything..we're just showing the form... so far! MUAHAHAHAHAHAAA!   what?
    }
    
    public function showall() {
        redirect_to(array("controller"=>'search',"action"=>'show'));
    }

    /**
     * tag cloud
     */
    function cloud() {
        global $db;
        expHistory::set('manageable', $this->params);
        $page = new expPaginator(array(
                    'model'=>'expTag',
                    'where'=>null,
                    'limit'=>999,
                    'order'=>"title",
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'src'=>$this->hasSources() == true ? $this->loc->src : null,
                    'columns'=>array(gt('ID#')=>'id',gt('Title')=>'title',gt('Body')=>'body'),
                    ));

        foreach ($db->selectColumn('content_expTags','content_type',null,null,true) as $contenttype) {
            foreach ($page->records as $key => $value) {
                $attatchedat = $page->records[$key]->findWhereAttachedTo($contenttype);
                if (!empty($attatchedat)) {
                    $page->records[$key]->attachedcount = @$page->records[$key]->attachedcount + count($attatchedat);
                    $page->records[$key]->attached[$contenttype] = $attatchedat;
                }
            }
        }
        // trim the tag cloud to our limit.
        $page->records = expSorter::sort(array('array'=>$page->records, 'order'=>'attachedcount DESC', 'type'=>'a'));
        if (!empty($this->config['limit'])) $page->records = array_slice($page->records,0,$this->config['limit']);
        if (!empty($this->config['order']) && $this->config['order'] != 'hits') {
            $page->records = expSorter::sort(array('array'=>$page->records, 'order'=>'title ASC', 'ignore_case'=>true, 'sort_type'=>'a'));
        }
        assign_to_template(array(
            'page'=>$page
        ));
    }

    // some general search stuff
    public function autocomplete() {
        return;
        global $db;
        $mod = new $this->params['model']();
        $srchcol = explode(",",$this->params['searchoncol']);
        /*for ($i=0; $i<count($srchcol); $i++) {
            if ($i>=1) $sql .= " OR ";
            $sql .= $srchcol[$i].' LIKE \'%'.$this->params['query'].'%\'';
        }*/
        //    $sql .= ' AND parent_id=0';
        //eDebug($sql);
        
        //$res = $mod->find('all',$sql,'id',25);
        $sql = "select DISTINCT(p.id), p.title, model, sef_url, f.id as fileid from exponent_product as p INNER JOIN exponent_content_expfiles as cef ON p.id=cef.content_id INNER JOIN exponent_expfiles as f ON cef.expfiles_id = f.id where match (p.title,p.model,p.body) against ('" . $this->params['query'] . "') AND p.parent_id=0 order by match (p.title,p.model,p.body) against ('" . $this->params['query'] . "') desc LIMIT 25";
        //$res = $db->selectObjectsBySql($sql);
        //$res = $db->selectObjectBySql('SELECT * FROM `exponent_product`');
        
        $ar = new expAjaxReply(200, gt('Here\'s the items you wanted'), $res);
        $ar->send();
    }
	
	public function searchQueryReport() {
		global $db;
		
		//Instantiate the search model
		$search = new search();
		
		//Store the keywords that returns nothing
        $badSearch = array();
		$badSearchArr =  array();
		
		//User Records Initialization
		$all_user  = -1;
		$anonymous = -2;
		$uname = array('id'=>array($all_user, $anonymous), 'name'=>array('All Users', 'Anonymous'));

		$user_default = '';
		$where = '';
		
		if(isset($this->params['user_id']) && $this->params['user_id'] != -1) {
			$user_default = $this->params['user_id'];
		}
		
		expHistory::set('manageable', $this->params);

		$ctr  = 2;
		$ctr2 = 0;
		
		//Getting the search users
		$records = $db->selectObjects('search_queries');
		
		
		foreach($records as $item) {
			$u = user::getUserById($item->user_id);

			if($item->user_id == 0) {
				$item->user_id = $anonymous;
			}
			
			if(!in_array($item->user_id, $uname['id'])) {
				$uname['name'][$ctr] = $u->firstname . ' ' . $u->lastname;
				$uname['id'][$ctr] = $item->user_id;
				$ctr++;
			}
			
			$result  = $search->getSearchResults($item->query, true);
			if(empty($result) && !in_array($item->query, $badSearchArr)) {
				$badSearchArr[] = $item->query;
				$badSearch[$ctr2]['query'] = $item->query;
				$badSearch[$ctr2]['count'] = $db->countObjects("search_queries", "query='{$item->query}'");
				$ctr2++;
			}
			
		}
	
		//Check if the user choose from the dropdown
		if(!empty($user_default)) {
			if($user_default == $anonymous) {
				$u_id = 0;
			} else {
				$u_id = $user_default;
			}
			$where .= "user_id = {$u_id}";
		}
	
		//Get all the search query records
		$records = $db->selectObjects('search_queries', $where);
		for($i = 0 ; $i < count($records); $i++) {
			if(!empty($records[$i]->user_id)) {
				$u = user::getUserById($records[$i]->user_id);
				$records[$i]->user = $u->firstname . ' ' . $u->lastname;
			}
		}
		
		$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $order = empty($this->config['order']) ? 'timestamp' : $this->config['order'];
		
        $page = new expPaginator(array(
					'records' => $records,
                    'where'=>1, 
					'model'=>'search_queries',
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
					'action'=>$this->params['action'],
                    'columns'=>array(
						gt('ID')=>'id',
                        gt('Query')=>'query',
                        gt('Timestamp')=>'timestamp',
                        gt('User')=>'user_id',
                        )
                    ));
	
        assign_to_template(array('page'=>$page, 'users'=>$uname, 'user_default' => $user_default, 'badSearch' => $badSearch)); 
		
	}
	
	public function topSearchReport() {
		global $db;
		$limit = TOP_SEARCH;
		
		if(empty($limit)) {
			$limit = 10;
		}

		$count   = $db->countObjects('search_queries');
	
		$records = $db->selectObjectsBySql("SELECT COUNT(query) cnt, query FROM " .DB_TABLE_PREFIX . "_search_queries GROUP BY query ORDER BY cnt DESC LIMIT 0, {$limit}");

        $records_key_arr = array();
        $records_values_arr = array();
		foreach($records as $item) {
			$records_key_arr[] = '"' . $item->query . '"';
			$records_values_arr[] = number_format((($item->cnt / $count)*100), 2);
		}
		$records_key   = implode(",", $records_key_arr);
		$records_values = implode(",", $records_values_arr);
		
		assign_to_template(array('records'=>$records, 'total'=>$count, 'limit' => $limit, 'records_key' => $records_key, 'records_values' => $records_values));
	}

}

?>