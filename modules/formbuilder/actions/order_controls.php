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

$_GET['a'] = intval($_GET['a']);
$_GET['b'] = intval($_GET['b']);
$_GET['p'] = intval($_GET['p']);

$a = $db->selectObject('formbuilder_control','form_id='.$_GET['p'].' AND rank='.$_GET['a']);
$b = $db->selectObject('formbuilder_control','form_id='.$_GET['p'].' AND rank='.$_GET['b']);
if ($a && $b) {
	$f = $db->selectObject('formbuilder_form','id='.$a->form_id);
	if (exponent_permissions_check('editform',unserialize($f->location_data))) {
		$tmp = $a->rank;
		$a->rank = $b->rank;
		$b->rank = $tmp;
		
		$db->updateObject($a,'formbuilder_control');
		$db->updateObject($b,'formbuilder_control');
		
		exponent_flow_redirect();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>