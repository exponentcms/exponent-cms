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
/** @define "BASE" "../../../../.." */

// GREP:HARDCODEDTEXT - 3am-6am is not i18n!

class bbextension {
	function name() { return exponent_lang_loadKey('subsystems/users/profileextensions/bbextension.php','extension_name'); }
	function author() { return 'Adam Kessler'; }
	function description() { return exponent_lang_loadKey('subsystems/users/profileextensions/bbextension.php','extension_description'); }

	function modifyForm($form,$user) { // new if !isset($user->id)
	
		$i18n = exponent_lang_loadFile('subsystems/users/profileextensions/bbextension.php');
	
		if (!isset($user->bb_user) || $user->bb_user == null) {
			$user->bb_user = bbextension::_blankBBUser();
		}

    if (!isset($user->bb_user->website) || $user->bb_user->website == "" ) {
      $user->bb_user->website = 'http://';
    }  
		$yesno = array(1=>$i18n['Yes'], 0=>$i18n['No']);	
		$form->register(null,"",new htmlcontrol('<hr size="1" /><b>'.$i18n['header'].'</b>'));
		$form->register("icq_num",$i18n['icq_num'], new textcontrol($user->bb_user->icq_num,16,false,15));
		$form->register("aim_addy",$i18n['aim_addy'], new textcontrol($user->bb_user->aim_addy,26,false,25));
		$form->register("msn_addy",$i18n['msn_addy'], new textcontrol($user->bb_user->msn_addy,26,false,25));
		$form->register("yahoo_addy",$i18n['yahoo_addy'], new textcontrol($user->bb_user->yahoo_addy,26,false,25));
		$form->register("skype_addy",$i18n['skype_addy'], new textcontrol($user->bb_user->skype_addy,26,false,25));
		$form->register("gtalk_addy",$i18n['gtalk_addy'], new textcontrol($user->bb_user->gtalk_addy,26,false,25));
		$form->register("website",$i18n['website'], new textcontrol($user->bb_user->website,46,false,55));
		$form->register("location",$i18n['location'], new textcontrol($user->bb_user->location,46,false,55));
		$form->register("occupation",$i18n['occupation'], new textcontrol($user->bb_user->occupation,46,false,55));
		$form->register("interests",$i18n['interests'], new texteditorcontrol($user->bb_user->interests,7,45));
		$form->register("signature",$i18n['signature'], new texteditorcontrol($user->bb_user->signature,7,45));
		$form->register("show_email_addy",$i18n['show_email_addy'], new radiogroupcontrol($user->bb_user->show_email_addy,$yesno,false,100,3));
		$form->register("hide_online_status",$i18n['hide_online_status'], new radiogroupcontrol($user->bb_user->hide_online_status,$yesno,false,100,3));
		$form->register("notify_of_replies",$i18n['notify_of_replies'], new radiogroupcontrol($user->bb_user->notify_of_replies,$yesno,false,100,3));
		$form->register("notify_of_pvt_msg",$i18n['notify_of_pvt_msg'], new radiogroupcontrol($user->bb_user->notify_of_pvt_msg,$yesno,false,100,3));
		$form->register("attach_signature",$i18n['attach_signature'], new radiogroupcontrol($user->bb_user->attach_signature,$yesno,false,100,3));
		
		//Show the avatar pic if there is one available.
		if ($user->bb_user->file_id != 0) {
			global $db;
			$file = $db->selectObject('file', 'id='.$user->bb_user->file_id);
			$form->register(null,"",new htmlcontrol('<hr size="1" />'));
			$form->register('file',$i18n['changefile'],new uploadcontrol());		
			$form->register(null,"",new htmlcontrol('<img src='.$file->directory.'/'.$file->filename.' border="0" />'));
		} else {
			$form->register('file',$i18n['file'],new uploadcontrol());		
		}

		return $form;
	}
	
	function saveProfile($values,$user,$is_new) {
		global $db;
		$bb_user = null;  
		$bb_user = $db->selectObject('bb_user', 'uid='.$user->id);
		//$db->delete("bb_user","uid=".$user->id);
		if($bb_user == null) $is_new = true;
		$bb_user->uid = intval( $user->id );
		$bb_user->icq_num = strip_tags($values['icq_num']);
		$bb_user->aim_addy = strip_tags($values['aim_addy']);
		$bb_user->msn_addy = strip_tags($values['msn_addy']);
		$bb_user->yahoo_addy = strip_tags($values['yahoo_addy']);
		$bb_user->skype_addy = strip_tags($values['skype_addy']);
		$bb_user->gtalk_addy = strip_tags($values['gtalk_addy']);
		$bb_user->website = strip_tags($values['website']);
		$bb_user->location = strip_tags($values['location']);
		$bb_user->occupation = strip_tags($values['occupation']);
		$bb_user->interests = strip_tags($values['interests']);
		$bb_user->signature = strip_tags($values['signature']);
		$bb_user->show_email_addy = $values['show_email_addy'];
		$bb_user->hide_online_status = $values['hide_online_status'];
		$bb_user->notify_of_replies = $values['notify_of_replies'];
		$bb_user->notify_of_pvt_msg = $values['notify_of_pvt_msg'];
		$bb_user->attach_signature = $values['attach_signature'];
	
			// check for avatar images.
		$filenew = $_FILES['file']['tmp_name'];
		$fileup = getimagesize ( $filenew );
		if ($fileup[2] > 0 && $fileup[1] > 0) {
			if ($fileup[0] <= 80 && $fileup[1] <= 80) {
//				if (!defined('SYS_FILES')) include_once(BASE.'framework/core/subsystems-1/files.php');
				include_once(BASE.'framework/core/subsystems-1/files.php');

				$directory = 'files/bbmodule/avatars';
				$fname = null;
				$file = null;
					//if the avatars directory is not there, create it.
				if (exponent_files_uploadDestinationFileExists($directory,'file')) {
					// Auto-uniqify Logic here
					$fileinfo = pathinfo($_FILES['file']['name']);
					$fileinfo['extension'] = '.'.$fileinfo['extension'];
					do {
							$fname = basename($fileinfo['basename'],$fileinfo['extension']).uniqid('').$fileinfo['extension'];
					} while (file_exists(BASE.$directory.'/'.$fname));
				}
				
				$bb_user->file_id = expValidator::uploadSuccessful(file::update('file',$directory,null,$fname));
				/*
				$file = file::update('file',$directory,null,$fname);
				if (is_object($file)) {
						$bb_user->file_id = $db->insertObject($file,'file');
				} else {
					// If file::update() returns a non-object, it should be a string.  That string is the error message.
					$post = $_POST;
					$post['_formError'] = $file;
					exponent_sessions_set('last_POST',$post);
					header('Location: ' . $_SERVER['HTTP_REFERER']);
					exit();
				}
				*/
			} else {
				$post = $_POST;
				$post['_formError'] = "Your avatar file is too large to upload.";
				exponent_sessions_set('last_POST',$post);
				header('Location: '.$_SERVER['HTTP_REFERER']);
				exit();
			}
		}
		if($is_new)
			$db->insertObject($bb_user,"bb_user");
		else
			$db->updateObject($bb_user,"bb_user","uid=".$user->id);
		$user->bb_user = $bb_user;
		unset($user->bb_user->uid);
		return $user;
	}
	
	function getProfile($user) {
		global $db;
		if (!isset($user->id)) {
			$user->bb_user = bbextension::_blankBBUser();
		} else {
			$user->bb_user = $db->selectObject("bb_user","uid=".$user->id);
		}
		return $user;
	}
	
	function cleanup($user) {
		global $db;
		$db->delete("bb_user","uid=".$user->id);
	}
	
	function clear() {
		global $db;
		$db->delete("bb_user");
	}
	
	function hasData() {
		global $db;
		return ($db->countObjects("bb_user") != 0);
	}
	
	function _blankBBUser() {
		$bb_user = null;
		$bb_user->icq_num = "";
		$bb_user->aim_addy = "";
		$bb_user->msn_addy = "";
		$bb_user->yahoo_addy = "";
		$bb_user->skype_addy = "";
		$bb_user->gtalk_addy = "";
		$bb_user->website = "";
		$bb_user->location = "";
		$bb_user->occupation = "";
		$bb_user->interests = "";
		$bb_user->signature = "";
		$bb_user->show_email_addy = "0";
		$bb_user->hide_online_status = "0";
		$bb_user->notify_of_replies = "0";
		$bb_user->notify_of_pvt_msg = "0";
		$bb_user->attach_signature = "0";
		$bb_user->file_id = "";
		return $bb_user;
	}
}

?>
