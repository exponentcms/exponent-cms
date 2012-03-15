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

class filedownloadController extends expController {
	//protected $basemodel_name = '';
	public $useractions = array(
        'showall'=>'Show all',
        'tags'=>"Tags",
    );
	public $remove_configs = array(
        'comments',
        'ealerts',
        'files',
        'rss'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

	function displayname() { return "File Downloads"; }
	function description() { return " This module lets you put files on your website for users to download."; }
	function isSearchable() { return true; }
	
    function showall() {
        $modelname = $this->basemodel_name;
        $where = $this->aggregateWhereClause();
        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
//        $dir   = isset($this->config['dir']) ? $this->config['dir'] : 'ASC';
        $limit = isset($this->config['limit']) ? $this->config['limit'] : null;
        if (!empty($this->params['view']) && ($this->params['view'] == 'showall_accordion' || $this->params['view'] == 'showall_tabbed')) {
            $limit = 999;
        }

        $page = new expPaginator(array(
                    'model'=>$modelname,
                    'where'=>$where, 
                    'limit'=>$limit,
                    'order'=>$order,
//                    'dir'=>$dir,
                    'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'src'=>$this->loc->src,
                    'columns'=>array('ID#'=>'id','Title'=>'title', 'Body'=>'body'),
                    ));
                    
		assign_to_template(array('page'=>$page, 'items'=>$page->records, 'rank'=>($order==='rank')?1:0));
    }
    
    public function downloadfile() {
        if (empty($this->params['fileid'])) {
            flash('error', gt('There was an error while trying to download your file.  No File Specified.'));
            expHistory::back();
        }
        
        $fd = new filedownload($this->params['fileid']); 
        
               
        if (empty($fd->expFile['downloadable'][0]->id)) {
            flash('error', gt('There was an error while trying to download your file.  The file you were looking for could not be found.'));
            expHistory::back();
        }        
        
        $fd->downloads += 1;
        $fd->save();
        
        // this will set the id to the id of the actual file..makes the download go right.
        $this->params['id'] = $fd->expFile['downloadable'][0]->id;
        parent::downloadfile();        
    }
    
//	public function showall_by_tags() {
//	    global $db;
//
//	    // get the tag being passed
//        $tag = new expTag($this->params['tag']);
//
//        // find all the id's of the filedownload for this filedownload module
////        $item_ids = $db->selectColumn('filedownloads', 'id', $this->aggregateWhereClause());
//        $item_ids = $db->selectColumn('filedownload', 'id', $this->aggregateWhereClause());
//
//        // find all the blogs that this tag is attached to
//        $items = $tag->findWhereAttachedTo('filedownload');
//
//        // loop the filedownload for this tag and find out which ones belong to this module
//        $items_by_tags = array();
//        foreach($items as $item) {
//            if (in_array($item->id, $item_ids)) $items_by_tags[] = $item;
//        }
//
//        // create a pagination object for the filedownload and render the action
//		$order = 'created_at';
//		$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
//
//		$page = new expPaginator(array(
//		            'records'=>$items_by_tags,
//		            'limit'=>$limit,
//		            'order'=>$order,
//		            'controller'=>$this->baseclassname,
//		            'action'=>$this->params['action'],
//		            'columns'=>array('Title'=>'title'),
//		            ));
//
//		assign_to_template(array('page'=>$page,'moduletitle'=>'File Downloads by tag "'.$this->params['tag'].'"'));
//	}

//	public function tags() {
//        $blogs = $this->filedownload->find('all');
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
//
//            }
//        }
////        $used_tags = expSorter::sort(array('array'=>$used_tags,'sortby'=>'title', 'order'=>'ASC', 'ignore_case'=>true));
//        $order = isset($this->config['order']) ? $this->config['order'] : 'title ASC';
//        $used_tags = expSorter::sort(array('array'=>$used_tags, 'order'=>$order, 'ignore_case'=>true));
//	    assign_to_template(array('tags'=>$used_tags));
//	}

    function getRSSContent() {
        // this function is very general and will most of the time need to be overwritten and customized
        include_once(BASE.'external/mp3file.php');

        global $db;     
    
        // setup the where clause for looking up records.
        $where = $this->aggregateWhereClause();

        $order = isset($this->config['order']) ? $this->config['order'] : 'created_at DESC';

        $fd = new filedownload();
        $items = $fd->find('all',$where, $order);
        
        //Convert the items to rss items
        $rssitems = array();
        foreach ($items as $key => $item) { 
            $rss_item = new FeedItem();

            // Add the basic data
            $rss_item->title = expString::convertSmartQuotes($item->title);
            $rss_item->description = expString::convertSmartQuotes($item->body);
            $rss_item->date = isset($item->publish_date) ? date('r',$item->publish_date) : date('r', $item->created_at);
            $rss_item->link = makeLink(array('controller'=>$this->classname, 'action'=>'show', 'title'=>$item->sef_url));
            $rss_item->guid = expUnserialize($item->location_data)->src.'-id#'.$item->id;
            if (!empty($item->expCat[0]->title)) $rss_item->category = array($item->expCat[0]->title);

            // Add the attachment/enclosure info
            $rss_item->enclosure = new Enclosure();
            $rss_item->enclosure->url = $item->expFile['downloadable'][0]->url;
            $rss_item->enclosure->length = $item->expFile['downloadable'][0]->filesize;
            $rss_item->enclosure->type = $item->expFile['downloadable'][0]->mimetype;
            if ($rss_item->enclosure->type == 'audio/mpeg') $rss_item->enclosure->type = 'audio/mpg';

            // Add iTunes info
            $rss_item->itunes->subtitle = expString::convertSmartQuotes($item->title);
            $rss_item->itunes->summary = expString::convertSmartQuotes($item->body);
            $rss_item->itunes->author = user::getUserById($item->poster)->firstname.' '.user::getUserById($item->poster)->lastname;
            $tags = '';
            foreach ($item->expTag as $tag) {
                $tags .= $tag->title.", ";
            }
            if (!empty($tags)) {
                $rss_item->itunes->keywords = $tags;
            }
            if (($rss_item->enclosure->type == "audio/mpg") && (file_exists(BASE.$item->expFile['downloadable'][0]->directory.'/'.$item->expFile['downloadable'][0]->filename))) {
                $mp3 = new mp3file(BASE.$item->expFile['downloadable'][0]->directory.'/'.$item->expFile['downloadable'][0]->filename);
                $id3 = $mp3->get_metadata();
                if (($id3['Encoding']=='VBR') || ($id3['Encoding']=='CBR')) {
                    $rss_item->itunes->duration = $id3['Length mm:ss'];
                }
            } else {
                $rss_item->itunes->duration = 'Unknown';
            }

            // Add the item to the array.
            $rssitems[$key] = $rss_item;

        }
        return $rssitems;
    }
	
}

?>