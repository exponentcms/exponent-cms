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

class flowplayerController extends expController {
    public $useractions = array(
        'showall'=>'Show all'
    );
	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
        'rss',
        'tags'
    );  // all options: ('aggregation','categories','comments','ealerts','files','pagination','rss','tags')

    static function displayname() { return gt("Flowplayer Media Player"); }
    static function description() { return gt("Flowplayer is a media player for Web sites. Use it to embed video/audio streams into your HTML pages."); }
    static function isSearchable() { return true; }
    
    function showall() {
        expHistory::set('viewable', $this->params);
        $page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>$this->aggregateWhereClause(),
            'limit'=>(isset($this->params['limit']) && $this->config['limit'] != '') ? $this->params['limit'] : 10,
            'order'=>"rank",
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('ID#')=>'id',
                gt('Title')=>'title',
                gt('Body')=>'body'
            ),
        ));
        
        assign_to_template(array(
            'page'=>$page,
            'items'=>$page->records
        ));
    }

}

?>