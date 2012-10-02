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

class flickrfeedController extends expController {
    public $useractions = array(
        'showall'=>'Display Public Photostream',
    );

    static function displayname() { return gt("Flickr Feed"); }
    static function description() { return gt("Display your Flickr Photostream"); }
    static function author() { return "Jonathan Worent - OIC Group, Inc"; }
    static function isSearchable() { return true; }
    
    public function showall() { 
        expHistory::set('viewable', $this->params);

        // if no RSS feed is set yet get public photostream of "everyone"
        if (empty($this->config['pull_rss'])) {
        	$this->config['pull_rss'] = array(0=>"http://api.flickr.com/services/feeds/photos_public.gne?format=php&id=");
        }
		
        $photos = array();
//        $RSS = new rssfeed();  // we'll use curl instead of magpierss
        foreach($this->config['pull_rss'] as $url) {
        	// we need to get at one of the query paramerers and change it
			// parse the url into its parts
        	$url = parse_url($url);
			// parse the params into an associative array
            parse_str($url['query'], $url['query']);	
			// set the 'format'  param to 'php_serial'		
			$url['query']['format'] = 'php_serial';
			// stitch the params back together
			$url['query'] = http_build_query($url['query'], '', '&');
			
			// stitch the whole thing back together
			// scheme
			$uri = (!empty($url['scheme'])) ? $url['scheme'].'://' : '';
			// user & pass
			if (!empty($url['user'])){
				$uri .= $url['user'].':'.$url['pass'].'@';
			}
			// host
			$uri .= $url['host'];
			// port
			$port = (!empty($url['port'])) ? ':'.$url['port'] : '';
			$uri .= $port;
			// path
			$uri .= $url['path'];
			// fragment or query
			if (isset($url['fragment'])){
				$uri .= '#'.$url['fragment'];
			} elseif (isset($url['query'])){
				$uri .= '?'.$url['query'];
			}
			
			// create curl resource
			$ch = curl_init();			
			// set url
			curl_setopt($ch, CURLOPT_URL, $uri);
			//return the transfer as a string
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// $output contains the output string
			$output = curl_exec($ch);
			// close curl resource to free up system resources
			curl_close($ch);  
			$output = unserialize($output);
			
            foreach ($output['items'] as $rssItem) {
                $rssObject = new stdClass();
                $rssObject->title = !empty($rssItem['title']) ? $rssItem['title'] : "";
                $rssObject->link = !empty($rssItem['url']) ? $rssItem['url'] : "";
                $rssObject->description = !empty($rssItem['description']) ? $rssItem['description'] : "";
				$rssObject->raw_description= !empty($rssItem['description_raw']) ? $rssItem['description_raw'] : "";
                $rssObject->src_t = !empty($rssItem['t_url']) ? $rssItem['t_url'] : "";
                $rssObject->src_m = !empty($rssItem['m_url']) ? $rssItem['m_url'] : "";
                $rssObject->src_l = !empty($rssItem['l_url']) ? $rssItem['l_url'] : "";
                $rssObject->src_o = !empty($rssItem['photo_url']) ? $rssItem['photo_url'] : "";
                $rssObject->date_taken = !empty($rssItem['date_taken_nice']) ? $rssItem['date_taken_nice'] : "";
                $rssObject->author_name = !empty($rssItem['author_name']) ? $rssItem['author_name'] : "";
                $rssObject->author_url = !empty($rssItem['author_url']) ? $rssItem['author_url'] : "";
				// There is more data that Flickr returns that I am not adding to the object. 
				//Some is redundant some just wasn't useful at this time
                $photos[] = $rssObject;
            }
		}
		//eDebug($photos);
        assign_to_template(array(
            'items'=>$photos
        ));
    }
}

?>