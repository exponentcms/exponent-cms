<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

//filter the message thru the form template for formatting
$msgtemplate = new formtemplate('forms/calendar', '_'.$_POST['formname']);
$msgtemplate->assign('post', $_POST);
$msg = $msgtemplate->render();
$ret = false;

//make sure this is from a valid event and that the email addresses are listed, then mail
if (isset($_POST['id'])) {
	$event = $db->selectObject('calendar','id='.intval($_POST['id']));
	$email_addrs = array();
	if ($event->feedback_email != '') {
		$email_addrs = explode(',', $event->feedback_email);
		//This is an easy way to remove duplicates
		$email_addrs = array_flip(array_flip($email_addrs));
		$email_addrs = array_map('trim', $email_addrs);

		$ret = 0;
		$mail = new expMail();
		$ret += $mail->quickSend(array(
				"text_message"=>$msg,
				'to'=>$email_addrs,
				'from'=>trim(SMTP_FROMADDRESS),
				'subject'=>$_POST['subject'],
		));
	}
}

$template = new template('calendarmodule','_feedback_submitted');
$template->assign('success',($ret?1:0));
$template->output();

?>
