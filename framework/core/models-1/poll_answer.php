<?php
##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

class poll_answer {
	static function form($object) {
		$form = new form();
		if (!isset($object->id)) {
			$object->answer = '';
		} else {
			$form->meta('id',$object->id);
		}
		
		$form->register('answer',gt('Answer'),new textcontrol($object->answer));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		
		return $form;
	}
	
	static function update($values,$object) {
		$object->answer = $values['answer'];
		return $object;
	}
}

?>