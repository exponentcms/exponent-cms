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

$cols = $db->selectObjects('formbuilder_control', 'form_id='.intval($_POST['id']) . ' ORDER BY rank');
$counts = array();
$responses = array();

foreach($cols as $col) {
    $coldef = unserialize($col->data);
    $coldata = new ReflectionClass($coldef);
    $coltype = $coldata->getName();
    $value = call_user_func(array($coltype,'parseData'),$col->name,$_POST,true);
    $value = call_user_func(array($coltype,'templateFormat'),$value,$coldef);
    //eDebug($value);
    $counts[$col->caption] = isset($counts[$col->caption]) ? $counts[$col->caption] + 1 : 1;
    $num = $counts[$col->caption] > 1 ? $counts[$col->caption] : '';
    
    if (!empty($_POST[$col->name])) {
        if ($coltype == 'checkboxcontrol') {
            $responses[$col->caption.$num] = 'Yes';
        } else {
            $responses[$col->caption.$num] = $value;
        }
    } else {
        if ($coltype == 'checkboxcontrol') {
            $responses[$col->caption.$num] = 'No';
         }elseif ($coltype=='datetimecontrol') {
            $responses[$col->name] = $value;
        } elseif ($coltype == 'uploadcontrol') { 
            $_POST[$col->name] = URL_FULL.call_user_func(array($coltype,'moveFile'),$col->name,$_FILES,true);
            $value = call_user_func(array($coltype,'buildDownloadLink'),$_POST[$col->name],$_FILES[$col->name]['name'],true);
            //eDebug($value);
            $responses[$col->caption.$num] = $_FILES[$col->name]['name'];
        } elseif ($coltype != 'htmlcontrol') {            
            $responses[$col->caption.$num] = '';
        }    
    }
}

// remove some post data we don't want to pass thru to the form
unset($_POST['action']);
unset($_POST['module']);
foreach ($_POST as $k=>$v)
{
//    $_POST[$k]=htmlentities(htmlspecialchars($v,ENT_COMPAT,LANG_CHARSET));
    $_POST[$k]=htmlspecialchars($v,ENT_COMPAT,LANG_CHARSET);
}
exponent_sessions_set('formmodule_data_'.$_POST['id'], $_POST);

$template = new template("formbuilder","_confirm_form");
$template->assign('recaptcha_theme', RECAPTCHA_THEME);
$template->assign('responses', $responses);
$template->assign('postdata', $_POST);
$template->output();

?>
