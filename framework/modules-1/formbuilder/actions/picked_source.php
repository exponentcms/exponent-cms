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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

$f1_loc = expCore::makeLocation($_GET['sm'],$_GET['ss']);
$f1 = $db->selectObject("formbuilder_form","location_data='".serialize($f1_loc)."'");

$f2_loc = expCore::makeLocation($_GET['m'],$_GET['s']);
$f2 = $db->selectObject("formbuilder_form","location_data='".serialize($f2_loc)."'");

if ($f1 && $f2) {
	if (expPermissions::check("editform",unserialize($f2->location_data))) {
		$controls  = $db->selectObjects("formbuilder_control","form_id=".$f1->id);
		$controls = expSorter::sort(array('array'=>$controls,'sortby'=>'rank', 'order'=>'ASC'));

		foreach ($controls as $control) {
			$count = 0;
			$name = $control->name;
			$rank = $db->max("formbuilder_control","rank","form_id","form_id=".$f2->id);
			//insure that we have a unique name;
			while ($db->countObjects("formbuilder_control","form_id=".$f2->id." and name='".$name."'")) {
				$count++;
				$name = $control->name . $count;
			}
			$control->name = $name;
			unset($control->id);
			$control->rank = ++$rank;
			$control->form_id = $f2->id;
			$db->insertObject($control,"formbuilder_control");
		}
		formbuilder_form::updateTable($f2);
		
		echo '<script>window.opener.location = window.opener.location; window.close();</script>';
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>