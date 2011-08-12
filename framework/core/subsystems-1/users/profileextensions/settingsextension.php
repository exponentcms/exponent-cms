<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright 2006 Maxim Mueller
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

class settingsextension {
	function name() { return 'Settings Extension'; }
	function author() { return 'Maxim Mueller'; }
	function description() { return "Stores a user's preferences for certain site settings."; }

	function modifyForm($form, $user) { // new if !isset($user->id)
		if (!isset($user->user_settings) || $user->user_settings == null) {
			$user->user_settings = settingsextension::_setDefaults();
		}
		
		$form->register(null, "", new htmlcontrol('<hr/><b>' . gt('User Settings') . '</b>'));
		$form->register("SITE_WYSIWYG_EDITOR", gt('Preferred Editor'), new textcontrol($user->user_settings->WYSIWYG_EDITOR, 16, false, 15));
		$form->register("USE_LANG", gt('Preferred Language'), new textcontrol($user->user_setings->USE_LANG, 16, false, 15));
		
		return $form;
	}
	
	function saveProfile($values, $user, $is_new) {
		global $db;
		$db->delete("user_settings", "uid=" . $user->id);
		
		$settings = null;
		$settings->uid = intval( $user->id );
		$settings->SITE_WYSIWYG_EDITOR = $values['SITE_WYSIWYG_EDITOR'];
		$settings->USE_LANG = $values['LANG'];
		
		$db->insertObject($settings, "user_settings");
		
		$user->user_settings = $settings;
		unset($user->user_settings->uid);
		return $user;
	}
	
	function getProfile($user) {
		global $db;
		if (!isset($user->id)) {
			$user->user_settings = settingsextension::_setDefaults();
		} else {
			$user->user_settings = $db->selectObject("user_settings", "uid=" . $user->id);
		}
		return $user;
	}
	
	function cleanup($user) {
		global $db;
		$db->delete("user_settings", "uid=" . $user->id);
	}
	
	function clear() {
		global $db;
		$db->delete("user_settings");
	}
	
	function hasData() {
		global $db;
		return ($db->countObjects("user_settings") != 0);
	}
	
	function _setDefaults() {
		$settings = null;
		
		//set to empty values => no override of site defaults
		$settings->SITE_WYSIWYG_EDITOR = "";
		$settings->USE_LANG = "";
		return $settings;
	}
}

?>