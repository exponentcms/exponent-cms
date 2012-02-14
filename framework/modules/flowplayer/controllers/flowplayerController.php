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

class flowplayerController extends expController {
    //public $basemodel_name = '';
    public $useractions = array(
        'showall'=>'Show all'
    );
	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation', 'categories','comments','ealerts','files','module_title','pagination', 'rss','tags')

    function displayname() { return "Flowplayer Media Player"; }
    function description() { return "Flowplayer is a media player for Web sites. Use it to embed video/audio streams into your HTML pages."; }
    function isSearchable() { return true; }
    
    function showall() {
        expHistory::set('viewable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->aggregateWhereClause();
        $limit = isset($this->params['limit']) ? $this->params['limit'] : null;
        $order = "rank";
        $page = new expPaginator(array(
                    'model'=>$modelname,
                    'where'=>$where, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array('ID#'=>'id','Title'=>'title', 'Body'=>'body'),
                    ));
        
        assign_to_template(array('page'=>$page, 'items'=>$page->records));
    }

}

?>
