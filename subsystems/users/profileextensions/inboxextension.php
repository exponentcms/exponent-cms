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

class inboxextension {
	function name() { return exponent_lang_loadKey('subsystems/users/profileextensions/inboxextension.php','extension_name'); }
	function author() { return 'James Hunt'; }
	function description() { return exponent_lang_loadKey('subsystems/users/profileextensions/inboxextension.php','extension_description'); }

	function modifyForm($form,$u) { // new if !isset($user->id)
		$i18n = exponent_lang_loadFile('subsystems/users/profileextensions/inboxextension.php');
		
		if (!isset($u->_inbox_config) || $u->_inbox_config == null) {
			$u->_inbox_config = inboxextension::_blank();
		}
		$form->register(null,'',new htmlcontrol('<hr size="1" /><b>'.$i18n['header'].'</b>'));
		$form->register('inbox_forward',$i18n['forward'], new checkboxcontrol($u->_inbox_config->forward,true));
		
		return $form;
	}
	
	function saveProfile($values,$user,$is_new) {
		global $db;
		$db->delete('inbox_userconfig','id='.$user->id);
		
		$inboxcfg = null;
		$inboxcfg->id = intval($user->id);
		$inboxcfg->forward = (isset($values['inbox_forward']) ? 1 : 0);
		
		$db->insertObject($inboxcfg,'inbox_userconfig');
		return $user;
	}
	
	function getProfile($user) {
		global $db;
		if (!isset($user->id)) {
			$user->_inbox_config = inboxextension::_blank();
		} else {
			$user->_inbox_config = $db->selectObject('inbox_userconfig','id='.$user->id);
		}
		return $user;
	}
	
	function cleanup($user) {
		global $db;
		$db->delete('inbox_userconfig','id='.$user->id);
	}
	
	function clear() {
		global $db;
		$db->delete('inbox_userconfig');
	}
	
	function hasData() {
		global $db;
		return ($db->countObjects('inbox_userconfig') != 0);
	}
	
	function _blank() {
		$cfg = null;
		$cfg->forward = 1;
		return $cfg;
	}
}

?>