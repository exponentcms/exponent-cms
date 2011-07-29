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

class youtubeController extends expController {
	//protected $basemodel_name = '';
	public $useractions = array('showall'=>'Display a YouTube Video');

    public $remove_configs = array('ealerts','tags','files','rss','comments');
	public $codequality = 'beta';

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
            $val->embed_code = preg_replace("/\" frameborder=\"/", '?wmode=transparent" frameborder="', $val->embed_code);
        }

        assign_to_template(array('items'=>$vids));
    }
	
}

?>
