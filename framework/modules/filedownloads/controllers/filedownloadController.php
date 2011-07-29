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

class filedownloadController extends expController {
	//protected $basemodel_name = '';
	public $useractions = array('showall'=>'Show all');

	public $remove_configs = array(
        'comments',
        'ealerts',
        'files',
        'rss',
		'tags'
    );
    public $codequality = 'beta';

	function displayname() { return "File Downloads"; }
	function description() { return " This module lets you put files on your website for users to download."; }
	function isSearchable() { return true; }
	

    function showall() {
        $modelname = $this->basemodel_name;
        $where = $this->aggregateWhereClause();
        $limit = isset($this->config['limit']) ? $this->config['limit'] : null;
        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
        $dir   = isset($this->config['dir']) ? $this->config['dir'] : 'ASC';
        $page = new expPaginator(array(
                    'model'=>$modelname,
                    'where'=>$where, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'dir'=>$dir,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'src'=>$this->loc->src,
                    'columns'=>array('ID#'=>'id','Title'=>'title', 'Body'=>'body'),
                    ));
                    
		assign_to_template(array('page'=>$page, 'items'=>$page->records, 'modelname'=>$modelname, 'rank'=>($order==='rank')?1:0));
    }
    
    public function downloadfile() {
        if (empty($this->params['fileid'])) {
            flash('error', 'There was an error while trying to download your file.  No File Specified.');
            expHistory::back();
        }
        
        $fd = new filedownload($this->params['fileid']); 
        
               
        if (empty($fd->expFile['downloadable'][0]->id)) {
            flash('error', 'There was an error while trying to download your file.  The file you were looking for could not be found.');
            expHistory::back();
        }        
        
        $fd->downloads += 1;
        $fd->save();
        
        // this will set the id to the id of the actual file..makes the download go right.
        $this->params['id'] = $fd->expFile['downloadable'][0]->id;
        parent::downloadfile();        
    }
    
    public function update() {
	    //FIXME:  Remove this code once we have the new tag implementation	    
	    if (!empty($this->params['tags'])) {
	        global $db;
	        if (isset($this->params['id'])) {
    	        $db->delete('content_expTags', 'content_type="filedownload" AND content_id='.$this->params['id']);
    	    }
    	    
	        $tags = explode(",", $this->params['tags']);
	        
	        foreach($tags as $tag) {
	            $tag = trim($tag);
	            $expTag = new expTag($tag);
	            if (empty($expTag->id)) $expTag->update(array('title'=>$tag));
	            $this->params['expTag'][] = $expTag->id;
	        }
	    }
	    // call expController update to save the file
	    parent::update();
	}
	
	public function showall_by_tags() {
	    global $db;	    

	    // get the tag being passed
        $tag = new expTag($this->params['tag']);

        // find all the id's of the blog posts for this blog module
        $item_ids = $db->selectColumn('filedownloads', 'id', $this->aggregateWhereClause());
                
        // find all the blogs that this tag is attached to
        $items = $tag->findWhereAttachedTo('filedownload');

        // loop the blogs for this tag and find out which ones belong to this module
        $items_by_tags = array();
        foreach($items as $item) {
            if (in_array($item->id, $item_ids)) $items_by_tags[] = $item;
        }

        // create a pagination object for the blog posts and render the action
		$order = 'created_at';
		$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
		
		$page = new expPaginator(array(
		            'records'=>$items_by_tags,
		            'limit'=>$limit,
		            'order'=>$order,
		            'controller'=>$this->baseclassname,
		            'action'=>$this->params['action'],
		            'columns'=>array('Title'=>'title'),
		            ));
		
		assign_to_template(array('page'=>$page));
	}

	public function tags() {
        $blogs = $this->filedownload->find('all');
        $used_tags = array();
        foreach ($blogs as $blog) {
            foreach($blog->expTag as $tag) {
                if (isset($used_tags[$tag->id])) {
                    $used_tags[$tag->id]->count += 1;
                } else {
                    $exptag = new expTag($tag->id);
                    $used_tags[$tag->id] = $exptag;
                    $used_tags[$tag->id]->count = 1;
                }
                
            }
        }
        $used_tags = expSorter::sort(array('array'=>$used_tags,'sortby'=>'title', 'order'=>'ASC', 'ignore_case'=>true));
	    assign_to_template(array('tags'=>$used_tags));
	}    
    function getRSSContent() {
        // this function is very general and will most of the time need to be overwritten and customized
        
        global $db;     
    
        // setup the where clause for looking up records.
        $where = $this->aggregateWhereClause();

        //$items = $db->selectObjects($this->model_table, $where.' ORDER BY created_at');
        $fd = new filedownload();
        $items = $fd->find('all',$where);
        
        //Convert the items to rss items
        $rssitems = array();
        foreach ($items as $key => $item) { 
            $rss_item = new FeedItem();
            $rss_item->title = $item->title;
            $rss_item->description = $item->body;
            $rss_item->date = isset($item->publish_date) ? date('r',$item->publish_date) : date('r', $item->created_at);
            $rss_item->link = makeLink(array('controller'=>$this->classname, 'action'=>'show', 'title'=>$item->sef_url));

            $rss_item->enclosure = new Enclosure();
            //$rss_item->enclosure->url = URL_FULL.'index.php?module=resourcesmodule&action=download_resource&id='.$item->id;
            $rss_item->enclosure->url = $item->expFile['downloadable'][0]->url;
            $rss_item->enclosure->length = $item->expFile['downloadable'][0]->filesize;
            $rss_item->enclosure->type = $item->expFile['downloadable'][0]->mimetype;
            if ($rss_item->enclosure->type == 'audio/mpeg') $rss_item->enclosure->type = 'audio/mpg';
            // Add the item to the array.
            $rssitems[$key] = $rss_item;

        }
        return $rssitems;
    }
	
}

?>
