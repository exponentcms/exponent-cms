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

$ctl = null;
if (isset($_GET['id'])) {
	$ctl = $db->selectObject('formbuilder_control','id='.intval($_GET['id']));
}


if ($ctl) {
	$f = $db->selectObject('formbuilder_form','id='.$ctl->form_id);
	if (exponent_permissions_check('editform',unserialize($f->location_data))) {
		$db->delete('formbuilder_control','id='.$ctl->id);
		$db->decrement('formbuilder_control','rank',1,'form_id='.$ctl->form_id.' AND rank > ' . $ctl->rank);
		
		$f = $db->selectObject('formbuilder_form','id='.$ctl->form_id);
		formbuilder_form::updateTable($f);
		
		exponent_flow_redirect();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>