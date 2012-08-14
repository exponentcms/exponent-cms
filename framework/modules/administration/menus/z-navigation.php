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

if (!defined('EXPONENT')) exit('');

global $user, $router, $db, $section;

// determine if the Pages menu should NOT be displayed
//if (!$db->selectValue('userpermission','uid','uid=\''.$user->id.'\' AND permission!=\'view\' AND internal!=\'\'') && !$user->isAdmin()) return false;
if (!$user->isAdmin()) {
    $pageperms = !$db->selectValue('userpermission','uid',"uid='".$user->id."' AND source=='' AND internal!=''");
    if (!$pageperms) {
        $groups = $user->getGroupMemberships;
        foreach ($groups as $group) {
            if (!$pageperms) {
                $pageperms = !$db->selectValue('grouppermission','gid',"gid='".$group->id."' AND source=='' AND internal!=''");
            } else {
                break;
            }
        }
    }
    if (!$pageperms) return false;
}

$type = "Page";
$page = $db->selectObject('section', 'id='.$section);
$subtheme = empty($page->subtheme) ? 'Default' : $page->subtheme;

$info = array(
    'id'=>'pgmgmt',
    'itemdata'=>array(
        array(
            'classname'=>'info',
            'text'=>gt('Information'),
            "submenu"=>array(
                'id'=>'pginfo',
                'itemdata'=>array(
                    array('classname'=>'moreinfo','text'=>gt("Name")." : ".$page->name."<br />ID : ".$page->id."<br />".gt("SEF Name")." : ".$page->sef_name."<br />".gt("Subtheme")." : ".$subtheme,"disabled"=>true)
                )
            )
        )
    )
);

if (expPermissions::check('manage',expCore::makeLocation('navigationController','',$section))) {
    $info['itemdata'][] = array('text'=>gt('Edit this page'),'classname'=>'edit', 'url'=>makeLink(array('module'=>'navigation', 'action'=>'edit_contentpage', 'id'=>$page->id)));
}

if ($user->isAdmin()) {
    $info['itemdata'][] = array('text'=>gt('Manage User Permissions'),'classname'=>'user', 'url'=>makeLink(array('module'=>'navigation','action'=>'userperms',"_common"=>"1","int"=>$page->id)));
    $info['itemdata'][] = array('text'=>gt('Manage Group Permissions'),'classname'=>'group', 'url'=>makeLink(array('module'=>'navigation','action'=>'groupperms',"_common"=>"1","int"=>$page->id)));
}
if (expPermissions::check('manage',expCore::makeLocation('navigationController','',$section))) {
    $info['itemdata'][] = array('text'=>gt('Manage all pages'),'classname'=>'sitetree', 'url'=>makeLink(array('module'=>'navigation','action'=>'manage')));
}

return array(
    'text'=>gt('Pages'),
    'classname'=>'thispage',
    'submenu'=>$info
);


?>
