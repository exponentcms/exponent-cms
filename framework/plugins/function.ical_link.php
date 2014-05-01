<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * Smarty {ical_link} function plugin
 *
 * Type:     function<br>
 * Name:     ical_link<br>
 * Purpose:  format a link for an iCalendar feed of the module
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_ical_link($params,&$smarty) {
    $config = $smarty->getTemplateVars('config');
    $ical_on = false;
    if (isset($params['show'])) {  // force display of link
        $ical_on = !empty($params['show']);
    } elseif (is_object($config)) {  // old school module
        $ical_on = !empty($config->enable_ical);
        $title = $config->feed_title;
        $sef = $config->sef_url;
    } elseif (is_array($config)) {  // controller
        $ical_on = !empty($config['enable_ical']);
        $title = $config['feed_title'];
        $sef = $config['feed_sef_url'];
    }
    if (isset($params['feed'])) {  // passing a feed
        $ical_on = true;
        $title = $params['feed']->title;
        $sef = $params['feed']->sef_url;
    }

    if ($ical_on) {
        // initialize a couple of variables
        $text = isset($params['text']) ? $params['text'] : '';
        $title = isset($params['title']) ? $params['title'] : gt('iCalendar Feed') . ' ' . $title;
        $sef = isset($params['url']) ? $params['url'] : $sef;
        $prepend = isset($params['prepend']) ? $params['prepend'] : '';
        $class = isset($params['class']) ? $params['class'] : 'icallink module-actions';
        $loc = $smarty->getTemplateVars('__loc');
       	if (!isset($params['module'])) $params['module'] = $loc->mod;
        if (!isset($params['src'])) $params['src'] = $loc->src;
       	if (!isset($params['int'])) $params['int'] = $loc->int;
        if (!empty($sef)) {
            $link = expCore::makeLink(array('module'=>$params['module'], 'action'=>'ical', 'title'=>$sef));
        } else {
            $link = expCore::makeLink(array('module'=>$params['module'], 'action'=>'ical', 'src'=>$params['src']));
        }
        // spit out the link
        echo $prepend.'<a class="'.$class.'" href="'.$link.'" title="'.$title.'">'.$text.'</a>';
    }
}

?>

