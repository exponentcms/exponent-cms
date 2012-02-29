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

class blogController extends expController {
    //public $basemodel_name = '';
    public $useractions = array(
        'showall'=>'Show all', 
        'tags'=>"Tags",
        'authors'=>"Authors",
        'dates'=>"Dates",
    );
    public $remove_configs = array(
//        'ealerts'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')
    public $add_permissions = array(
        'approve'=>"Approve Comments"
    );

    function displayname() { return "Blog"; }
    function description() { return "This module allows you to run a blog on your site."; }
    function author() { return "Phillip Ball - OIC Group, Inc"; }
    function hasSources() { return false; }  // must be explicitly added by config['add_source'] or config['aggregate']
    function isSearchable() { return true; }

    public function showall() {
	    expHistory::set('viewable', $this->params);
		$where = $this->aggregateWhereClause();
        if (!expPermissions::check('edit',$this->loc)) {
            $where = "(publish = 0 or publish <= " . time() . ") AND (unpublish = 0 OR unpublish > ".time().") AND ".$where;
        }
		$order = 'publish';
		$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
		$dir = empty($this->config['sort_dir']) ? 'DESC' : $this->config['sort_dir'];
		
		$page = new expPaginator(array(
		            'model'=>'blog',
		            'where'=>$where, 
		            'limit'=>$limit,
		            'src'=>$this->loc->src,
		            'order'=>$order,
		            'dir'=>$dir,
		            'controller'=>$this->baseclassname,
		            'action'=>$this->params['action'],
		            'columns'=>array('Title'=>'title'),
		            ));
		            
		assign_to_template(array('page'=>$page));
	}
	
//	public function tags() {
//        $blogs = $this->blog->find('all');
//        $used_tags = array();
//        foreach ($blogs as $blog) {
//            foreach($blog->expTag as $tag) {
//                if (isset($used_tags[$tag->id])) {
//                    $used_tags[$tag->id]->count += 1;
//                } else {
//                    $exptag = new expTag($tag->id);
//                    $used_tags[$tag->id] = $exptag;
//                    $used_tags[$tag->id]->count = 1;
//                }
//            }
//        }
//
//        $used_tags = expSorter::sort(array('array'=>$used_tags,'sortby'=>'title', 'order'=>'ASC', 'ignore_case'=>true));
//        $order = isset($this->config['order']) ? $this->config['order'] : 'title ASC';
//        $used_tags = expSorter::sort(array('array'=>$used_tags, 'order'=>$order, 'ignore_case'=>true));
//	    assign_to_template(array('tags'=>$used_tags));
//	}
	
	public function authors() {
        $blogs = $this->blog->find('all');
        $users = array();
        foreach ($blogs as $blog) {
            if (isset($users[$blog->poster])) {
                $users[$blog->poster]->count += 1;
            } else {
                $users[$blog->poster] = new user($blog->poster);
                $users[$blog->poster]->count = 1;
            }
        }
        
	    assign_to_template(array('authors'=>$users));
	}
	
	public function dates() {
	    global $db;

        $where = $this->aggregateWhereClause();
        if (!expPermissions::check('edit',$this->loc)) {
            $where = "(publish = 0 or publish <= " . time() . ") AND (unpublish = 0 OR unpublish > ".time().") AND ".$where;
        }
	    $dates = $db->selectColumn('blog', 'publish', $where, 'publish DESC');
	    $blog_date = array();
        $count = 0;
        $limit = empty($this->config['limit']) ? count($dates) : $this->config['limit'];
	    foreach ($dates as $date) {
	        $year = date('Y',$date);
	        $month = date('n',$date);
	        if (isset($blog_date[$year][$month])) {
	            $blog_date[$year][$month]->count += 1;
	        } else {
                $count++;
                if ($count > $limit) break;
	            $blog_date[$year][$month]->name = date('F',$date);
	            $blog_date[$year][$month]->count = 1;    
	        }
	    }
        if (!empty($blog_date)) {
            ksort($blog_date);
            $blog_date = array_reverse($blog_date,1);
            foreach ($blog_date as $key=>$val) {
                ksort($blog_date[$key]);
                $blog_date[$key] = array_reverse($blog_date[$key],1);
            }
        } else {
            $blog_date = array();
        }
	    //eDebug($blog_date);
	    assign_to_template(array('dates'=>$blog_date));
	}
	
	public function showall_by_date() {
	    expHistory::set('viewable', $this->params);
	    
	    $start_date = expDateTime::startOfMonthTimestamp(mktime(0, 0, 0, $this->params['month'], 1, $this->params['year']));
	    $end_date = expDateTime::endOfMonthTimestamp(mktime(0, 0, 0, $this->params['month'], 1, $this->params['year']));
		$where = ($this->aggregateWhereClause()?$this->aggregateWhereClause()." AND ":"")."publish >= '".$start_date."' AND publish <= '".$end_date."'";
        if (!expPermissions::check('edit',$this->loc)) {
            $where = "(publish = 0 or publish <= " . time() . ") AND (unpublish = 0 OR unpublish > ".time().") AND ".$where;
        }
		$order = 'publish';
		$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
		
		$page = new expPaginator(array(
		            'model'=>'blog',
		            'where'=>$where, 
		            'limit'=>$limit,
		            'order'=>$order,
		            'dir'=>'desc',
		            'controller'=>$this->baseclassname,
		            'action'=>$this->params['action'],
		            'columns'=>array('Title'=>'title'),
		            ));
		            
		assign_to_template(array('page'=>$page,'moduletitle'=>'Blogs by date "'.expDateTime::format_date($start_date).'"'));
	}
	
	public function showall_by_author() {
	    expHistory::set('viewable', $this->params);
	    
//	    $user = user::getByUsername($this->params['author']);
        $user = user::getUserByName($this->params['author']);
		$where = ($this->aggregateWhereClause()?$this->aggregateWhereClause()." AND ":"")."poster=".$user->id;
        if (!expPermissions::check('edit',$this->loc)) {
            $where = "(publish = 0 or publish <= " . time() . ") AND (unpublish = 0 OR unpublish > ".time().") AND ".$where;
        }
		$order = 'publish';
		$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];

		$page = new expPaginator(array(
		            'model'=>'blog',
		            'where'=>$where, 
		            'limit'=>$limit,
		            'order'=>$order,
		            'controller'=>$this->baseclassname,
		            'action'=>$this->params['action'],
		            'columns'=>array('Title'=>'title'),
		            ));
            	    
		assign_to_template(array('page'=>$page,'moduletitle'=>'Blogs by author "'.$this->params['author'].'"'));
	}
	
	public function show() {
	    global $template;	    
	    expHistory::set('viewable', $this->params);
	    $id = isset($this->params['title']) ? $this->params['title'] : $this->params['id'];
	    $blog = new blog($id);
	    
	    // since we are probably getting here via a router mapped url
	    // some of the links (tags in particular) require a source, we will
	    // populate the location data in the template now.
	    $loc = expUnserialize($blog->location_data);
	    
	    assign_to_template(array('__loc'=>$loc,'record'=>$blog));
	}

    /**
     * view items referenced by tags
     */
    function showByTags() {
        global $db;

        // set the history point for this action
        expHistory::set('viewable', $this->params);

        // setup some objects
        $tagobj = new expTag();
        $modelname = empty($this->params['model']) ? $this->basemodel_name : $this->params['model'];
        $model = new $modelname();

        // start building the sql query
        $sql  = 'SELECT DISTINCT m.id FROM '.DB_TABLE_PREFIX.'_'.$model->table.' m ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_'.$tagobj->attachable_table.' ct ';
        $sql .= 'ON m.id = ct.content_id WHERE (';
        $first = true;

        if (isset($this->params['tags'])) {
            $tags = is_array($this->params['tags']) ? $this->params['tags'] : array($this->params['tags']);
        } elseif (isset($this->config['expTags'])) {
            $tags = $this->config['expTags'];
        } else {
            $tags = array();
        }

        foreach ($tags as $tagid) {
            $sql .= ($first) ? 'exptag_id='.intval($tagid) : ' OR exptag_id='.intval($tagid);
            $first = false;
        }
        $sql .= ") AND content_type='".$model->classname."'";
        if (!expPermissions::check('edit',$this->loc)) {
            $sql = "(publish =0 or publish <= " . time() . ") AND (unpublish = 0 OR unpublish > ".time().") AND ". $sql . ' AND private=0';
        }
        if (!expPermissions::check('edit',$this->loc)) {
        }

        // get the objects and render the template
        $tag_assocs = $db->selectObjectsBySql($sql);
        $records = array();
        foreach ($tag_assocs as $assoc) {
            $records[] = new $modelname($assoc->id);
        }

        assign_to_template(array('items'=>$records));
    }

    /**
   	 * The aggregateWhereClause function creates a sql where clause which also includes aggregated module content
   	 *
   	 * @return string
   	 */
   	function aggregateWhereClause() {
        $sql = '';

        if (!$this->hasSources() && empty($this->config['add_source'])) { return $sql; }

        if (!empty($this->config['aggregate'])) $sql .= '(';

        $sql .= "location_data ='".serialize($this->loc)."'";

        if (!empty($this->config['aggregate'])) {
            foreach ($this->config['aggregate'] as $src) {
                $loc = makeLocation($this->baseclassname, $src);
                $sql .= " OR location_data ='".serialize($loc)."'";
            }

            $sql .= ')';
        }
        if (!expPermissions::check('edit',$this->loc)) {
            $sql .= ' AND private = 0';
        }

        return $sql;
    }

	function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;
        
        // figure out what metadata to pass back based on the action 
        // we are in.
        $action = $_REQUEST['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'');
        $modelname = $this->basemodel_name;
        switch($action) {
            case 'showall':
                $metainfo = array('title'=>"Showing all - ".$this->displayname(), 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
            break;
            case 'show':
            case 'showByTitle':
                // look up the record.
                if (isset($_REQUEST['id']) || isset($_REQUEST['title'])) {
                    $lookup = isset($_REQUEST['id']) ? $_REQUEST['id'] :$_REQUEST['title']; 
                    $object = new $modelname($lookup);
                    // set the meta info
                    if (!empty($object)) {
                        $metainfo['title'] = empty($object->meta_title) ? $object->title : $object->meta_title;
                        $metainfo['keywords'] = empty($object->meta_keywords) ? SITE_KEYWORDS : $object->meta_keywords;
                        $metainfo['description'] = empty($object->meta_description) ? SITE_DESCRIPTION : $object->meta_description;
                    }              
                }
            break;
            case 'showall_by_tags':
                // look up the record.
                if (isset($_REQUEST['tag'])) {
                    $object = new expTag($_REQUEST['tag']);
                    // set the meta info
                    if (!empty($object)) {
                        $metainfo['title'] = gt('Showing all Blog Posts tagged with') ." \"" . $object->title . "\"";
                        $metainfo['keywords'] = empty($object->meta_keywords) ? SITE_KEYWORDS : $object->meta_keywords;
                        $metainfo['description'] = empty($object->meta_description) ? SITE_DESCRIPTION : $object->meta_description;
                    }              
                }
            break;
            case 'showall_by_author':
                // look up the record.
                if (isset($_REQUEST['author'])) {
                    // set the meta info
                    $u = user::getUserByName($_REQUEST['author']);
                    
            		switch (DISPLAY_ATTRIBUTION) {
            			case "firstlast":
            				$str = $u->firstname . " " . $u->lastname;
            				break;
            			case "lastfirst":
            				$str = $u->lastname . ", " . $u->firstname;
            				break;
            			case "first":
            				$str = $u->firstname;
            				break;
            			case "username":
            			default:
            				$str = $u->username;
            				break;
            		}
                    
                    if (!empty($str)) {
                        $metainfo['title'] = gt('Showing all Blog Posts written by ') ."\"" . $str . "\"";
                        $metainfo['keywords'] = empty($object->meta_keywords) ? SITE_KEYWORDS : $object->meta_keywords;  //FIXME $object not set
                        $metainfo['description'] = empty($object->meta_description) ? SITE_DESCRIPTION : $object->meta_description;
                    }              
                }
                case 'showall_by_date':
                    // look up the record.
                    if (isset($_REQUEST['month'])) {
            			$mk = mktime(0, 0, 0, $_REQUEST['month'], 01, $_REQUEST['year']);
            			$ts = strftime('%B, %Y',$mk);
                        // set the meta info
                        $metainfo['title'] = gt('Showing all Blog Posts written in ') . $ts ;
                        $metainfo['keywords'] = empty($object->meta_keywords) ? SITE_KEYWORDS : $object->meta_keywords;  //FIXME $object not set
                        $metainfo['description'] = empty($object->meta_description) ? SITE_DESCRIPTION : $object->meta_description;
                    }
            break;
            default:
                //check for a function in the controller called 'action'_meta and use it if so
                $functionName = $action."_meta";
                $mod = new $this->classname;                
                if(method_exists($mod,$functionName))
                {
                    $metainfo = $mod->$functionName($_REQUEST);
                }                    
                else
                {
                    $metainfo = array('title'=>$this->displayname()." - ".SITE_TITLE, 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
                }
        }
        
        return $metainfo;
    }
	
}

?>