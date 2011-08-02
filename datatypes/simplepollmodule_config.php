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
# $Id: simplepollmodule_config.php,v 1.2 2005/04/25 19:02:17 filetreefrog Exp $
##################################################
/** @define "BASE" ".." */

class simplepollmodule_config {
	function form($object) {
//		if (!defined('SYS_FORMS')) require_once(BASE.'subsystems/forms.php');
		require_once(BASE.'subsystems/forms.php');
//		exponent_forms_initialize();
		
		$form = new form();
		if (!isset($object->id)) {
			$object->thank_you_message = 'Thank you for voting.';
			$object->already_voted_message = 'You have already voted in this poll.';
			$object->voting_closed_message = 'Voting has been closed for this poll.';
			$object->anonymous_timeout = 5;
		} else {
			$object->anonymous_timeout /= 3600;
			$form->meta('id',$object->id);
		}
		
		$form->register('thank_you_message','"Thank You" Message',new texteditorcontrol($object->thank_you_message,7,40));
		$form->register('already_voted_message','"Already Voted" Message',new texteditorcontrol($object->already_voted_message,7,40));
		$form->register('voting_closed_message','"Voting Closed" Message',new texteditorcontrol($object->voting_closed_message,7,40));
		$form->register('anonymous_timeout','Anonymous Block Timeout (hours)',new textcontrol($object->anonymous_timeout));
		$form->register('submit','',new buttongroupcontrol('Save','','Cancel'));
		
		return $form;
	}
	
	function update($values,$object) {
		$object->thank_you_message = $values['thank_you_message'];
		$object->already_voted_message = $values['already_voted_message'];
		$object->voting_closed_message = $values['voting_closed_message'];
		$object->anonymous_timeout = $values['anonymous_timeout']*3600;
		if ($object->anonymous_timeout <= 0) {
			$object->anonymous_timeout = 5*3600;
		}
		return $object;
	}
}

?>