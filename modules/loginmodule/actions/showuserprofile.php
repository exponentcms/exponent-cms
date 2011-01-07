<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

$i18n = exponent_lang_loadFile('modules/loginmodule/actions/login.php');

if (!defined('SYS_USERS')) require_once('subsystems/users.php');

$user_profile = null;
$user_profile->id = $_GET['id'];
$user_profile = exponent_users_getFullProfile($user_profile);

$exts = exponent_users_listExtensions();


$user_temp = $db->selectObject("user", "id=".$_GET['id']);
$profile = null;
if($profile != null) {
	$profile->firstname = $user_temp->firstname;
	$profile->lastname = $user_temp->lastname;
	$profile->username = $user_temp->username;
}

foreach ($user_profile as $key=>$value) {
  if (gettype($value) == "object") {
    foreach ($value as $key2=>$value2) {
      $profile->$key2 = $value2;
    }
  }
}

if (isset($profile->show_email_addy) && $profile->show_email_addy == 1) {
  $profile->email = $user_temp->email;
}

if (isset($profile->file_id) && $profile->file_id != "" && $profile->file_id != 0) {
  $file = $db->selectObject("file", "id=".$profile->file_id);
  $profile->image_url = $file->directory."/".$file->filename;
}

//eDebug($profile);exit();
$template = $template = new template("loginmodule","_show_userprofile");
$template->assign("profile",$profile);
$template->output();


?>
