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

class photosController extends expController {
    public $basemodel_name = 'photo';
    public $useractions = array(
        'showall'=>'Gallery', 
        'slideshow'=>'Slideshow', 
        //'showall_tags'=>"Tag Categories"
    );
    public $remove_configs = array(
        'files',
        'ealerts',
        'comments',
        'rss',
        'tags'
    );

    function displayname() { return "Photo Album"; }
    function description() { return "This module allows you to display and manage images."; }
    function isSearchable() { return true; }
    
    public function showall() {
        expHistory::set('viewable', $this->params);
        $where = $this->aggregateWhereClause();
        $order = 'rank';
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        
        $page = new expPaginator(array(
                    'model'=>'photo',
                    'where'=>$where, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'src'=>$this->loc->src,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array('Title'=>'title'),
                    ));
                    
        assign_to_template(array('page'=>$page));
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
        
        assign_to_template(array('record'=>$record,'imgnum'=>$record->rank,'imgtot'=>count($record->find('all',$this->aggregateWhereClause())),"next"=>$next,"previous"=>$prev));
    }
    
    public function slideshow() {
        expHistory::set('viewable', $this->params);
        $where = $this->aggregateWhereClause();
        $order = 'rank';
        $s = new photo();
        $slides = $s->find('all',$where,$order);
                    
        assign_to_template(array('slides'=>$slides));
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
        
        assign_to_template(array('tags'=>$used_tags));
    }           
    
    public function update() {
        //FIXME:  Remove this code once we have the new tag implementation
        if (!empty($this->params['tags'])) {
            global $db;
            if (isset($this->params['id'])) {
                $db->delete('content_expTags', 'content_type="image" AND content_id='.$this->params['id']);
            }
            
            $tags = explode(",", $this->params['tags']);
            
            foreach($tags as $tag) {
                $tag = strtolower(trim($tag));
                $expTag = new expTag($tag);
                if (empty($expTag->id)) $expTag->update(array('title'=>$tag));
                $this->params['expTag'][] = $expTag->id;
            }
        }
        
        //populate the alt tag field if the user didn't
        if (empty($this->params['alt'])) $this->params['alt'] = $this->params['title'];
        
        // call expController update to save the image
        parent::update();
    }
}

?>
