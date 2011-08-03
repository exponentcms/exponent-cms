<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# Exponent is distributed in the hope that it
# will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR
# PURPOSE.  See the GNU General Public License
# for more details.
#
# You should have received a copy of the GNU
# General Public License along with Exponent; if
# not, write to:
#
# Free Software Foundation, Inc.,
# 59 Temple Place,
# Suite 330,
# Boston, MA 02111-1307  USA
#
# $Id: poll_question.php,v 1.3 2005/04/25 19:02:17 filetreefrog Exp $
##################################################
/** @define "BASE" "../../.." */

class poll_question {
	static function form($object) {
//		if (!defined('SYS_FORMS')) require_once(BASE.'framework/core/subsystems-1/forms.php');
		require_once(BASE.'framework/core/subsystems-1/forms.php');
//		exponent_forms_initialize();
		
		$form = new form();
		if (!isset($object->id)) {
			$object->question = '';
			$object->open_results = 1;
			$object->open_voting = 1;
			$object->is_active = 0;
		} else {
			$form->meta('id',$object->id);
		}
		
		$form->register('question','Question',new textcontrol($object->question));
		$form->register('open_results','Results are Publically Viewable',new checkboxcontrol($object->open_results,1));
		$form->register('open_voting','Open Voting?',new checkboxcontrol($object->open_voting,1));
		$form->register('submit','',new buttongroupcontrol('Save','','Cancel'));
		
		return $form;
	}
	
	static function update($values,$object) {
		$object->question = $values['question'];
		$object->open_results = (isset($values['open_results']) ? 1 : 0);
		$object->open_voting = (isset($values['open_voting']) ? 1 : 0);
		return $object;
	}
}

?>