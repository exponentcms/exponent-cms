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
# $Id: poll_answer.php,v 1.2 2005/04/25 19:02:17 filetreefrog Exp $
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
		
		$form->register('answer','Answer',new textcontrol($object->answer));
		$form->register('submit','',new buttongroupcontrol('Save','','Cancel'));
		
		return $form;
	}
	
	static function update($values,$object) {
		$object->answer = $values['answer'];
		return $object;
	}
}

?>