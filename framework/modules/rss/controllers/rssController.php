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

class rssController extends expController {
    public  $basemodel_name = 'expRss';
    public $useractions = array(
        'showall'=>'Show all RSS Feeds'
    );

	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

    function displayname() { return gt("RSS Syndication"); }
    function description() { return gt("This module will allow you to display a list of your syndicated RSS feeds on a web page"); }
    
    function showall() {
        $rss = new expRss();
        assign_to_template(array(
            'feeds'=>$rss->getFeeds('enable_rss=1')
        ));
    }
    
    function show() {
//        redirect_to(array('controller'=>'rss', 'action'=>'showall'));
        $this->showall();
    }

    /**
     * Alternate universal method to call rss feed without using module name/type
     *  e.g., www.site.org/rss/feed/sef_url
     */
    function feed() {
        $this->rss();
    }

}

?>