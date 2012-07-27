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
 * Smarty {subscribe_link} function plugin
 *
 * Type:     function<br>
 * Name:     subscribe_link<br>
 * Purpose:  format a link for subscribing/unsubscribing to email alerts for the module
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_subscribe_link($params,&$smarty) {
	global $db, $router, $user;

    $config = $smarty->getTemplateVars('config');
    if (is_object($config)) {
        $sub = !empty($config->enable_ealerts);
    } elseif (is_array($config)) {
        $sub = !empty($config['enable_ealerts']);
    } elseif (isset($params['show'])) {  // force display of link
        $sub = isset($params['show']) ? $params['show'] : null;
    }
    if ($sub && !empty($user)) {
        $cloc = $smarty->getTemplateVars('__loc');
        $ealert = $db->selectObject('expeAlerts',"module='".$cloc->mod."' AND src='".$cloc->src."'");
        if (!empty($ealert)) {
            // initialize a couple of variables
            $text = isset($params['text']) ? $params['text'] : gt('Subscribe to Content Updates');
            $prepend = isset($params['prepend']) ? $params['prepend'] : '';
            $class = isset($params['class']) ? $params['class'] : 'subscribe-link';
            $action = 'subscribe';
            $subscribed = $db->selectObject('user_subscriptions','user_id='.$user->id.' AND expeAlerts_id='.$ealert->id);
            if (!empty($subscribed)) {
                $text = gt('Un-').$text;
                $class = 'un'.$class;
                $action = 'un'.$action;
            }
            // spit out the link
            $link =  '<a class="'.$class.'" href="'.$router->makelink(array('controller'=>'ealert', 'action'=>$action, 'id'=>$ealert->id)).'">'.$text.'</a>';
            echo $prepend.$link;
        }
    }
}

?>

