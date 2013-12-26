<?php
//FIXME Deprecated! Not used
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Anti-Spam Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class antispamcontrol extends formcontrol {  //FIXME this is old tech and not-language-ized

	static function name() { return "Anti-Spam Control"; }

	function controlToHTML($name,$label) {
		$html = '<div class="antispam">This question is being presented to you to try to differentiate between a human submission and
			a bot in an effort to reduce spam. Please choose the obvious answer below or your inquiry will not be successfully submitted.<br /><br />';
		switch ( rand(1,2) ) {
			case 1:
				$html .= '<label>I am not human: <input class="antispamcontrol" type="checkbox" id="checker" name="checker" value="1" checked="checked">(true)</label>';
			break;
			case 2:
				$html .= '<label>I am a robot: <input class="antispamcontrol" type="radio" id="checker" name="checker" value="1" checked="checked"></label><br />';
				$html .= '<label>I am a cat: <input class="antispamcontrol" type="radio" id="checker" name="checker" value="2"></label><br />';
				$html .= '<label>I am a human: <input class="antispamcontrol" type="radio" id="checker" name="checker" value="0"></label>';
			break;
		}
		$html .= '</div>';

		return $html;
	}

	static function form($object) {
		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
			$object->default = "";
			$object->size = 0;
			$object->maxlength = 0;
			$object->required = false;
		}
		$form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
		$form->register(null, null, new htmlcontrol('<br />'));
		$form->register(null, null, new htmlcontrol('<br />'));
		$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		return $form;
	}

    static function update($values, $object) {
		if ($object == null) $object = new antispamcontrol();
		if ($values['identifier'] == "") {
			$post = $_POST;
			$post['_formError'] = gt('Identifier is required.');
			expSession::set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
		$object->default = $values['default'];
		$object->size = intval($values['size']);
		$object->maxlength = intval($values['maxlength']);
		$object->required = isset($values['required']);
		return $object;
	}

}

?>
