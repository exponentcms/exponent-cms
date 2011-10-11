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

//Sanity Check
if (!defined('EXPONENT')) exit('');

$file = fopen(BASE.$_POST["filename"],"r");
$post = null;
$post = $_POST;
$userinfo = null;
$userinfo->username = "";
$userinfo->firstname = "";
$userinfo->lastname = "";
$userinfo->is_admin = 0;
$userinfo->is_acting_admin = 0;
$userinfo->is_locked = 0;
$userinfo->email = '';
$userarray = array();
$usersdone = array();
$linenum = 1;

while ( ($filedata = fgetcsv($file, 2000, $_POST["delimiter"])) != false){

if ($linenum >= $post["rowstart"]){
	$i = 0;

	$userinfo = null;
	$userinfo->changed = "";

        foreach ($filedata as $field){
		if ($post["column"][$i] != "none"){
			$colname = $post["column"][$i];
			$userinfo->$colname = trim($field);	
		}
		$i++;
	}

	switch ($post["unameOptions"]){

	case "FILN":
		if ( ($userinfo->firstname != "") && ($userinfo->lastname != "") ) {
			$userinfo->username = str_replace(" ", "", strtolower($userinfo->firstname{0}.$userinfo->lastname));
		}else{
			$userinfo->username = "";
			$userinfo->clearpassword = "";
			$userinfo->changed = "skipped";
		}
		break;
	case "FILNNUM":
		if ( ($userinfo->firstname != "") && ($userinfo->lastname != "") ) {
			$userinfo->username = str_replace(" ", "", strtolower($userinfo->firstname{0}.$userinfo->lastname.rand(100,999)));
                }else{
			$userinfo->username = "";
			$userinfo->clearpassword = "";
                        $userinfo->changed = "skipped";
                }
                break;
	case "EMAIL":
		if ($userinfo->email != "") {
			$userinfo->username = str_replace(" ", "", strtolower($userinfo->email));
                }else{
			$userinfo->username = "";
			$userinfo->clearpassword = "";
                        $userinfo->changed = "skipped";
                }
		break;
	case "FNLN":
		if ( ($userinfo->firstname != "") && ($userinfo->lastname != "") ) {
			$userinfo->username = str_replace(" ", "",strtolower($userinfo->firstname.$userinfo->lastname));
                }else{
			$userinfo->username = "";
			$userinfo->clearpassword = "";
                        $userinfo->changed = "skipped";
                }
		break;
	case "INFILE":
		if ($userinfo->username != "") {
			$userinfo->username = str_replace(" ", "", $userinfo->username);
                }else{
			$userinfo->username = "";
			$userinfo->clearpassword = "";
                        $userinfo->changed = "skipped";
                }
		break;
	}

	if ( (!isset($userinfo->changed)) || ($userinfo->changed != "skipped")) {
		switch ($post["pwordOptions"]){

		case "RAND":
			$newpass = "";
                	for ($i = 0; $i < rand(12,20); $i++) {
                        	$num=rand(48,122);
                        	if(($num > 97 && $num < 122) || ($num > 65 && $num < 90) || ($num >48 && $num < 57)) $newpass.=chr($num);
                        	else $i--;
                	}
			$userinfo->clearpassword = $newpass;
			break;
		case "DEFPASS":
			$userinfo->clearpassword = str_replace(" ", "", trim($_POST["pwordText"]));
			break;
		}

		$userinfo->password = md5($userinfo->clearpassword);

		$suffix = "";
		while (user::getUserByName($userinfo->username.$suffix) != null) {//username already exists
			if (isset($_POST["update"]) == 1 ) {
				if (in_array($userinfo->username, $usersdone)) {
					$suffix = rand(100,999);
					$userinfo->changed = 1;	
				}else{
					$tmp = user::getUserByName($userinfo->username.$suffix);
					$userinfo->id = $tmp->id;
					break;
				}
			}else{
				$suffix = rand(100,999);
                                $userinfo->changed = 1;
			}
		}

		$userinfo->username = $userinfo->username.$suffix;	
		$userarray[] = exponent_users_saveUser($userinfo);
		$usersdone[] = $userinfo->username;
	}else{
		$userinfo->linenum = $linenum;
		$userarray[] = $userinfo;
	}	
}
	$linenum++;
}
$template = New Template("importer", "_usercsv_display_users");
$template->assign("userarray", $userarray);
$template->output();
unlink(BASE.$_POST["filename"]);
?>
