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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {tags_assigned} function plugin
 *
 * Type:     function<br>
 * Name:     tags_assigned<br>
 * Purpose:  format links for displaying tags assigned to an item
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_tags_assigned($params,&$smarty) {
    if (empty($params['item'])) return;
    $item = $params['item'];
    if (!empty($item->disable_comments)) return;
    $config = $smarty->getTemplateVars('config');
    if (!empty($config['disabletags']) || !count($item->expTag)) return;

    // initialize a couple of variables
    $prepend = isset($params['prepend']) ? $params['prepend'] : '';
//    $class = isset($params['class']) ? $params['class'] : 'comments';

    // spit out the link
    $link = '<span class="label tags">'.gt('Tags').':</span>
             <span class="value">';
    $i = 1;
    foreach ($item->expTag as $tags=>$tag) {
        $iloc = expUnserialize($item->location_data);
        $link .= '<a href="'.expCore::makeLink(array('controller'=>$item->classname,'action'=>'showall_by_tags','tag'=>$tag->sef_url,'src'=>$iloc->src)).'">'.$tag->title.'</a>';
        if ($i != count($item->expTag)) $link .= ", ";
        $i++;
    }
    $link .= '</span>';

    echo $prepend.$link;
}

?>

