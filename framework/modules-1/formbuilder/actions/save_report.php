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

$rpt = null;
if (isset($_POST['id'])) {
	$rpt = $db->selectObject('formbuilder_report','id='.intval($_POST['id']));
}

if ($rpt) {
	if (expPermissions::check('editreport',unserialize($f->location_data))) {
		$rpt = formbuilder_report::update($_POST,$rpt);
		
		if (isset($rpt->id)) {
			$db->updateObject($rpt,'formbuilder_report');
		} else {
			$db->insertObject($rpt,'formbuilder_report');
		}
		
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>