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

if (expPermissions::check('order_modules',$loc)) {
//	$db->switchValues('container','rank',$_GET['a'],$_GET['b'],"external='".serialize($loc)."'");
	$object_a = $db->selectObject('container',"rank='".$_GET['a']."' AND external='".serialize($loc)."'");
	$object_b = $db->selectObject('container',"rank='".$_GET['b']."' AND external='".serialize($loc)."'");

	if ($object_a && $object_b) {
		$db->switchValues('container','rank',$_GET['a'],$_GET['b'],"external='".serialize($loc)."'");
	} else {
		if ($object_a) {
			$object_a->rank = $_GET['b'];
			$db->updateObject($object_a,'container');
		}
		if ($object_b) {
			$object_b->rank = $_GET['a'];
			$db->updateObject($object_b,'container');
		}
	}
   	expSession::clearAllUsersSessionCache('containermodule');
    expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
