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

class addressextension {
	function name() { return exponent_lang_loadKey('subsystems/users/profileextensions/addressextension.php','extension_name'); }
	function author() { return "James Hunt"; }
	function description() { return exponent_lang_loadKey('subsystems/users/profileextensions/addressextension.php','extension_description'); }

	function modifyForm($form,$user) { // new if !isset($user->id)
	
		$i18n = exponent_lang_loadFile('subsystems/users/profileextensions/addressextension.php');
	
		if (!isset($user->user_address) || $user->user_address == null) {
			$user->user_address = addressextension::_blankAddress();
		}
		$form->register(null,"",new htmlcontrol('<hr size="1" /><b>'.$i18n['header'].'</b>'));
		$form->register("address1",$i18n['address1'], new textcontrol($user->user_address->address1));
		$form->register("address2",$i18n['address2'], new textcontrol($user->user_address->address2));
		$form->register("city",$i18n['city'], new textcontrol($user->user_address->city));
		$form->register("state",$i18n['state'], new textcontrol($user->user_address->state));
		$form->register("zip",$i18n['zip'], new textcontrol($user->user_address->zip));
		$form->register("country",$i18n['country'], new textcontrol($user->user_address->country));
		
		return $form;
	}
	
	function saveProfile($values,$user,$is_new) {
		global $db;
		$db->delete("user_address","uid=".$user->id);
		
		$address = null;
		$address->uid = intval( $user->id );
		$address->address1 = $values['address1'];
		$address->address2 = $values['address2'];
		$address->city = $values['city'];
		$address->state = $values['state'];
		$address->zip = $values['zip'];
		$address->country = $values['country'];
		
		$db->insertObject($address,"user_address");
		
		$user->user_address = $address;
		unset($user->user_address->uid);
		return $user;
	}
	
	function getProfile($user) {
		global $db;
		if (!isset($user->id)) {
			$user->user_address = addressextension::_blankAddress();
		} else {
			$user->user_address = $db->selectObject("user_address","uid=".$user->id);
		}
		return $user;
	}
	
	function cleanup($user) {
		global $db;
		$db->delete("user_address","uid=".$user->id);
	}
	
	function clear() {
		global $db;
		$db->delete("user_address");
	}
	
	function hasData() {
		global $db;
		return ($db->countObjects("user_address") != 0);
	}
	
	function _blankAddress() {
		$address = null;
		$address->address1 = "";
		$address->address2 = "";
		$address->city = "";
		$address->state = "";
		$address->zip = "";
		$address->country = "";
		return $address;
	}
}

?>