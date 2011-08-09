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

if (!defined('EXPONENT')) exit('');
global $user, $router, $db, $section;

if (!$db->selectValue('userpermission','uid','uid=\''.$user->id.'\' AND permission!=\'view\' AND internal!=\'\'') && !$user->isAdmin()) return false;

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

if ($db->selectValue('userpermission','uid','uid=\''.$user->id.'\' AND permission!=\'view\' AND internal='.$section.'') || $user->isAdmin()) {
    $info['itemdata'][] = array('text'=>gt('Edit this page'),'classname'=>'edit', 'url'=>makeLink(array('module'=>'navigationmodule', 'action'=>'edit_contentpage', 'id'=>$page->id)));
}
if ($user->isAdmin()) {
    $info['itemdata'][] = array('text'=>gt('Manage User Permissions'),'classname'=>'user', 'url'=>makeLink(array('module'=>'navigationmodule','action'=>'userperms',"_common"=>"1","int"=>$page->id)));
    $info['itemdata'][] = array('text'=>gt('Manage Group Permissions'),'classname'=>'group', 'url'=>makeLink(array('module'=>'navigationmodule','action'=>'groupperms',"_common"=>"1","int"=>$page->id)));
}
$info['itemdata'][] = array('text'=>gt('Manage all pages'),'classname'=>'sitetree', 'url'=>makeLink(array('module'=>'navigationmodule','action'=>'manage')));

return array(
    'text'=>gt('Pages'),
    'classname'=>'thispage',
    'submenu'=>$info
);


?>
