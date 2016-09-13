<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
//        'facebook',
//        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)
    protected $add_permissions = array(
        'showUnpublished'=>'View Unpublished News',
        'import'=>'Import News Items',
        'export'=>'Export News Items'
    );

    static function displayname() { return gt("News"); }
    static function description() { return gt("Display & manage news type content on your site."); }
    static function isSearchable() { return true; }

    static function canImportData() {
        return true;
    }

    static function canExportData() {
        return true;
    }

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
            'rank'=>($order==='rank')?1:0,
            'params'=>$this->params,
        ));
    }

    public function showall_by_date() {
	    expHistory::set('viewable', $this->params);
        if (!empty($this->params['day'])) {
            $start_date = expDateTime::startOfDayTimestamp(mktime(0, 0, 0, $this->params['month'], $this->params['day'], $this->params['year']));
            $end_date = expDateTime::endOfDayTimestamp(mktime(23, 59, 59, $this->params['month'], $this->params['day'], $this->params['year']));
            $format_date = DISPLAY_DATE_FORMAT;
        } elseif (!empty($this->params['month'])) {
            $start_date = expDateTime::startOfMonthTimestamp(mktime(0, 0, 0, $this->params['month'], 1, $this->params['year']));
            $end_date = expDateTime::endOfMonthTimestamp(mktime(0, 0, 0, $this->params['month'], 1, $this->params['year']));
            $format_date = "%B %Y";
        } elseif (!empty($this->params['year'])) {
            $start_date = expDateTime::startOfYearTimestamp(mktime(0, 0, 0, 1, 1, $this->params['year']));
            $end_date = expDateTime::endOfYearTimestamp(mktime(23, 59, 59, 12, 31, $this->params['year']));
            $format_date = "%Y";
        } else {
            exit();
        }

		$page = new expPaginator(array(
            'model'=>$this->basemodel_name,
//            'where'=>($this->aggregateWhereClause()?$this->aggregateWhereClause()." AND ":"")."publish >= '".$start_date."' AND publish <= '".$end_date."'",
            'where'=>"publish >= '".$start_date."' AND publish <= '".$end_date."'",
            'limit'=>isset($this->config['limit']) ? $this->config['limit'] : 10,
            'order'=>'publish',
            'dir'=>'desc',
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
            'moduletitle'=>gt('News for').' "'.expDateTime::format_date($start_date,$format_date).'"')
        );
	}

    public function show() {
        expHistory::set('viewable', $this->params);
        // figure out if we're looking this up by id or title
        $id = null;
        if (isset($this->params['id'])) {
            $id = $this->params['id'];
        } elseif (isset($this->params['title'])) {
            $id = $this->params['title'];
        }

        $record = new news($id);
        if (empty($record->id))
            redirect_to(array('controller'=>'notfound','action'=>'page_not_found','title'=>$this->params['title']));

        $config = expConfig::getConfig($record->location_data);
        if (empty($this->config))
            $this->config = $config;
        if (empty($this->loc->src)) {
            $r_loc = expUnserialize($record->location_data);
            $this->loc->src = $r_loc->src;
        }

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
            'config'=>$config,
            'params'=>$this->params
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
    
    public function getRSSContent($limit = 0) {
        // pull the news posts from the database
        $items = $this->news->find('all', $this->aggregateWhereClause(), isset($this->config['order']) ? $this->config['order'] : 'publish DESC', $limit);

        //Convert the newsitems to rss items
        $rssitems = array();
        foreach ($items as $key => $item) { 
            $rss_item = new FeedItem();
            $rss_item->title = expString::convertSmartQuotes($item->title);
            $rss_item->link = $rss_item->guid = makeLink(array('controller'=>'news', 'action'=>'show', 'title'=>$item->sef_url));
            $rss_item->description = expString::convertSmartQuotes($item->body);
            $rss_item->author = user::getUserById($item->poster)->firstname.' '.user::getUserById($item->poster)->lastname;
            $rss_item->authorEmail = user::getEmailById($item->poster);
//            $rss_item->date = date(DATE_RSS,$item->publish_date);
            $rss_item->date = $item->publish_date;
            $rssitems[$key] = $rss_item;

            if ($limit && count($rssitems) >= $limit)
                break;
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
                    $rssObject->poster = $rssItem->get_author()->get_name();
                    $rssObject->isRss = true;
					$t = explode(' • ',$rssObject->title);
					$rssObject->forum = $t[0];
					if (!empty($t[1])) {
                        $rssObject->topic = $t[1];
                    } else {
                        $t = explode(' &bull; ',$rssObject->title);
                        $rssObject->forum = $t[0];
                        if (!empty($t[1])) {
                            $rssObject->topic = $t[1];
                        }
                    }
                    $news[] = $rssObject;
                }
            }
            $items = array_merge($items, $news);
        }
        return $items;
    }

    /**
     * additional check for display of search hit, only display published
     *
     * @param $record
     *
     * @return bool
     */
    public static function searchHit($record) {
        $news = new news($record->original_id);
        if (expPermissions::check('showUnpublished', expUnserialize($record->location_data)) || ($news->publish == 0 || $news->publish <= time()) && ($news->unpublish == 0 || $news->unpublish > time())) {
            return true;
        } else {
            return false;
        }
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

    /**
     * Returns Facebook og: meta data
     *
     * @param $request
     * @param $object
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
            if (!empty($object->expFile['images'][0]->is_image)) {
                $metainfo['image'] = $object->expFile['images'][0]->url;
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
            if (!empty($object->expFile['images'][0]->is_image)) {
                $metainfo['image'] = $object->expFile['images'][0]->url;
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

//    function import() {
//        $pullable_modules = expModules::listInstalledControllers('news');
//        $modules = new expPaginator(array(
//            'records' => $pullable_modules,
//            'controller' => $this->loc->mod,
//            'order'   => isset($this->params['order']) ? $this->params['order'] : 'section',
//            'dir'     => isset($this->params['dir']) ? $this->params['dir'] : '',
//            'page'    => (isset($this->params['page']) ? $this->params['page'] : 1),
//            'columns' => array(
//                gt('Title') => 'title',
//                gt('Page')  => 'section'
//            ),
//        ));
//
//        assign_to_template(array(
//            'modules'              => $modules,
//        ));
//    }
//
//    function import_select() {
//        //Get the temp directory to put the uploaded file
//        $directory = "tmp";
//
//        //Get the file save it to the temp directory
//        if ($_FILES["import_file"]["error"] == UPLOAD_ERR_OK) {
//            $file = expFile::fileUpload("import_file", false, false, time() . "_" . $_FILES['import_file']['name'], $directory.'/');
//            if ($file == null) {
//                switch ($_FILES["import_file"]["error"]) {
//                    case UPLOAD_ERR_INI_SIZE:
//                    case UPLOAD_ERR_FORM_SIZE:
//                        $this->params['_formError'] = gt('The file you attempted to upload is too large.  Contact your system administrator if this is a problem.');
//                        break;
//                    case UPLOAD_ERR_PARTIAL:
//                        $this->params['_formError'] = gt('The file was only partially uploaded.');
//                        break;
//                    case UPLOAD_ERR_NO_FILE:
//                        $this->params['_formError'] = gt('No file was uploaded.');
//                        break;
//                    default:
//                        $this->params['_formError'] = gt('A strange internal error has occurred.  Please contact the Exponent Developers.');
//                        break;
//                }
//                expSession::set("last_POST", $this->params);
//                header("Location: " . $_SERVER['HTTP_REFERER']);
//                exit("");
//            } else {
//                $errors = array();
//                $data = expFile::parseDatabase(BASE . $directory . "/" . $file->filename, $errors, 'news');
//                if (!empty($errors)) {
//                    $message = gt('Importing encountered the following errors') . ':<br>';
//                    foreach ($errors as $error) {
//                        $message .= '* ' . $error . '<br>';
//                    }
//                    flash('error', $message);
//                }
//
//                assign_to_template(array(
//                   'items' => $data['news']->records,
//                   'filename' => $directory . "/" . $file->filename,
//                   'source' => $this->params['aggregate'][0]
//               ));
//            }
//        }
//    }
//
//    function import_process() {
//        $filename = $this->params['filename'];
//        $src = $this->params['source'];
//        $selected = $this->params['items'];
//        $errors = array();
//        $data = expFile::parseDatabase(BASE . $filename, $errors, 'news');
//        foreach ($selected as $select) {
//            $item = new news();
//            foreach ($data['news']->records[$select] as $key => $value) {
//                if ($key != 'id' && $key != 'location_data') {
//                    $item->$key = $value;
//                }
//            }
//            $item->id = null;
//            $item->rank = null;
//            $item->location_data = serialize(expCore::makeLocation('news', $src));
//            $item->save();
//        }
//        flash('message', count($selected) . ' ' . gt('News items were imported.'));
//        expHistory::back();
//    }
//
//    function export() {
//        $pullable_modules = expModules::listInstalledControllers('news');
//        $modules = new expPaginator(array(
//            'records' => $pullable_modules,
//            'controller' => $this->loc->mod,
//            'order'   => isset($this->params['order']) ? $this->params['order'] : 'section',
//            'dir'     => isset($this->params['dir']) ? $this->params['dir'] : '',
//            'page'    => (isset($this->params['page']) ? $this->params['page'] : 1),
//            'columns' => array(
//                gt('Title') => 'title',
//                gt('Page')  => 'section'
//            ),
//        ));
//        assign_to_template(array(
//            'modules'              => $modules,
//        ));
//    }
//
//    function export_process() {
//        if (!empty($this->params['aggregate'])) {
//            $selected = $this->params['aggregate'];
//            $where = '(';
//            foreach ($selected as $key=>$src) {
//                if ($key) $where .= ' OR ';
//                $where .= "location_data='" . serialize(expCore::makeLocation('news', $src)) . "'";
//            }
//            $where .= ')';
//
//            $filename = 'news.eql';
//
//            ob_end_clean();
//            ob_start("ob_gzhandler");
//
//            // 'application/octet-stream' is the registered IANA type but
//            //        MSIE and Opera seems to prefer 'application/octetstream'
//            $mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';
//
//            header('Content-Type: ' . $mime_type);
//            header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
//            // IE need specific headers
//            if (EXPONENT_USER_BROWSER == 'IE') {
//                header('Content-Disposition: inline; filename="' . $filename . '"');
//                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//                header('Pragma: public');
//            } else {
//                header('Content-Disposition: attachment; filename="' . $filename . '"');
//                header('Pragma: no-cache');
//            }
//            echo expFile::dumpDatabase('news', 'export', $where);
//            exit; // Exit, since we are exporting
//        }
//        expHistory::back();
//    }

}

?>