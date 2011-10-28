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

function smarty_function_optiondisplayer($params,&$smarty) {
    global $db;
    
    $groupname = $params['options'];
    $product = $params['product'];
    $display_price_as = isset($params['display_price_as']) ? $params['display_price_as'] : 'diff';
    
    // get the option group
    $og = new optiongroup();
    //$group = $og->find('bytitle', $groupname);
    $group = $og->find('first', 'product_id='.$product->id.' AND title="'.$groupname.'"');

    //grab the options configured for this product
    $options = $product->optionDropdown($group->title, $display_price_as);

    // if there are no  options we can return now
    if (empty($options)) return false;
    
    // find the default option if there is one.
    $default = $db->selectValue('option', 'id', 'optiongroup_id='.$group->id.' AND is_default=1');
    
    $view = $params['view'];
    
    //if((isset() || $og->required == false) $includeblank = $params['includeblank'] ; 
    //elseif((isset($params['includeblank']) && $params['includeblank'] == false) || $og->required == true) $includeblank = false;
    
    $includeblank = $og->required == false && !isset($params['includeblank']) ? '-- Please Select an Option --' : $params['includeblank'];
    
    $template = get_common_template($view, $smarty->getTemplateVars('__loc'), 'options');
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
