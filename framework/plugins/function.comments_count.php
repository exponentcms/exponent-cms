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
 * Smarty {comments_count} function plugin
 *
 * Type:     function<br>
 * Name:     comments_count<br>
 * Purpose:  format a link for displaying number of item comments
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_comments_count($params,&$smarty) {
    if (empty($params['item'])) return;
    $item = $params['item'];
    if (!empty($item->disable_comments)) return;
    $config = $smarty->getTemplateVars('config');
    if (!empty($config['usescomments']) && !count($item->expComment)) return;

    // initialize a couple of variables
    if (!empty($params['show'])) {
        $linkparams = array();
        $linkparams['controller'] = $item->classname;
        $linkparams['action'] = 'show';
        $linkparams['title'] = $item->sef_url;
        $link = expCore::makeLink($linkparams).'#exp-comments';
    } else {
        $link = '#exp-comments';
    }

    $prepend = isset($params['prepend']) ? $params['prepend'] : '';
    $class = isset($params['class']) ? $params['class'] : 'comments';

    // spit out the link
    echo $prepend.'<a class="'.$class.'" href="'.$link.'">'.count($item->expComment).' '.(count($item->expComment)==1?gt("Comment"):gt("Comments")).'</a>';
}

?>

