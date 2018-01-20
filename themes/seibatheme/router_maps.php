<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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

// Find news by the date of the news post.  URL would look like news/20137/10/18 to find all the posts on Oct 18, 2013
//$maps[] = array('controller'=>'news',
//                'action'=>'showall_by_date',
//                'url_parts'=>array(
//                    'controller'=>'news',
//                    'year'=>'(19|20)\d\d',
//                    'month'=>'[01]?\d',
//                    'day'=>'[0-3]?\d'
//                ),
//);
//
//$maps[] = array('controller'=>'news',
//                'action'=>'showall_by_date',
//                'url_parts'=>array(
//                    'controller'=>'news',
//                    'year'=>'(19|20)\d\d',
//				    'month'=>'[01]?\d',
//                ),
//);
//
//$maps[] = array('controller'=>'news',
//                'action'=>'showall_by_date',
//                'url_parts'=>array(
//                    'controller'=>'news',
//                    'year'=>'(19|20)\d\d'
//                ),
//);

// Find news by the title.  URL would look like news/my-news-title
$maps[] = array('controller' => 'news',
                'action'     => 'show',
                'url_parts'  => array(
                    'controller' => 'news',
                    'title'      => '(.*)'
                ),
);

// Find blog post by the title.  URL would look like blog/my-post-title
$maps[] = array('controller' => 'blog',
                'action'     => 'show',
                'url_parts'  => array(
                    'controller' => 'blog',
                    'title'      => '(.*)'
                ),
);

// Find blog posts by the tag.  URL would look like view-blogs-by-tag/my-tag
$maps[] = array('controller' => 'blog',
                'action'     => 'showall_by_tags',
                'url_parts'  => array(
                    'controller' => 'view-blogs-by-tag',
                    'title'      => '(.*)'
                ),
);

// Find blog posts by date.  URL would look like blog/2013/05
$maps[] = array('controller'=>'blog',
                'action'=>'showall_by_date',
                'url_parts'=>array(
                    'controller'=>'blog',
                    'year'=>'(19|20)\d\d',
				    'month'=>'[01]?\d',
                ),
);

// Find blog posts by author.  URL would look like view-blogs-by-author/author-username
$maps[] = array('controller' => 'blog',
                'action'     => 'showall_by_author',
                'url_parts'  => array(
                    'controller' => 'view-blogs-by-author',
                    'author'     => '(.*)'
                ),
);

// Find filedownloads by the title.  URL would look like file/my-file-title
$maps[] = array('controller' => 'filedownload',
                'action'     => 'show',
                'url_parts'  => array(
                    'controller' => 'file',
                    'title'      => '(.*)'
                ),
);

$maps[] = array('controller' => 'portfolio',
                'action'     => 'show',
                'url_parts'  => array(
                    'controller' => 'directory',
                    'title'      => '(.*)'
                ),
);

//// Find sermonseries by the title selecting the appropriate sermon.  URL would look like sermonseries/my-sermonseries-title/sermon-number
//$maps[] = array('controller' => 'sermonseries',
//                'action'     => 'show',
//                'url_parts'  => array(
//                    'controller' => 'sermonseries',
//                    'title'      => '(.*)',
//                    'page'       => '(.*)'
//                ),
//);
//
//// Find sermonseries by the title selecting the most recent sermon.  URL would look like sermonseries/my-sermonseries-title
//$maps[] = array('controller' => 'sermonseries',
//                'action'     => 'show',
//                'url_parts'  => array(
//                    'controller' => 'sermonseries',
//                    'title'      => '(.*)'
//                ),
//);
//
//// Find sermons by the title.  URL would look like sermon/my-sermon-title
//$maps[] = array('controller' => 'sermons',
//                'action'     => 'show',
//                'url_parts'  => array(
//                    'controller' => 'sermon',
//                    'title'      => '(.*)'
//                ),
//);

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

?>
