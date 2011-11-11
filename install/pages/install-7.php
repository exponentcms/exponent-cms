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

$error = false;
$lang = (defined('LANGUAGE')) ? "&lang='".LANGUAGE."'" : '';
// We have to force the language name into the config.php file
expSettings::change('LANGUAGE',LANGUAGE);

$user = $db->selectObject('user','is_admin=1');

$user->username = $_POST['username'];
if ($user->username == '') {
    $error = true;
	$errorstr = gt('You must specify a valid username.');
    $errorflag = '&errusername=true';
    echo $errorstr;
} elseif ($_POST['password'] != $_POST['password2']) {
    $error = true;
    $errorstr = gt('Your passwords do not match. Please check your entries.');
    $errorflag = '&errpassword=true';
    echo $errorstr;
} elseif (!expValidator::validate_email_address($_POST['email'])) {
    $error = true;
    $errorstr = gt('Your email address is invalid. Please check your entry.');
    $errorflag = '&erremail=true';
    echo $errorstr;
}

if ($error) {  //FIXME Shouldn't get this because of check in install-6.php unless browser jscript disabled
    flash('error',$errorstr);
    header('Location: index.php?page=install-6'.$errorflag.$lang);
    exit();
} else {
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
	header('Location: '.'index.php?page=final'.$lang);
}

?>
