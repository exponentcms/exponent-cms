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

if (!defined('EXPONENT')) exit('');
global $db;

$user = $db->selectObject('user','is_admin=1');

$user->username = $_POST['username'];
if ($user->username == '') {  //FIXME Shouldn't get this because of check in install-6.php
	echo gt('You must specify a valid username.');
} else {
    if (expValidator::validate_email_address($_POST['email']) == false) {
        flash('error',gt('You must supply a valid email address.'));
        header('Location: index.php?page=install-6&erremail=true');
        exit();
    }
	$user->password = md5($_POST['password']);
	$user->firstname = $_POST['firstname'];
	$user->lastname = $_POST['lastname'];
	$user->is_admin = 1;
	$user->is_acting_admin = 1;
	$user->email = $_POST['email'];
	$user->created_on = time();
	if (isset($user->id)){
		$db->updateObject($user,'user');
	}else{
		$db->insertObject($user,'user');
	}
	$lang = (defined('LANGUAGE')) ? "&lang='".LANGUAGE."'" : '';

	header('Location: '.'index.php?page=final'.$lang);
}

?>
