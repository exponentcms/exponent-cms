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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {rss_link} function plugin
 *
 * Type:     function<br>
 * Name:     rss_link<br>
 * Purpose:  format a link for an rss feed of the module
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_rss_link($params,&$smarty) {
    $config = $smarty->getTemplateVars('config');
    $rss_on = false;
    if (isset($params['show'])) {  // force display of link
        $rss_on = !empty($params['show']);
    } elseif (is_array($config)) {
        $rss_on = !empty($config['enable_rss']);
        $title = $config['feed_title'];
        $sef = $config['feed_sef_url'];
    }
    if (isset($params['feed'])) {  // passing a feed
        $rss_on = true;
        $title = $params['feed']->title;
        $sef = $params['feed']->sef_url;
    }

    if ($rss_on) {
        // initialize a couple of variables
        $text = isset($params['text']) ? $params['text'] : '';
        $title = isset($params['title']) ? $params['title'] : gt('Subscribe to') . ' ' . $title;
        $sef = isset($params['url']) ? $params['url'] : $sef;
        $prepend = isset($params['prepend']) ? $params['prepend'] : '';
        $class = isset($params['class']) ? $params['class'] : 'rsslink module-actions';
        $loc = $smarty->getTemplateVars('__loc');
       	if (!isset($params['module'])) $params['module'] = $loc->mod;
        if (!isset($params['src'])) $params['src'] = $loc->src;
       	if (!isset($params['int'])) $params['int'] = $loc->int;

//       	$link =  expCore::makeLink(array('controller'=>$params['module'], 'action'=>'rss', 'title'=>$sef));
        if (!empty($sef)) {
            if (!empty($params['type'])) {
                $link_params = array('controller'=>'rss', 'action'=>'feed', 'title'=>$sef, 'type'=>$params['type']);
            } else {
                $link_params = array('controller'=>'rss', 'action'=>'feed', 'title'=>$sef);
            }
            $link = expCore::makeLink($link_params);
        } else {
            if (!empty($params['type'])) {
                $link_params = array('controller'=>$params['module'], 'action'=>'rss', 'src'=>$params['src'], 'type'=>$params['type']);
            } else {
                $link_params = array('controller'=>$params['module'], 'action'=>'rss', 'src'=>$params['src']);
            }
            $link = expCore::makeLink($link_params);
        }
        // spit out the link
        echo $prepend.'<a class="'.$class.'" href="'.$link.'" title="'.$title.'">'.$text.'</a>';
    }
}

?>

