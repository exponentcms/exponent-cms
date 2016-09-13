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
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    public $rss_is_podcast = true;

    static function displayname() { return gt("File Downloads"); }
    static function description() { return gt("Place files on your website for users to download or use as a podcast."); }
    static function isSearchable() { return true; }
	
    function showall() {
        expHistory::set('viewable', $this->params);
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
            'dontsortwithincat'=>!empty($this->config['dontsort']) ? $this->config['dontsort'] : false,
            'groups'=>!isset($this->params['group']) ? array() : array($this->params['group']),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('ID#')=>'id',
                gt('Title')=>'title',
                gt('Description')=>'body'
            ),
        ));

        include_once(BASE.'external/mp3file.php');
        foreach ($page->records as $file) {
            if (!empty($file->expFile['downloadable'][0]) && ($file->expFile['downloadable'][0]->mimetype == "audio/mpeg") && (file_exists(BASE.$file->expFile['downloadable'][0]->directory.$file->expFile['downloadable'][0]->filename))) {
                $mp3 = new mp3file(BASE.$file->expFile['downloadable'][0]->directory.$file->expFile['downloadable'][0]->filename);
                $id3 = $mp3->get_metadata();
                if (($id3['Encoding']=='VBR') || ($id3['Encoding']=='CBR')) {
                    $file->expFile['downloadable'][0]->duration = $id3['Length mm:ss'];
                }
            }
        }

		assign_to_template(array(
            'page'=>$page,
            'items'=>$page->records,
            'rank'=>($order==='rank')?1:0,
            'params'=>$this->params,
        ));
    }

    public function downloadfile() {
        if (empty($this->params['fileid'])) {
            flash('error', gt('There was an error while trying to download your file.  No File Specified.'));
            expHistory::back();
        }
        
        $fd = new filedownload($this->params['fileid']); 
        if (empty($this->params['filenum'])) $this->params['filenum'] = 0;

        if (empty($fd->expFile['downloadable'][$this->params['filenum']]->id)) {
            flash('error', gt('There was an error while trying to download your file.  The file you were looking for could not be found.'));
            expHistory::back();
        }        
        
        $fd->downloads++;
        $fd->save();
        
        // this will set the id to the id of the actual file..makes the download go right.
        $this->params['id'] = $fd->expFile['downloadable'][$this->params['filenum']]->id;
        parent::downloadfile();        
    }

    /**
     * Returns rich snippet PageMap meta data
     *
     * @param $request
     * @param $object
     *
     * @return string
     */
    function meta_rich($request, $object) {
        if (!empty($object->expFile[0]) && file_exists(BASE.$object->expFile[0]->directory.$object->expFile[0]->filename)) {
            $rich_meta = '<!--
        <PageMap>
            <DataObject type="action">
                <Attribute name="label" value="' . gt('Download') . '"/>
                <Attribute name="url" value="' . $object->download_link() . '"/>
                <Attribute name="class" value="download"/>
            </DataObject>
        </PageMap>
    -->';
            return $rich_meta;
        }
    }

    /**
     * Returns Facebook og: meta data
     *
     * @param $request
     * @param $object
     *
     * @return null
     */
    public function meta_fb($request, $object, $canonical)
    {
        $metainfo = array();
        $metainfo['type'] = 'article';
        if (!empty($object->body)) {
            $desc = str_replace('"', "'", expString::summarize($object->body, 'html', 'para'));
        } else {
            $desc = SITE_DESCRIPTION;
        }
        $metainfo['title'] = substr(empty($object->meta_fb['title']) ? $object->title : $object->meta_fb['title'], 0, 87);
        $metainfo['description'] = substr(empty($object->meta_fb['description']) ? $desc : $object->meta_fb['description'], 0, 199);
        $metainfo['url'] = empty($object->meta_fb['url']) ? $canonical : $object->meta_fb['url'];
        $metainfo['image'] = empty($object->meta_fb['fbimage'][0]) ? '' : $object->meta_fb['fbimage'][0]->url;
        if (empty($metainfo['image'])) {
            if (!empty($object->expFile['downloadable'][0]->is_image)) {
                $metainfo['image'] = $object->expFile['downloadable'][0]->url;
            } else {
                $config = expConfig::getConfig($object->location_data);
                if (!empty($config['expFile']['fbimage'][0])) {
                    $file = new expFile($config['expFile']['fbimage'][0]);
                }
                if (!empty($file->id)) {
                    $metainfo['image'] = $file->url;
                }
                if (empty($metainfo['image'])) {
                    $metainfo['image'] = URL_BASE . MIMEICON_RELATIVE . 'generic_22x22.png';
                }
            }
        }
        $mt = explode('/', $object->expFile['downloadable'][0]->mimetype);
        if ($mt[0] == 'audio' || $mt[0] == 'video')  // add an audio/video attachment
            $metainfo[$mt[0]] = $object->expFile['downloadable'][0]->url;

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

    function getRSSContent($limit = 0) {
        include_once(BASE.'external/mp3file.php');

        $fd = new filedownload();
        $items = $fd->find('all',$this->aggregateWhereClause(), isset($this->config['order']) ? $this->config['order'] : 'created_at DESC', $limit);
        
        //Convert the items to rss items
        $rssitems = array();
        foreach ($items as $key => $item) { 
            $rss_item = new FeedItem();

            // Add the basic data
            $rss_item->title = expString::convertSmartQuotes($item->title);
            $rss_item->link = $rss_item->guid = makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'title'=>$item->sef_url));
            $rss_item->description = expString::convertSmartQuotes($item->body);
            $rss_item->author = user::getUserById($item->poster)->firstname.' '.user::getUserById($item->poster)->lastname;
            $rss_item->authorEmail = user::getEmailById($item->poster);
//            $rss_item->date = isset($item->publish_date) ? date(DATE_RSS,$item->publish_date) : date(DATE_RSS, $item->created_at);
            $rss_item->date = isset($item->publish_date) ? $item->publish_date : $item->created_at;
            if (!empty($item->expCat[0]->title))
                $rss_item->category = array($item->expCat[0]->title);

            // Add the attachment/enclosure info
            $rss_item->enclosure = new Enclosure();
            $rss_item->enclosure->url = !empty($item->expFile['downloadable'][0]->url) ? $item->expFile['downloadable'][0]->url : '';
            $rss_item->enclosure->length = !empty($item->expFile['downloadable'][0]->filesize) ? $item->expFile['downloadable'][0]->filesize : '';
            $rss_item->enclosure->type = !empty($item->expFile['downloadable'][0]->mimetype) ? $item->expFile['downloadable'][0]->mimetype : '';
            if ($rss_item->enclosure->type == 'audio/mpeg')
                $rss_item->enclosure->type = 'audio/mpg';

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
            if (($rss_item->enclosure->type == "audio/mpg") && (file_exists(BASE.$item->expFile['downloadable'][0]->directory.$item->expFile['downloadable'][0]->filename))) {
                $mp3 = new mp3file(BASE.$item->expFile['downloadable'][0]->directory.$item->expFile['downloadable'][0]->filename);
                $id3 = $mp3->get_metadata();
                if (($id3['Encoding']=='VBR') || ($id3['Encoding']=='CBR')) {
                    $rss_item->itunes->duration = $id3['Length mm:ss'];
                }
                if (!empty($id3['artist'])) {
                    $rss_item->author = $id3['artist'];
                    $rss_item->itunes->author = $id3['artist'];
                }
                if (!empty($id3['comment'])) {
                    $rss_item->itunes->subtitle = $id3['comment'];
                }
            } else {
                $rss_item->itunes->duration = 'Unknown';
            }

            // Add the item to the array.
            $rssitems[$key] = $rss_item;

            if ($limit && count($rssitems) >= $limit)
                break;
        }
        return $rssitems;
    }
	
}

?>