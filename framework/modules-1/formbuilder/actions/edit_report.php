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

if (!defined('EXPONENT')) exit('');

$f = null;
$rept = null;
if (isset($_GET['id'])) {
	$f = $db->selectObject('formbuilder_form','id='.intval($_GET['id']));
}

if ($f) {
	if (expPermissions::check('editreport',unserialize($f->location_data))) {
		$floc = unserialize($f->location_data);
		$rept = $db->selectObject('formbuilder_report','form_id='.$f->id);
	
		$form = formbuilder_report::form($rept);
		$form->location($loc);
		$form->meta('action','save_report');
		$form->meta('id',$rept->id);
		$form->meta('m',$floc->mod);
		$form->meta('s',$floc->src);
		$form->meta('i',$floc->int);
		$template = new template('formbuilder','_edit_report');
		$template->assign('form_html',$form->toHTML($f->id));
		$template->output();		
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>