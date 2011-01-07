<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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
if (!$user->isAdmin()) return false;

$type = "Page";
$page = $db->selectObject('section', 'id='.$section);
$subtheme = empty($page->subtheme) ? 'Default' : $page->subtheme;
$info = array(
    'id'=>'pgmgmt',
    'itemdata'=>array(
        array(
            'classname'=>'info',
            'text'=>'Information',
            "submenu"=>array(
                'id'=>'pginfo',
                'itemdata'=>array(
                    array('classname'=>'moreinfo','text'=>"Name : ".$page->name."<br />SEF Name : ".$page->sef_name."<br />Subtheme : ".$subtheme,"disabled"=>true)
                )
            )
        ),
        array('text'=>'Edit this page','classname'=>'edit', 'url'=>makeLink(array('module'=>'navigationmodule', 'action'=>'edit_contentpage', 'id'=>$page->id))),
        array('text'=>'Manage all pages','classname'=>'sitetree', 'url'=>makeLink(array('module'=>'navigationmodule','action'=>'manage'))),
    )
);

return array(
    'text'=>'Pages',
    'classname'=>'thispage',
    'submenu'=>$info
);


?>
