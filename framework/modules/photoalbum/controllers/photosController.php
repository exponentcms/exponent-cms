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

class photosController extends expController {
    public $basemodel_name = 'photo';
//    public $useractions = array(
//        'showall'=>'Gallery',
//        'slideshow'=>'Slideshow',
//        //'showall_tags'=>"Tag Categories"
//    );
    public $remove_configs = array(
        'comments',
        'ealerts',
        'files',
        'rss'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

    function displayname() { return gt("Photo Album"); }
    function description() { return gt("This module allows you to display and manage images."); }
    function isSearchable() { return true; }
    
    public function showall() {
        expHistory::set('viewable', $this->params);

        $page = new expPaginator(array(
                    'model'=>'photo',
                    'where'=>$this->aggregateWhereClause(),
                    'limit'=>(isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
                    'order'=>'rank',
                    'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
                    'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
                    'groups'=>empty($this->params['gallery']) ? array() : array($this->params['gallery']),
                    'src'=>$this->loc->src,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array(gt('Title')=>'title'),
                    ));
                    
        assign_to_template(array(
            'page'=>$page
        ));
    }
    
    function show() {
        global $db;
        expHistory::set('viewable', $this->params);
        
        // figure out if we're looking this up by id or title
        $id = null;
        if (isset($this->params['id'])) {
            $id = $this->params['id'];
        } elseif (isset($this->params['title'])) {
            $id = $this->params['title'];
        }
        $record = new photo($id);
        $where = $this->aggregateWhereClause();
        $maxrank = $db->max($this->model_table,'rank','',$where);
                
        $next = $db->selectValue($this->model_table,'sef_url',$where." AND rank=".($record->rank+1));
        $prev = $db->selectValue($this->model_table,'sef_url',$where." AND rank=".($record->rank-1));

        if ($record->rank==$maxrank) {
            $where = $where." AND rank=1";
            $next = $db->selectValue($this->model_table,'sef_url',$where);
        }
        
        if ($record->rank==1) {
            $where = $where." AND rank=".$maxrank;
            $prev = $db->selectValue($this->model_table,'sef_url',$where);
        }
        $config = expUnserialize($db->selectValue('expConfigs','config',"location_data='".$record->location_data."'"));

        assign_to_template(array(
            'record'=>$record,
            'imgnum'=>$record->rank,
            'imgtot'=>count($record->find('all',$this->aggregateWhereClause())),
            "next"=>$next,
            "previous"=>$prev,
            'config'=>$config
        ));
    }
    
    public function slideshow() {
        expHistory::set('viewable', $this->params);
//        $where = $this->aggregateWhereClause();
//        $order = 'rank';
//        //FIXME we need to change this to expPaginator to get category grouping
//        $s = new photo();
//        $slides = $s->find('all',$where,$order);

        $page = new expPaginator(array(
                    'model'=>'photo',
                    'where'=>$this->aggregateWhereClause(),
                    'limit'=>(isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
                    'order'=>'rank',
                    'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
                    'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
                    'groups'=>empty($this->params['gallery']) ? array() : array($this->params['gallery']),
                    'src'=>$this->loc->src,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array(gt('Title')=>'title'),
                    ));

        assign_to_template(array(
//            'slides'=>$slides
            'slides'=>$page->records
        ));
    }
    
    public function showall_tags() {
        $images = $this->image->find('all');
        $used_tags = array();
        foreach ($images as $image) {
            foreach($image->expTag as $tag) {
                if (isset($used_tags[$tag->id])) {
                    $used_tags[$tag->id]->count += 1;
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
    
    public function update() {

        //populate the alt tag field if the user didn't
        if (empty($this->params['alt'])) $this->params['alt'] = $this->params['title'];
        
        // call expController update to save the image
        parent::update();
    }
}

?>