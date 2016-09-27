<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

if (!defined('EXPONENT')) {
    exit('');
}

global $db;

$error = false;
// We have to force the language name into the config.php file
expSettings::change('LANGUAGE', LANGUAGE);

$user = $db->selectObject('user', 'is_system_user=1');

$user->username = $_REQUEST['username'];
$pwstrength = expValidator::checkPasswordStrength($_REQUEST['password']);
if ($user->username == '') {
    $error = true;
    $errorstr = gt('You must specify a valid username. Please check your entry.');
    $errorflag = '&errusername=true';
    echo $errorstr;
} elseif ($_REQUEST['password'] != $_REQUEST['password2']) {
    $error = true;
    $errorstr = gt('Your passwords do not match. Please check your entries.');
    $errorflag = '&errpassword=true';
    echo $errorstr;
} elseif (strcasecmp($user->username, $password) == 0) {
    $error = true;
    $errorstr = gt('The password cannot be the same as the username.');
    $errorflag = '&errpwusername=true';
    echo $errorstr;
} elseif ($pwstrength != '') {
    $error = true;
    $errorstr = $pwstrength . ' ' . gt('Please check your entries.');
    $errorflag = '&errpwstrength=true';
    echo $errorstr;
} elseif (!expValidator::validate_email_address($_REQUEST['email'])) {
    $error = true;
    $errorstr = gt('Your email address is invalid. Please check your entry.');
    $errorflag = '&erremail=true';
    echo $errorstr;
}

if ($error) { //NOTE Shouldn't get this because of check in install-6.php unless browser jscript disabled
    flash('error', $errorstr);
    header('Location: index.php?page=install-6' . $errorflag);
    exit();
} else {
    $user->password = user::encryptPassword($_REQUEST['password']);
    $user->firstname = $_REQUEST['firstname'];
    $user->lastname = $_REQUEST['lastname'];
    $user->is_admin = 1;
    $user->is_acting_admin = 1;
    $user->is_system_user = 1;
    $user->email = $_REQUEST['email'];
    $user->created_on = time();
    if (isset($user->id)) {
        $db->updateObject($user, 'user');
    } else {
        $db->insertObject($user, 'user');
    }
    header('Location: index.php?page=final');
}
?>