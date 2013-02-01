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

$maps = array();

// Find news by the title of the news post.  URL would look like news/my-post-title
// $maps[] = array('controller'=>'blog',
//      'action'=>'show',
//      'url_parts'=>array(
//              'controller'=>'blog',
//              'title'=>'(.*)'),
// );
// 
// // Find news by the title of the news post.  URL would look like news/my-post-title
// $maps[] = array('controller'=>'news',
//      'action'=>'showByTitle',
//      'url_parts'=>array(
//              'controller'=>'news',
//              'title'=>'(.*)'),
// );
// 
// // Find news by the title of the news post.  URL would look like news/my-post-title
// $maps[] = array('controller'=>'store',
//      'action'=>'showall',
//      'url_parts'=>array(
//              'controller'=>'browse-store',
//              'title'=>'(.*)'),
// );
// 
// // Find news by the title of the news post.  URL would look like news/my-post-title
// $maps[] = array('controller'=>'store',
//      'action'=>'showByTitle',
//      'url_parts'=>array(
//              'controller'=>'store',
//              'title'=>'(.*)'),
// );

// Find help docs by version & title
// $maps[] = array('controller'=>'help',
//      'action'=>'show',
//      'url_parts'=>array(
//              'controller'=>'help',
//              'title'=>'(.*)',
//              'version'=>'(.*)'),
// );
// 
// // Find help docs by version & title
// $maps[] = array('controller'=>'help',
//      'action'=>'show',
//      'url_parts'=>array(
//              'controller'=>'help',
//              'title'=>'(.*)'),
// );

/*
// Find news by the date of the news post.  URL would look like news/2007/10/18 to find all the posts on Oct 18, 2007
$maps[] = array('controller'=>'news',
		'action'=>'findByDate',
		'url_parts'=>array(
				'controller'=>'news',
				'year'=>'(19|20)\d\d',
				'month'=>'[01]?\d',
				'day'=>'[0-3]?\d'),
);

$maps[] = array('controller'=>'news',
                'action'=>'findByDate',
                'url_parts'=>array(
                                'controller'=>'news',
                                'year'=>'(19|20)\d\d'),
);

$maps[] = array('controller'=>'news',
                'action'=>'findByDate',
                'url_parts'=>array(
                                'controller'=>'news',
                                'year'=>'(19|20)\d\d',
				'month'=>'[01]?\d',),
);
*/
?>