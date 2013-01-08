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

class filedownloadController extends expController {
	public $useractions = array(
        'showall'=>'Show all',
        'tags'=>"Tags",
    );
	public $remove_configs = array(
//        'comments',
//        'ealerts',
        'files',
        'rss', // because we do this as a custom tab within the module
    );  // all options: ('aggregation','categories','comments','ealerts','files','pagination','rss','tags')

    static function displayname() { return gt("File Downloads"); }
    static function description() { return gt("This module lets you put files on your website for users to download."); }
    static function isSearchable() { return true; }
	
    function showall() {
        $limit = (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10;
        if (!empty($this->params['view']) && ($this->params['view'] == 'showall_accordion' || $this->params['view'] == 'showall_tabbed')) {
            $limit = '0';
        }
        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
        $page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>$this->aggregateWhereClause(),
            'limit'=>$limit,
            'order'=>$order,
            'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
            'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
            'dontsort'=>!empty($this->config['dontsort']) ? $this->config['dontsort'] : false,
            'groups'=>!isset($this->params['group']) ? array() : array($this->params['group']),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('ID#')=>'id',
                gt('Title')=>'title',
                gt('Body')=>'body'
            ),
        ));

        include_once(BASE.'external/mp3file.php');
        foreach ($page->records as $file) {
            if (!empty($file->expFile['downloadable'][0]) && ($file->expFile['downloadable'][0]->mimetype == "audio/mpeg") && (file_exists(BASE.$file->expFile['downloadable'][0]->directory.'/'.$file->expFile['downloadable'][0]->filename))) {
                $mp3 = new mp3file(BASE.$file->expFile['downloadable'][0]->directory.'/'.$file->expFile['downloadable'][0]->filename);
                $id3 = $mp3->get_metadata();
                if (($id3['Encoding']=='VBR') || ($id3['Encoding']=='CBR')) {
                    $file->expFile['downloadable'][0]->duration = $id3['Length mm:ss'];
                }
            }
        }

		assign_to_template(array(
            'page'=>$page,
            'items'=>$page->records,
            'rank'=>($order==='rank')?1:0
        ));
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
    
    function getRSSContent() {
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
            $rss_item->link = makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'title'=>$item->sef_url));
            $rss_item->description = expString::convertSmartQuotes($item->body);
            $rss_item->author = user::getUserById($item->poster)->firstname.' '.user::getUserById($item->poster)->lastname;
            $rss_item->authorEmail = user::getEmailById($item->poster);
            $rss_item->date = isset($item->publish_date) ? date('r',$item->publish_date) : date('r', $item->created_at);
            if (!empty($item->expCat[0]->title)) $rss_item->category = array($item->expCat[0]->title);

            // Add the attachment/enclosure info
            $rss_item->enclosure = new Enclosure();
            $rss_item->enclosure->url = !empty($item->expFile['downloadable'][0]->url) ? $item->expFile['downloadable'][0]->url : '';
            $rss_item->enclosure->length = !empty($item->expFile['downloadable'][0]->filesize) ? $item->expFile['downloadable'][0]->filesize : '';
            $rss_item->enclosure->type = !empty($item->expFile['downloadable'][0]->mimetype) ? $item->expFile['downloadable'][0]->mimetype : '';
            if ($rss_item->enclosure->type == 'audio/mpeg') $rss_item->enclosure->type = 'audio/mpg';

            // Add iTunes info
            $rss_item->itunes = new iTunes();
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