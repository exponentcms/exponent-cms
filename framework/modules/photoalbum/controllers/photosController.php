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

class photosController extends expController {
    public $basemodel_name = 'photo';
//    public $useractions = array(
//        'showall'=>'Gallery',
//        'slideshow'=>'Slideshow',
//        //'showall_tags'=>"Tag Categories"
//    );
    protected $manage_permissions = array(
        'multi'=>'Bulk Actions',
    );
    public $remove_configs = array(
        'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',  // we need to customize it in this module?
        'rss',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Photo Album"); }
    static function description() { return gt("Displays and manages images."); }
    static function isSearchable() { return true; }

    public function showall() {
        expHistory::set('viewable', $this->params);
        $limit = (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10;
        if (!empty($this->params['view']) && ($this->params['view'] == 'showall_accordion' || $this->params['view'] == 'showall_tabbed')) {
            $limit = '0';
        }
        $order = isset($this->config['order']) ? $this->config['order'] : "rank";
        $page = new expPaginator(array(
            'model'=>'photo',
            'where'=>$this->aggregateWhereClause(),
            'limit'=>$limit,
            'order'=>$order,
            'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
            'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
            'groups'=>!isset($this->params['gallery']) ? array() : array($this->params['gallery']),
            'grouplimit'=>!empty($this->params['view']) && $this->params['view'] == 'showall_galleries' ? 1 : null,
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
    }

    function show() {
        expHistory::set('viewable', $this->params);

        // figure out if we're looking this up by id or title
        $id = null;
        if (isset($this->params['id'])) {
            $id = $this->params['id'];
        } elseif (isset($this->params['title'])) {
            $id = expString::escape($this->params['title']);
        }
        $record = new photo($id);
        if (empty($record->id))
            redirect_to(array('controller'=>'notfound','action'=>'page_not_found','title'=>$this->params['title']));

        $config = expConfig::getConfig($record->location_data);
        if (empty($this->config))
            $this->config = $config;
        if (empty($this->loc->src)) {
            $r_loc = expUnserialize($record->location_data);
            $this->loc->src = $r_loc->src;
        }

        $where = $this->aggregateWhereClause();
//        $maxrank = $db->max($this->model_table,'rank','',$where);
//
//        $record->next = $db->selectValue($this->model_table,'sef_url',$where." AND rank=".($record->rank+1));
//        $record->prev = $db->selectValue($this->model_table,'sef_url',$where." AND rank=".($record->rank-1));
//
//        if ($record->rank==$maxrank) {
//            $where = $where." AND rank=1";
//            $record->next = $db->selectValue($this->model_table,'sef_url',$where);
//        }
//
//        if ($record->rank==1) {
//            $where = $where." AND rank=".$maxrank;
//            $record->prev = $db->selectValue($this->model_table,'sef_url',$where);
//        }
        $record->addNextPrev($where);

        assign_to_template(array(
            'record'=>$record,
            'imgnum'=>$record->rank,
            'imgtot'=>count($record->find('all',$this->aggregateWhereClause())),
//            "next"=>$next,
//            "previous"=>$prev,
            'config'=>$config
        ));
    }

    public function slideshow() {
        expHistory::set('viewable', $this->params);
        $order = isset($this->config['order']) ? $this->config['order'] : "rank";
        $page = new expPaginator(array(
            'model'=>'photo',
            'where'=>$this->aggregateWhereClause(),
            'limit'=>(isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
            'order'=>$order,
            'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
            'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
            'groups'=>empty($this->params['gallery']) ? array() : array($this->params['gallery']),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Title')=>'title'
            ),
        ));

        assign_to_template(array(
//            'slides'=>$slides
            'slides'=>$page->records,
        ));
    }

    public function showall_tags() {
        $images = $this->image->find('all');
        $used_tags = array();
        foreach ($images as $image) {
            foreach($image->expTag as $tag) {
                if (isset($used_tags[$tag->id])) {
                    $used_tags[$tag->id]->count++;
                } else {
                    $exptag = new expTag($tag->id);
                    $used_tags[$tag->id] = $exptag;
                    $used_tags[$tag->id]->count = 1;
                }

            }
        }

        assign_to_template(array(
            'tags'=>$used_tags
        ));
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
            return '<!--
        <PageMap>
            <DataObject type="thumbnail">
                <Attribute name="src" value="'.URL_FULL.$object->expFile[0]->directory.$object->expFile[0]->filename.'"/>
                <Attribute name="width" value="'.$object->expFile[0]->image_width.'"/>
                <Attribute name="height" value="'.$object->expFile[0]->image_width.'"/>
            </DataObject>
        </PageMap>
    -->';
        } else return null;
    }

    public function update() {

        //populate the alt tag field if the user didn't
        if (empty($this->params['alt'])) $this->params['alt'] = $this->params['title'];

        // call expController update to save the image
        parent::update();
    }

    public function multi_add() {
//        global $db;

//        $tags = $db->selectObjects('expTags', '1', 'title ASC');
//        $taglist = '';
//        foreach ($tags as $tag) {
//            $taglist .= "'" . $tag->title . "',";
//        }
//        $taglist = expTag::getAllTags();
//        $modelname = $this->basemodel_name;
//        assign_to_template(array(
//            'record'     => $record,
//            'table'      => $this->$modelname->tablename,
//            'controller' => $this->params['controller'],
//            'taglist'    => $taglist
//        ));
    }

    public function multi_update() {
//        global $db;

        if (!empty($this->params['expFile'])) {
            if (!empty($this->params['title'])) {
                $prefix = $this->params['title'] . ' - ';
            } else {
                $prefix = '';
            }
            $params = array();
            //check for and handle tags
            if (array_key_exists('expTag', $this->params)) {
                $tags = explode(",", trim($this->params['expTag']));

                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $tag = strtolower(trim($tag));
                        $tag = str_replace(array('"', "'"), "", $tag); // strip double and single quotes
                        if (!empty($tag)) {
                            $expTag = new expTag($tag);
                            if (empty($expTag->id))
                                $expTag->update(array('title' => $tag));
                            $params['expTag'][] = $expTag->id;
                        }
                    }
                }
            }

            //check for and handle cats
            if (array_key_exists('expCat', $this->params) && !empty($this->params['expCat'])) {
                $catid = $this->params['expCat'];
                $params['expCat'][] = $catid;
            }
            foreach ($this->params['expFile'] as $fileid) {
                $params['expFile'][0] = new expFile($fileid);
                if (!empty($params['expFile'][0]->id)) {
                    $photo = new photo();
                    $photo->expFile = $params['expFile'];
                    $loc = expCore::makeLocation("photo",$this->params['src']);
                    $photo->location_data = serialize($loc);
    //                $photo->body = $gi['description'];
    //                $photo->alt = !empty($gi['alt']) ? $gi['alt'] : $photo->title;
                    $filename = pathinfo($params['expFile'][0]->filename);
                    $photo->title = $prefix . $filename['filename'];
                    if (!empty($params['expTag'])) {
                        $photo->expTag = $params['expTag'];
                    }
                    if (!empty($params['expCat'])) {
                        $photo->expCat = $params['expCat'];
                    }
                    $photo->update($params);  // save gallery name as category
                }
            }
            $this->addContentToSearch();
        }
        expHistory::back();
    }

    function delete_multi() {
        expHistory::set('manageable', $this->params);
        $order = isset($this->config['order']) ? $this->config['order'] : "rank";
        $page = new expPaginator(array(
            'model'=>'photo',
            'where'=>$this->aggregateWhereClause(),
            'order'=>$order,
            'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
            'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
            'groups'=>!isset($this->params['gallery']) ? array() : array($this->params['gallery']),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Title')=>'title'
            ),
        ));

        assign_to_template(array(
            'page'=>$page,
        ));
    }

    function delete_multi_act() {
        foreach ($this->params['pic'] as $pic_id=>$value) {
            $obj = new photo($pic_id);
            $obj->delete();
        }
        expHistory::back();
    }

}

?>