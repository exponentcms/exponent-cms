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

class rssController extends expController {
    public  $basemodel_name = 'expRss';
    public $useractions = array('showall'=>'Show all RSS Feeds');

	public $remove_configs = array(
        'aggregretion',
        'comments',
        'files',
        //'rss',
        'tags'
    );

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "RSS Syndication"; }
    function description() { return "This module will allow you to display a list of your syndicated RSS feeds on a web page"; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }    
    
    function showall() {
        $rss = new expRss();
        assign_to_template(array('feeds'=>$rss->getFeeds()));
    }
    
    function show() {
        redirect_to(array('controller'=>'rss', 'action'=>'showall'));
    }
}

?>
