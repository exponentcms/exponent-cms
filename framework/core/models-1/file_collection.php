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

class file_collection {
	function form($object) {
//		require_once(BASE.'framework/core/subsystems-1/forms.php');
		$form = new form();
		if (!isset($object->id)) {
			$object->name = '';
			$object->description = '';
		} else {
			$form->meta('id',$object->id);
		}

		$form->register('name',gt('Name'),new textcontrol($object->name));
		$form->register('description',gt('Description'),new htmleditorcontrol($object->description));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));

		return $form;
	}

	function update($values,$object) {
		$object->name = $values['name'];
		$object->description = $values['description'];
		return $object;
	}
}

?>