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

class youtubeController extends expController {
	public $useractions = array(
        'showall'=>'Display a YouTube Video'
    );
    public $remove_configs = array(
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'rss',
        'tags'
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags')
    public $codequality = 'DEPRECATED';

    static function displayname() { return gt("YouTube"); }
    static function description() { return gt("Display YouTube videos on your page."); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }
	
	function showall() {
        $page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>$this->aggregateWhereClause(),
            'limit'=>(isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
            'order'=>'rank',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
        ));

        if (!empty($this->config['width'])&&!empty($this->config['height'])) {
            // adjust the height/width to our settings
            foreach ($page->records as $key=>$val) {
                $val->embed_code = preg_replace("/height=\"\d+\"/", 'height='.$this->config['height'], $val->embed_code);
                $val->embed_code = preg_replace("/width=\"\d+\"/", 'width='.$this->config['width'], $val->embed_code);
            }
        }
		// force fix for menus appearing BEHIND the video in IE
        foreach ($page->records as $key=>$val) {
            $val->embed_code = preg_replace("/\" frameborder=\"/", '?wmode=opaque" frameborder="', $val->embed_code);
        }

        assign_to_template(array(
            'page'=>$page
        ));
    }
	
}

?>