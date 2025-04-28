<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
    public $useractions = array(
        'showall'=>'Show All Posts',
        'tags'=>"Show Post Tags",
        'authors'=>"Show Post Authors",
        'categories'=>"Show Post Categories",
        'dates'=>"Show Post Dates",
        'comments'=>"Show Recent Post Comments",
    );
    protected $manage_permissions = array(
//        'approve'=>"Approve Comments",
    );
    public $remove_configs = array(
//        'categories',
//        'ealerts'
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Blog"); }
    static function description() { return gt("Run a blog on your site."); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }
    static function hasSources() { return false; }  // must be explicitly added by config['add_source'] or config['aggregate']
    static function isSearchable() { return true; }

    static function canImportData() {
        return true;
    }

    static function canExportData() {
        return true;
    }

    /**
     * can this module export EAAS data?
     *
     * @return bool
     */
    public static function canHandleEAAS() {
        return true;
    }

    public function showall() {
        global $db;

	    expHistory::set('viewable', $this->params, true);
        if (isset($this->params['cat']) && !is_numeric($this->params['cat'])) {
            $cat = $db->selectObject('expCats', "sef_url='" . $this->params['cat'] . "'");
            if (!empty($cat->id)) {
                $this->params['cat'] = $cat->id;
            }
        }
		$page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>$this->aggregateWhereClause(),
            'limit'=>(isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] :10,
            'order'=>'publish',
            'dir'=>'DESC', //empty($this->config['sort_dir']) ? 'DESC' : $this->config['sort_dir'],
            'categorize'=> empty($this->config['usecategories']) ? false : $this->config['usecategories'],
            'groups'=>!isset($this->params['cat']) ? array() : array($this->params['cat']),
            'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
            'dontsortwithincat'=>true,
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
            'params'=>$this->params,
        ));
        if (isset($this->params['cat'])) {
            assign_to_template(array(
                'moduletitle' => gt('Posts filed under') . ' ' . (empty($page->records[0]->expCat[0]->title) ? $this->config['uncat'] : $page->records[0]->expCat[0]->title),
            ));
        }

	}

	public function authors() {
        global $db;

        expHistory::set('viewable', $this->params);
        $blogs = $db->selectObjectsBySql('SELECT poster, COUNT(poster) as count FROM ' . $db->tableStmt('blog') . ' WHERE ' . $this->aggregateWhereClause() . ' GROUP BY poster;');
        $users = array();
        foreach ($blogs as $blog) {
            $users[$blog->poster] = new user($blog->poster);
            $users[$blog->poster]->count = $blog->count;
        }

//        $blogs = $this->blog->find('all');
//        $users = array();
//        foreach ($blogs as $blog) {
//            if (isset($users[$blog->poster])) {
//                $users[$blog->poster]->count++;
//            } else {
//                $users[$blog->poster] = new user($blog->poster);
//                $users[$blog->poster]->count = 1;
//            }
//        }

	    assign_to_template(array(
            'authors'=>$users
        ));
	}

	public function dates() {
	    global $db;

        expHistory::set('viewable', $this->params);
        $where = $this->aggregateWhereClause();
	    $dates = $db->selectColumn('blog', 'publish', $where, 'publish DESC');
	    $blog_date = array();
        $count = 0;
        $limit = empty($this->config['limit']) ? count($dates) : $this->config['limit'];
        if (!empty($this->params['view']) && $this->params['view'] === 'dates_calendar') {
            $limit = count($dates);
        }
	    foreach ($dates as $date) {
	        $year = date('Y',$date);
	        $month = date('n',$date);
	        if (isset($blog_date[$year][$month])) {
	            $blog_date[$year][$month]->count++;
	        } else {
                $count++;
                if ($count > $limit) break;
                if (!isset($blog_date[$year]) || @count($blog_date[$year]) == 0) {
                    for ($i=1;$i<=12;$i++) {
                        if (!isset($blog_date[$year][$i])) {
                            $blog_date[$year][$i] = new stdClass();
                            $blog_date[$year][$i]->name = date("F", mktime(0, 0, 0, $i, 1));
                            $blog_date[$year][$i]->count = 0;
                        }
                    }
                }
//                $blog_date[$year][$month] = new stdClass();
//	              $blog_date[$year][$month]->name = date('F',$date);
	            $blog_date[$year][$month]->count = 1;
	        }
	    }
        if (!empty($blog_date)) {
            if (!empty($this->config['yearcount']) && $this->params['view'] !== 'dates_calendar') {
                $blog_date = array_slice($blog_date, 0, $this->config['yearcount'], true);
            }
            // sort years
            ksort($blog_date);
            $blog_date = array_reverse($blog_date,1);
            // sort months
            foreach ($blog_date as $yr=>$months) {
                ksort($blog_date[$yr]);
//                $blog_date[$yr] = array_reverse($blog_date[$yr],1);
                if (empty($this->params['view']) || $this->params['view'] === 'dates') {
                    $blog_date[$yr] = array_reverse($blog_date[$yr],1);
                } else {
                    for ($i=1;$i<=12;$i++) {
                        if (!isset($blog_date[$yr][$i]))  {
                            $blog_date[$yr][$i] = new stdClass();
                            $blog_date[$yr][$i]->name = date("F", mktime(0, 0, 0, $i, 1));
                        }
                    }
                }           }
        } else {
            $blog_date = array();
        }
	    //eDebug($blog_date);
	    assign_to_template(array(
            'dates'=>$blog_date
        ));
	}

    public function showall_by_date() {
	    expHistory::set('viewable', $this->params, true);
        if (isset($this->params['month'])) {
            $start_date = expDateTime::startOfMonthTimestamp(mktime(0, 0, 0, $this->params['month'], 1, $this->params['year']));
     	    $end_date = expDateTime::endOfMonthTimestamp(mktime(23, 59, 59, $this->params['month'], 1, $this->params['year']));
             $period = expDateTime::format_date($start_date,"%B %Y");
        } else {
            $start_date = expDateTime::startOfYearTimestamp(mktime(0, 0, 0, 1, 1, $this->params['year']));
     	    $end_date = expDateTime::endOfYearTimestamp(mktime(23, 59, 59, 12, 1, $this->params['year']));
            $period = expDateTime::format_date($start_date,"%Y");
        }

		$page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>($this->aggregateWhereClause()?$this->aggregateWhereClause()." AND ":"")."publish >= '".$start_date."' AND publish <= '".$end_date."'",
            'limit'=>isset($this->config['limit']) ? $this->config['limit'] : 10,
            'order'=>'publish',
            'dir'=>'DESC',
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
            'moduletitle'=>gt('Posts from').' "'.$period.'"')
        );
	}

    public function showall_by_author() {
        expHistory::set('viewable', $this->params, true);

        $this->params['author'] = expString::escape($this->params['author']);
        $user = user::getUserByName($this->params['author']);
        $page = new expPaginator(array(
            'model' => $this->basemodel_name,
            'where' => ($this->aggregateWhereClause() ? $this->aggregateWhereClause() . " AND " : "") . "poster=" . $user->id,
            'limit' => isset($this->config['limit']) ? $this->config['limit'] : 10,
            'order' => 'publish',
            'dir'=>'DESC',
            'page' => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->baseclassname,
            'action' => $this->params['action'],
            'src' => $this->loc->src,
            'columns' => array(
                gt('Title') => 'title'
            ),
        ));

        assign_to_template(array(
            'page' => $page,
            'moduletitle' => gt('Posts by') . ' "' . user::getUserAttribution($user->id) . '"'
        ));
    }

	public function show() {
        expHistory::set('viewable', $this->params, true);
	    $id = isset($this->params['title']) ? $this->params['title'] : $this->params['id'];
        $record = new blog($id);
        if (empty($record->id))  // fixme shouldn't show private/draft/unpublished articles??
            redirect_to(array('controller'=>'notfound','action'=>'page_not_found','title'=>$this->params['title']));

	    // since we are probably getting here via a router mapped url
	    // some of the links (tags in particular) require a source, we will
	    // populate the location data in the template now.
        $config = expConfig::getConfig($record->location_data);
        if (empty($this->config))
            $this->config = $config;
        if (empty($this->loc->src)) {
            $r_loc = expUnserialize($record->location_data);
            $this->loc->src = $r_loc->src;
        }

        $nextwhere = $this->aggregateWhereClause().' AND publish > '.$record->publish.' ORDER BY publish';
        $record->next = $record->find('first',$nextwhere);
        $prevwhere = $this->aggregateWhereClause().' AND publish < '.$record->publish.' ORDER BY publish DESC';
        $record->prev = $record->find('first',$prevwhere);

	    assign_to_template(array(
            'record'=>$record,
            'config'=>$config,
            'params'=>$this->params
        ));
	}

    /**
     * view items referenced by tags
     * @deprecated 2.0.0
     */
    function showByTags() {
        global $db;

        // set the history point for this action
        expHistory::set('viewable', $this->params);

        // setup some objects
        $tagobj = new expTag();
        $modelname = empty($this->params['model']) ? $this->basemodel_name : expString::escape($this->params['model']);
        $model = new $modelname();

        // start building the sql query
        $sql  = 'SELECT DISTINCT m.id FROM ' . $db->tableStmt($model->tablename) . ' m ';
        $sql .= 'JOIN ' . $db->tableStmt($tagobj->attachable_table) . ' ct ';
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
            $sql .= ($first) ? 'exptags_id='.(int)($tagid) : ' OR exptags_id='.(int)($tagid);
            $first = false;
        }
        $sql .= ") AND content_type='".$model->classname."'";
        if (!expPermissions::check('edit',$this->loc)) {
            $sql = "(publish =0 or publish <= " . time() . ")) AND ". $sql . ' AND private=0';
        }

        // get the objects and render the template
        $tag_assocs = $db->selectObjectsBySql($sql);
        $records = array();
        foreach ($tag_assocs as $assoc) {
            $records[] = new $modelname($assoc->id);
        }

        assign_to_template(array(
            'items'=>$records
        ));
    }

    /**
     * get the blog items in an rss feed format
     *
     * @return array
     */
//    function getRSSContent() {
//        $class = new blog();
//        $items = $class->find('all', $this->aggregateWhereClause(), isset($this->config['order']) ? $this->config['order'] : 'publish DESC');
//
//        //Convert the items to rss items
//        $rssitems = array();
//        foreach ($items as $key => $item) {
//            $rss_item = new FeedItem();
//            $rss_item->title = expString::convertSmartQuotes($item->title);
//            $rss_item->link = $rss_item->guid = makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'title'=>$item->sef_url));
//            $rss_item->description = expString::convertSmartQuotes($item->body);
//            $rss_item->author = user::getUserById($item->poster)->firstname.' '.user::getUserById($item->poster)->lastname;
//            $rss_item->authorEmail = user::getEmailById($item->poster);
////            $rss_item->date = isset($item->publish_date) ? date(DATE_RSS,$item->publish_date) : date(DATE_RSS, $item->created_at);
//            $rss_item->date = isset($item->publish_date) ? $item->publish_date : $item->created_at;
////            $rss_item->guid = expUnserialize($item->location_data)->src.'-id#'.$item->id;
//            if (!empty($item->expCat[0]->title)) $rss_item->category = array($item->expCat[0]->title);
//            $comment_count = expCommentController::countComments(array('content_id'=>$item->id,'content_type'=>$this->basemodel_name));
//            if ($comment_count) {
//                $rss_item->comments = makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'title'=>$item->sef_url)).'#exp-comments';
////                $rss_item->commentsRSS = makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'title'=>$item->sef_url)).'#exp-comments';
//                $rss_item->commentsCount = $comment_count;
//            }
//            $rssitems[$key] = $rss_item;
//        }
//        return $rssitems;
//    }

    /**
     * additional check for display of search hit, only display non-draft
     *
     * @param $record
     *
     * @return bool
     */
    public static function searchHit($record) {
        $blog = new blog($record->original_id);
        if (expPermissions::check('edit', expUnserialize($record->location_data)) || ($blog->private == 0 && ($blog->publish === 0 || $blog->publish <= time()))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The aggregateWhereClause function creates a sql where clause which also includes aggregated module content
     *
     * @param string $type
     *
     * @return string
     */
   	function aggregateWhereClause($type='') {
        $sql = parent::aggregateWhereClause();
        if (!expPermissions::check('edit',$this->loc)) {
            if (!empty($sql)) {
                $sql .= " AND ";
            }
            $sql .= "private = 0 AND (publish = 0 OR publish <= " . time() . ")";
        }
        if (empty($sql)) {
            $sql = "1"; // must explicitly be set for next/prev to work
        }

        return $sql;
    }

    /**
     * delete module's items (all) by instance
     *
     * @param bool $loc
     */
    function delete_instance($loc = false) {
        parent::delete_instance(true);
    }

    /**
     * Returns Facebook og: meta data
     *
     * @param $request
     * @param $object
     * @param $canonical
     *
     * @return null
     */
    public function meta_fb($request, $object, $canonical) {
        $metainfo = array();
        $metainfo['type'] = 'article';
        if (!empty($object->body)) {
            $desc = str_replace('"',"'",expString::summarize($object->body,'html','para'));
        } else {
            $desc = SITE_DESCRIPTION;
        }
        $metainfo['title'] = substr(empty($object->meta_fb['title']) ? $object->title : $object->meta_fb['title'], 0, 87);
        $metainfo['description'] = substr(empty($object->meta_fb['description']) ? $desc : $object->meta_fb['description'], 0, 199);
        $metainfo['url'] = empty($object->meta_fb['url']) ? $canonical : $object->meta_fb['url'];
        $metainfo['image'] = empty($object->meta_fb['fbimage'][0]) ? '' : $object->meta_fb['fbimage'][0]->url;
        if (empty($metainfo['image'])) {
            if (!empty($object->expFile[0]->is_image)) {
                $metainfo['image'] = $object->expFile[0]->url;
            } else {
                $config = expConfig::getConfig($object->location_data);
                if (!empty($config['expFile']['fbimage'][0]))
                    $file = new expFile($config['expFile']['fbimage'][0]);
                if (!empty($file->id))
                    $metainfo['image'] = $file->url;
                if (empty($metainfo['image']))
                    $metainfo['image'] = URL_BASE . MIMEICON_RELATIVE . 'generic_22x22.png';
            }
        }
        return $metainfo;
    }

    /**
     * Returns Twitter twitter: meta data
     *
     * @param $request
     * @param $object
     * @param $canonical
     *
     * @return null
     */
    public function meta_tw($request, $object, $canonical) {
        $metainfo = array();
        $metainfo['card'] = 'summary';
        if (!empty($object->body)) {
            $desc = str_replace('"',"'",expString::summarize($object->body,'html','para'));
        } else {
            $desc = SITE_DESCRIPTION;
        }
        $config = expConfig::getConfig($object->location_data);
        if (!empty($object->meta_tw['twsite'])) {
            $metainfo['site'] = $object->meta_tw['twsite'];
        } elseif (!empty($config['twsite'])) {
            $metainfo['site'] = $config['twsite'];
        }
        $metainfo['title'] = substr(empty($object->meta_tw['title']) ? $object->title : $object->meta_tw['title'], 0, 87);
        $metainfo['description'] = substr(empty($object->meta_tw['description']) ? $desc : $object->meta_tw['description'], 0, 199);
        $metainfo['image'] = empty($object->meta_tw['twimage'][0]) ? '' : $object->meta_tw['twimage'][0]->url;
        if (empty($metainfo['image'])) {
            if (!empty($object->expFile[0]->is_image)) {
                $metainfo['image'] = $object->expFile[0]->url;
            } else {
                if (!empty($config['expFile']['twimage'][0]))
                    $file = new expFile($config['expFile']['twimage'][0]);
                if (!empty($file->id))
                    $metainfo['image'] = $file->url;
                if (empty($metainfo['image']))
                    $metainfo['image'] = URL_BASE . MIMEICON_RELATIVE . 'generic_22x22.png';
            }
        }
        return $metainfo;
    }

    function showall_by_author_meta($request) {
        global $router;

        // look up the record.
        if (isset($request['author'])) {
            // set the meta info
            $u = user::getUserByName(expString::sanitize($request['author']));
            $str = user::getUserAttribution($u->id);

            if (!empty($str)) {
                $metainfo = array('title' => '', 'keywords' => '', 'description' => '', 'canonical' => '', 'noindex' => false, 'nofollow' => false);
                $metainfo['title'] = gt('Blog') . ' ' . gt('items') . ' ' . gt('authored by') . ': ' . $str;
//                $metainfo['keywords'] = empty($object->meta_keywords) ? SITE_KEYWORDS : $object->meta_keywords;  //FIXME $object not set
                $metainfo['keywords'] = $str;
//                $metainfo['description'] = empty($object->meta_description) ? SITE_DESCRIPTION : $object->meta_description;  //FIXME $object not set
                $metainfo['description'] = SITE_DESCRIPTION;
//                $metainfo['canonical'] = empty($object->canonical) ? URL_FULL.substr($router->sefPath, 1) : $object->canonical;  //FIXME $object not set
//                $metainfo['canonical'] = URL_FULL.substr($router->sefPath, 1);
                $metainfo['canonical'] = $router->plainPath();

                return $metainfo;
            }
        }
        return array();
    }

    /**
     * returns module's EAAS data as an array of records
     *
     * @return array
     */
    public function eaasData($params=array(), $where=null) {
        $data = array();  // initialize
        if (!empty($params['id'])) {
            $blog = new blog($params['id']);
            $data['records'] = $blog;
        } else {
            $blog = new blog();

            // figure out if we should limit the results
            if (isset($params['limit'])) {
                $limit = $params['limit'] === 'none' ? null : $params['limit'];
            } else {
                $limit = '';
            }

            $order = isset($params['order']) ? $params['order'] : 'publish DESC';
            $items = $blog->find('all', $where, $order, $limit);
            $data['records'] = $items;
        }
        return $data;
    }

}

?>