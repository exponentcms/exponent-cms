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

class headlineController extends expController {
    //protected $basemodel_name = '';
    public $useractions = array(
        'show'=>'Show Headline',
    );
	public $codequality = 'beta';
 
	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation', 'categories','comments','ealerts','files','module_title','pagination', 'rss','tags')
	   
    function displayname() { return "Headline"; }
    function description() { return "Allows Admin's to create headlines for sections, and pulls the Title in for modules actions."; }
    function author() { return "Phillip Ball - OIC Group, Inc"; }
    function isSearchable() { return true; }
    
    public function show() {
        $where = "location_data='".serialize($this->loc)."'";
        $db_headline = $this->headline->find('first', $where);

        $this->metainfo = expTheme::pageMetaInfo();
        $title = !empty($db_headline) ? $db_headline->title : $this->metainfo['title'];

        assign_to_template(array(
            'headline'=>$title,
            'record'=>$db_headline,
        ));
    }
    
}

?>
