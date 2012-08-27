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
 * Smarty {getnav} function plugin
 *
 * Type:     function<br>
 * Name:     getnav<br>
 * Purpose:  get and assign navigation sub-structure based on type
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_getnav($params,&$smarty) {
	global $sections;

    foreach ($sections as $key=>$value) {
        $rekeyed[$value->id] = $value;
    }
    $linkInQuestion = $rekeyed[$params['of']];
    switch ($params['type']) {
        case "parent" :
            $nav = $rekeyed[$linkInQuestion->parent];
            break;
        case "siblings" :
            foreach ($sections as $key=>$value) {
                if ($value->parent == $linkInQuestion->parent) {
                    $nav[] = $value;
                } elseif ((!empty($params['parents']) && $value->id == $linkInQuestion->parent) || (!empty($params['top']) && $value->depth == 0) || (!empty($params['top']) && in_array($value->id,$linkInQuestion->parents))) {
                    $nav[] = $value;
                }
            }
            break;
        case "children" :
            foreach ($sections as $key=>$value) {
                if ($value->parent == $linkInQuestion->id) {
                    $nav[] = $value;
                } elseif ((!empty($params['parents']) && $value->id == $linkInQuestion->parent) || (!empty($params['top']) && $value->depth == 0) || (!empty($params['top']) && in_array($value->id,$linkInQuestion->parents))) {
                    $nav[] = $value;
                }
            }
            break;
        case "siblingsandchildren" :
            foreach ($sections as $key=>$value) {
                if ($value->depth >= $linkInQuestion->depth && $value->depth <= $linkInQuestion->depth+1) {
                    if ($value->parent == 0) {
                        $nav[] = $value;
                    } else {
                        foreach ($value->parents as $parent) {
                            if ($parent == $linkInQuestion->id || $parent == $linkInQuestion->parent) {
                                $nav[] = $value ;
                                break;
                            } elseif ((!empty($params['parents']) && $value->id == $linkInQuestion->parent) || (!empty($params['top']) && $value->depth == 0) || (!empty($params['top']) && in_array($value->id,$linkInQuestion->parents))) {
                                $nav[] = $value;
                            }
                        }
                    }
                }
            }
            break;
        case "siblingsandallsubchildren" :
            foreach ($sections as $key=>$value) {
                if ($value->depth >= $linkInQuestion->depth) {
                    if ($value->parent == 0) {
                        $nav[] = $value;
                    } else {
                        foreach ($value->parents as $parent) {
                            if ($parent == $linkInQuestion->id || $parent == $linkInQuestion->parent) {
                                $nav[] = $value ;
                                break;
                            } elseif ((!empty($params['parents']) && $value->id == $linkInQuestion->parent) || (!empty($params['top']) && $value->depth == 0) || (!empty($params['top']) && in_array($value->id,$linkInQuestion->parents))) {
                                $nav[] = $value;
                            }
                        }
                    }
                }
            }
            break;
        case "allsubchildren" :
            foreach ($sections as $key=>$value) {
                foreach ($value->parents as $pkey=>$parent) {
                    if ($parent == $linkInQuestion->id) {
                        $nav[] = $value ;
                    } elseif ((!empty($params['parents']) && $value->id == $linkInQuestion->parent) || (!empty($params['top']) && $value->depth == 0) || (!empty($params['top']) && in_array($value->id,$linkInQuestion->parents))) {
                        $nav[] = $value;
                    }
                }
            }
            break;
        case "haschildren" :
            foreach ($sections as $key=>$value) {
                if ($value->parent == $linkInQuestion->id) {
                    $tmp[] = $value;
                }
            }
            if (count($tmp)>0) {
                $nav = 1;
            } else {
                $nav = 0;
            }
            break;
        case "hierarchy" :
            $nav = navigationController::navhierarchy();
            break;
        default :
            $nav = $sections;
    }
	$nav = (!empty($params['json'])) ? json_encode($nav) : $nav;
	if (isset($params['assign'])) $smarty->assign($params['assign'],$nav);
    else echo $nav;
}

?>
