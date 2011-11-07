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

/**
 * Smarty {uniqueid} function plugin
 *
 * Type:     function<br>
 * Name:     uniquieid<br>
 * Purpose:  create a unique id
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_uniqueid($params,&$smarty) {
    $badvals = array("[", "]", ",", " ", "'", "\"", "&", "#", "%", "@", "!", "$", "(", ")", "{", "}");
    $randstr  = 'exp';
    $randstr .= empty($smarty->getTemplateVars('__loc')->src) ? mt_rand(1, 9999) : str_replace($badvals, "",$smarty->getTemplateVars('__loc')->src);
    $id =  $randstr.$params['id'];
    
    if (!empty($params['prepend'])) $id = $params['prepend'].$id;
    
    if (isset($params['assign'])){ 
        $smarty->assign($params['assign'],$id);
    } else {
        echo $id;
    }
}

?>
