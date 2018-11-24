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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {optiondisplayer} function plugin
 *
 * Type:     function<br>
 * Name:     optiondisplayer<br>
 * Purpose:  display option dropdown list
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @return bool
 * @throws ReflectionException
 */
function smarty_function_optiondisplayer($params,&$smarty) {
    global $db;

    $groupname = $params['options'];
    $product = $params['product'];
    $display_price_as = isset($params['display_price_as']) ? $params['display_price_as'] : 'diff';

    // get the option group
    $og = new optiongroup();
    //$group = $og->find('bytitle', $groupname);
    $group = $og->find('first', 'product_id='.$product->id.' AND title=\''.$groupname.'\'');

    //grab the options configured for this product
    $options = $product->optionDropdown($group->title, $display_price_as);

    // if there are no  options we can return now
    if (empty($options)) return false;

    // find the default option if there is one.
    $default = $db->selectValue('option', 'id', 'optiongroup_id='.$group->id.' AND is_default=1');
    if (!array_key_exists($default,$options)) $default = null;

    $view = $params['view'];
    if ($view !== 'checkboxes' && $view !== 'dropdown') {
        if (!empty($params['view'])) {
            $view = 'checkboxes';
        } else {
            $view = 'dropdown';
        }
    }

    //if((isset() || $group->required == false) $includeblank = $params['includeblank'] ;
    //elseif((isset($params['includeblank']) && $params['includeblank'] == false) || $group->required == true) $includeblank = false;

    // only include a blank if not 'required', otherwise we need to display it, or if forced by param
    $includeblank = $group->required == false && !isset($params['includeblank']) ? gt('-- Please Select an Option --') : $params['includeblank'];

    $template = expTemplate::get_common_template($view, $smarty->getTemplateVars('__loc'), 'options');
    $template->assign('product', $product);
    $template->assign('options', $options);
    $template->assign('group', $group);
    $template->assign('params', $params);
    $template->assign('default', $default);
    $template->assign('includeblank', $includeblank);
    $template->assign('required', $params['required']);
    $template->assign('selected', $params['selected']);

    echo $template->render();
}

?>
