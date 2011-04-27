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

//TODO: bring back icon selector, this time based on the filebrowser engine
class mimetype {
	function form($object) {
		$i18n = exponent_lang_loadFile('datatypes/mimetype.php');
		
		$form = new form();
		if (!isset($object->mimetype)) {
			$object->mimetype = '';
			$object->name = '';
			$object->icon = '';
		} else {
			$form->meta('oldmime',$object->mimetype);
		}
		
		$form->register('mimetype',$i18n['mimetype'], new textcontrol($object->mimetype));
		$form->register('name',$i18n['name'],new textcontrol($object->name));
		
		$icodir = MIMEICON_RELATIVE;
		$htmlimg = ($object->icon == '' ? '' : '<img src="'.MIMEICON_RELATIVE.$object->icon.'"/>');
		// Replace this with something a little better.

		$form->register('submit','',new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
		return $form;
	}
	
	function update($values,$object) {
		$object->mimetype = $values['mimetype'];
		$object->name = $values['name'];
		// temp fix
		$object->icon = "binary.png";
		
		return $object;
	}
}

?>