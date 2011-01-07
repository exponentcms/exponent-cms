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

$f = null;
if (isset($_GET['id'])) {
	$f = $db->selectObject('formbuilder_form','id='.intval($_GET['id']));
}

if ($f) {
	$floc = unserialize($f->location_data);
	if (exponent_permissions_check('editform',unserialize($f->location_data))) {
		$form = formbuilder_form::form($f);
		$form->location($loc);
		$form->meta('action','save_form');
		$form->meta('m',$floc->mod);
		$form->meta('s',$floc->src);
		$form->meta('i',$floc->int);
		echo $form->toHTML();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>