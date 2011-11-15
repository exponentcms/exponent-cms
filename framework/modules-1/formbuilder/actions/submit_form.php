<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');

// Check for form errors
$post = $_POST;
$post['manual_redirect'] = true;
if (!expValidator::check_antispam($post)) {
    flash('error', gt('Security Validation Failed'));
    expHistory::back();
}

global $db, $user;
$f = $db->selectObject("formbuilder_form","id=".intval($_POST['id']));
$rpt = $db->selectObject("formbuilder_report","form_id=".intval($_POST['id']));
$controls = $db->selectObjects("formbuilder_control","form_id=".$f->id." and is_readonly=0");
$controls = expSorter::sort(array('array'=>$controls,'sortby'=>'rank', 'order'=>'ASC'));

$db_data = null;
$emailFields = array();
$captions = array();
foreach ($controls as $c) {
    $ctl = unserialize($c->data);
    $control_type = get_class($ctl);
    $def = call_user_func(array($control_type,"getFieldDefinition"));
    if ($def != null) {
        $emailValue = htmlspecialchars_decode(call_user_func(array($control_type,'parseData'),$c->name,$_POST,true));
        $value = stripslashes($db->escapeString($emailValue));

        //eDebug($value);
        $varname = $c->name;
        $db_data->$varname = $value;
//        $fields[$c->name] = call_user_func(array($control_type,'templateFormat'),$value,$ctl);
//        $emailFields[$c->name] = call_user_func(array($control_type,'templateFormat'),$emailValue,$ctl);
        $emailFields[$c->name] = call_user_func(array($control_type,'templateFormat'),$value,$ctl);
        $captions[$c->name] = $c->caption;
		if ($c->name == "email") {
			$from = $value;
		}
		if ($c->name == "name") {
			$from_name = $value;
		}
    }
}

if (!isset($_POST['data_id']) || (isset($_POST['data_id']) && expPermissions::check("editdata",unserialize($f->location_data)))) {
    if ($f->is_saved == 1) {    
        if (isset($_POST['data_id'])) {
            //if this is an edit we remove the record and insert a new one.
            $olddata = $db->selectObject('formbuilder_'.$f->table_name,'id='.intval($_POST['data_id']));
            $db_data->ip = $olddata->ip;
            $db_data->user_id = $olddata->user_id;
            $db_data->timestamp = $olddata->timestamp;
            $db->delete('formbuilder_'.$f->table_name,'id='.intval($_POST['data_id']));
        } 
        else {
            $db_data->ip = $_SERVER['REMOTE_ADDR'];
            if (expSession::loggedIn()) {
                $db_data->user_id = $user->id;
				$from = $user->email;
				$from_name = $user->firstname." ".$user->lastname." (".$user->username.")";
            } else {
                $db_data->user_id = 0;
            }
            $db_data->timestamp = time();
        }        
        $db->insertObject($db_data, 'formbuilder_'.$f->table_name);
    }

    //Email stuff here...
    //Don't send email if this is an edit.
    if ($f->is_email == 1 && !isset($_POST['data_id'])) {
        //Building Email List...
        $emaillist = array();
        foreach ($db->selectObjects("formbuilder_address","form_id=".$f->id) as $address) {
            if ($address->group_id != 0) {
                foreach (group::getUsersInGroup(group::getGroupById($address->group_id)) as $locUser){
                    if ($locUser->email != '') $emaillist[] = $locUser->email;
                }
            } else if ($address->user_id != 0) {
                $locUser = user::getUserById($address->user_id);
                if ($locUser->email != '') $emaillist[] = $locUser->email;
            } else if ($address->email != '') {
                $emaillist[] = $address->email;
            }
        }
        if ($rpt->text == "") {
            $template = new template("formbuilder","_default_report");
        } else {
            $template = new template("formbuilder","_custom_report");
            $template->assign("template",$rpt->text);
        }
        $template->assign("fields",$emailFields);
        $template->assign("captions",$captions);
		$template->assign('title',$rpt->name);
        $template->assign("is_email",1);
        $emailText = $template->render();
		$emailText = chop(strip_tags(str_replace(array("<br />","<br>","br/>"),"\n",$emailText)));
		$template->assign("css",file_get_contents(BASE."framework/core/assets/css/tables.css"));
		$emailHtml = $template->render();
		if (empty($from)) {
			$from = trim(SMTP_FROMADDRESS);
		}
		if (empty($from_name)) {
			$from_name = trim(ORGANIZATION_NAME);
		}
		$headers = array(
			"MIME-Version"=>"1.0",
			"Content-type"=>"text/html; charset=".LANG_CHARSET
		);
        if (count($emaillist)) {
            //This is an easy way to remove duplicates
            $emaillist = array_flip(array_flip($emaillist));
            $emaillist = array_map('trim', $emaillist);
			$mail = new expMail();
			$mail->quickSend(array(
					'headers'=>$headers,
					'html_message'=>$emailHtml,
					"text_message"=>$emailText,
					'to'=>$emaillist,
					'from'=>array(trim($from)=>$from_name),
					'subject'=>$f->subject,
			));
        }
    }

    // clear the users post data from the session.
    expSession::un_set('formmodule_data_'.$f->id);

    //If is a new post show response, otherwise redirect to the flow.
    if (!isset($_POST['data_id'])) {
        $template = new template("formbuilder","_view_response");
//        global $SYS_FLOW_REDIRECTIONPATH;
//        $SYS_FLOW_REDIRECTIONPATH = "editfallback";
        $template->assign("backlink",expHistory::getLastNotEditable());
//        $SYS_FLOW_REDIRECTIONPATH = "exponent_default";
        $template->assign("response_html",$f->response);
        $template->output();
    } else {
		flash ('message', 'Record was updated!');
//        expHistory::back();
        expHistory::returnTo('editable');
    }
} else {
    echo SITE_403_HTML;
}

?>
