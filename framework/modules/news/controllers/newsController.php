<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

class newsController extends expController {
    public $useractions = array(
        'showall'=>'Show all News',
        'tags'=>"Tags",
    );
    public $remove_configs = array(
        'categories',
        'comments',
//        'ealerts',
        'facebook',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags')
    public $add_permissions = array(
        'showUnpublished'=>'View Unpublished News'
    );

    static function displayname() { return gt("News"); }
    static function description() { return gt("Display & manage news type content on your site."); }
    static function isSearchable() { return true; }
    
    public function showall() { 
        expHistory::set('viewable', $this->params);
        // figure out if should limit the results
        if (isset($this->params['limit'])) {
            $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
        } else {
            $limit = (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10;
        }       
        $order = isset($this->config['order']) ? $this->config['order'] : 'publish DESC';

        // pull the news posts from the database
        $items = $this->news->find('all', $this->aggregateWhereClause(), $order);

        // merge in any RSS news and perform the sort and limit the number of posts we return to the configured amount.
        if (!empty($this->config['pull_rss'])) $items = $this->mergeRssData($items);
        
        // setup the pagination object to paginate the news stories.
        $page = new expPaginator(array(
            'records'=>$items,
            'limit'=>$limit,
            'order'=>$order,
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'view'=>empty($this->params['view']) ? null : $this->params['view']
        ));
            
        assign_to_template(array(
            'page'=>$page,
            'items'=>$page->records,
            'rank'=>($order==='rank')?1:0
        ));
    }

    public function show() {
        global $db;

        expHistory::set('viewable', $this->params);

        // figure out if we're looking this up by id or title
        $id = null;
        if (isset($this->params['id'])) {
            $id = $this->params['id'];
        } elseif (isset($this->params['title'])) {
            $id = $this->params['title'];
        }

        $record = new news($id);
        $config = expUnserialize($db->selectValue('expConfigs','config',"location_data='".$record->location_data."'"));

        $order = !empty($config['order']) ? $config['order'] : 'publish DESC';
        if (strstr($order," ")) {
            $orderby = explode(" ",$order);
            $order = $orderby[0];
            $order_direction = $orderby[1];
        } else {
            $order_direction = '';
        }
        if ($order_direction == 'DESC') {
            $order_direction_next = '';
        } else {
            $order_direction_next = 'DESC';
        }
        $nextwhere = $this->aggregateWhereClause().' AND '.$order.' > '.$record->$order.' ORDER BY '.$order.' '.$order_direction_next;
        $record->next = $record->find('first',$nextwhere);
        $prevwhere = $this->aggregateWhereClause().' AND '.$order.' < '.$record->$order.' ORDER BY '.$order.' '.$order_direction;
        $record->prev = $record->find('first',$prevwhere);

        assign_to_template(array(
            'record'=>$record,
            'config'=>$config
        ));
    }

    public function showUnpublished() {
        expHistory::set('viewable', $this->params);
        
        // setup the where clause for looking up records.
        $where = parent::aggregateWhereClause();
        $where = "((unpublish != 0 AND unpublish < ".time().") OR (publish > ".time().")) AND ".$where;
        if (isset($this->config['only_featured'])) $where .= ' AND is_featured=1';

        $page = new expPaginator(array(
            'model'=>'news',
            'where'=>$where,
            'limit'=>25,
            'order'=>'unpublish',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Title')=>'title',
                gt('Published On')=>'publish',
                gt('Status')=>'unpublish'
            ),
        ));
            
        assign_to_template(array(
            'page'=>$page
        ));
    }
    
    public function showExpired() {
        redirect_to(array('controller'=>'news', 'action'=>'showUnpublished','src'=>$this->params['src']));
    }
    
//    public function configure() {
//        parent::configure();
//        assign_to_template(array('sortopts'=>$this->sortopts));
//    }
    
    public function saveConfig() { 
        if (!empty($this->params['aggregate']) || !empty($this->params['pull_rss'])) {
            if ($this->params['order'] == 'rank ASC') {
                expValidator::failAndReturnToForm(gt('User defined ranking is not allowed when aggregating or pull RSS data feeds.'), $this->params);
            }
        }
        
        parent::saveConfig();
    }
    
    public function getRSSContent() {
        // pull the news posts from the database
        $order = isset($this->config['order']) ? $this->config['order'] : 'publish DESC';
        $items = $this->news->find('all', $this->aggregateWhereClause(), $order);

        //Convert the newsitems to rss items
        $rssitems = array();
        foreach ($items as $key => $item) { 
            $rss_item = new FeedItem();
            $rss_item->title = $item->title;
            $rss_item->link = makeLink(array('controller'=>'news', 'action'=>'show', 'title'=>$item->sef_url));
            $rss_item->description = $item->body;
            $rss_item->author = user::getUserById($item->poster)->firstname.' '.user::getUserById($item->poster)->lastname;
            $rss_item->authorEmail = user::getEmailById($item->poster);
            $rss_item->date = date('r',$item->publish_date);
            $rssitems[$key] = $rss_item;
        }
        return $rssitems;
    }

    /**
     * Pull RSS Feed and display as news items
     *
     * @param $items
     * @return array
     */
    private function mergeRssData($items) {
        if (!empty($this->config['pull_rss'])) {    
            $RSS = new SimplePie();
	        $RSS->set_cache_location(BASE.'tmp/rsscache');  // default is ./cache
//	        $RSS->set_cache_duration(3600);  // default is 3600
	        $RSS->set_timeout(20);  // default is 10
//	        $RSS->set_output_encoding('UTF-8');  // default is UTF-8
            $news = array();
            foreach($this->config['pull_rss'] as $url) {
                $RSS->set_feed_url($url);
                $feed = $RSS->init();
                if (!$feed) {
                    // an error occurred in the rss.
                    continue;
                }
	            $RSS->handle_content_type();
                foreach ($RSS->get_items() as $rssItem) {
                    $rssObject = new stdClass();
                    $rssObject->title = $rssItem->get_title();
                    $rssObject->body = $rssItem->get_description();
                    $rssObject->rss_link = $rssItem->get_permalink();
                    $rssObject->publish = $rssItem->get_date('U');
                    $rssObject->publish_date = $rssItem->get_date('U');
                    $rssObject->poster = $rssItem->get_author()->name;
                    $rssObject->isRss = true;
					$t = explode(' • ',$rssObject->title);
					$rssObject->forum = $t[0];
					if (!empty($t[1])) $rssObject->topic = $t[1];
                    $news[] = $rssObject;
                }
            }
            $items = array_merge($items, $news);
        }
        return $items;
    }
    
    private function sortDescending($a,$b) {
        return ($a->publish_date > $b->publish_date ? -1 : 1);
    }

    private function sortAscending($a,$b) {
        return ($a->publish_date < $b->publish_date ? -1 : 1);
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
        $sql = "(publish = 0 or publish <= " . time() . ") AND (unpublish=0 OR unpublish > ".time().") AND ".$sql;
        if (isset($this->config['only_featured'])) $sql .= ' AND is_featured=1';
        return $sql;
    }

}

?>