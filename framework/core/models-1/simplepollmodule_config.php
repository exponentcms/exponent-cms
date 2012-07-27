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

class simplepollmodule_config {
	static function form($object) {
		$form = new form();
        $form->is_tabbed = true;
		if (!isset($object->id)) {
			$object->thank_you_message = gt('Thank you for voting.');
			$object->already_voted_message = gt('You have already voted in this poll.');
			$object->voting_closed_message = gt('Voting has been closed for this poll.');
			$object->anonymous_timeout = 5;
		} else {
			$object->anonymous_timeout /= 3600;
			$form->meta('id',$object->id);
		}
		
        $form->register(null,'',new htmlcontrol('<h2>'.gt('General Configuration').'</h2>'),true,gt('Simple Poll'));
		$form->register('thank_you_message',gt('\'Thank You\' Message'),new texteditorcontrol($object->thank_you_message,7,40),true,gt('Simple Poll'));
		$form->register('already_voted_message',gt('\'Already Voted\' Message'),new texteditorcontrol($object->already_voted_message,7,40),true,gt('Simple Poll'));
		$form->register('voting_closed_message',gt('\'Voting Closed\' Message'),new texteditorcontrol($object->voting_closed_message,7,40),true,gt('Simple Poll'));
		$form->register('anonymous_timeout',gt('Anonymous Block Timeout (hours)'),new textcontrol($object->anonymous_timeout),true,gt('Simple Poll'));
        $form->register('hidemoduletitle',gt("Hide Module Title?"),new checkboxcontrol(empty($object->hidemoduletitle)?'':$object->hidemoduletitle),true,gt('Module Title'));
        $form->register('moduledescription',gt("Module Description"),new htmleditorcontrol(empty($object->moduledescription)?'':$object->moduledescription),true,gt('Module Title'));
		$form->register('submit','',new buttongroupcontrol(gt('Save Config'),'',gt('Cancel')),true,'base');
		
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
        $object->hidemoduletitle = (isset($values['hidemoduletitle']) ? 1 : 0);
        $object->moduledescription = $values['moduledescription'];
		return $object;
	}
}

?>