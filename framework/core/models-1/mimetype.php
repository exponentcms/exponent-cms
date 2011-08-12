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
		$form = new form();
		if (!isset($object->mimetype)) {
			$object->mimetype = '';
			$object->name = '';
			$object->icon = '';
		} else {
			$form->meta('oldmime',$object->mimetype);
		}
		
		$form->register('mimetype',gt('MIME Type'), new textcontrol($object->mimetype));
		$form->register('name',gt('Name'),new textcontrol($object->name));
		
		$icodir = MIMEICON_RELATIVE;
		$htmlimg = ($object->icon == '' ? '' : '<img src="'.MIMEICON_RELATIVE.$object->icon.'"/>');
		// Replace this with something a little better.

		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
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