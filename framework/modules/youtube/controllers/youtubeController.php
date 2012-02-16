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

class youtubeController extends expController {
	//protected $basemodel_name = '';
	public $useractions = array(
        'showall'=>'Display a YouTube Video'
    );
    public $remove_configs = array(
        'categories',
        'comments',
        'ealerts',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

	function displayname() { return "YouTube"; }
	function description() { return "Display youtube videos on your page."; }
	function author() { return "Phillip Ball - OIC Group, Inc"; }
	
	function showall() {
        $yt = new $this->basemodel_name();
        $vids = $yt->find('all',$this->aggregateWhereClause());

        if (!empty($this->config['width'])&&!empty($this->config['height'])) {
            foreach ($vids as $key=>$val) {
                $val->embed_code = preg_replace("/height=\"\d+\"/", 'height='.$this->config['height'], $val->embed_code);
                $val->embed_code = preg_replace("/width=\"\d+\"/", 'width='.$this->config['width'], $val->embed_code);
            }
        }
		// force fix for menus appearing BEHIND the video in IE
        foreach ($vids as $key=>$val) {
            $val->embed_code = preg_replace("/\" frameborder=\"/", '?wmode=opaque" frameborder="', $val->embed_code);
        }

        assign_to_template(array('items'=>$vids));
    }
	
}

?>
