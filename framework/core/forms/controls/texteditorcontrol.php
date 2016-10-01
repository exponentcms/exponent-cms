<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * Text Editor Control - displays text area widget
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class texteditorcontrol extends formcontrol {

	var $placeholder = "";
    var $rows = 5;
	var $cols = 38;
    var $maxlength = "";

	static function name() { return "Text Area"; }
	static function isSimpleControl() { return true; }
	static function getFieldDefinition() {
		return array(
			DB_FIELD_TYPE=>DB_DEF_STRING,
			DB_FIELD_LEN=>10000);
	}

	function __construct($default="",$rows = 5,$cols = 38) {
		$this->default = $default;
		$this->rows = $rows;
		$this->cols = $cols;
		$this->required = false;
		$this->maxchars = 0;
	}

	function controlToHTML($name,$label) {
        $html = ($this->horizontal && bs3()) ? '<div class="col-sm-10">' : '';
        $idname  = (!empty($this->id)) ? $this->id : $this->name;
        if (empty($idname))
            $idname = $name;
		$html .= "<textarea class=\"textarea" . (bs3() ? " form-control" : "") . "\" id=\"$idname\" name=\"$name\"";
        if ($this->focus) $html .= " autofocus";
		$html .= " rows=\"" . $this->rows . "\" cols=\"" . $this->cols . "\"";
        $html .= ($this->maxlength?" maxlength=\"".$this->maxlength."\"":"");
		if ($this->accesskey != "") $html .= " accesskey=\"" . $this->accesskey . "\"";
		$html .= $this->placeholder ? " placeholder=\"" . $this->placeholder . "\"" : "";
		if (!empty($this->class)) $html .= " class=\"" . $this->class . "\"";
		if ($this->tabindex >= 0) $html .= " tabindex=\"" . $this->tabindex . "\"";
		if ($this->maxchars != 0) {
			$html .= " onkeydown=\"if (this.value.length > $this->maxchars ) {this.value = this.value.substr(0, $this->maxchars );}\"";
			$html .= " onkeyup=\"if (this.value.length > $this->maxchars ) {this.value = this.value.substr(0, $this->maxchars );}\"";
		}
		if ($this->disabled) $html .= " disabled";
		if (@$this->required) {
			$html .= ' required="'.rawurlencode($this->default).'" caption="'.rawurlencode($this->caption).'" ';
		}
		$html .= ">";
		$html .= htmlentities($this->default,ENT_COMPAT,LANG_CHARSET);
		$html .= "</textarea>";
        if (!empty($this->description)) $html .= "<div class=\"".(bs3()?"help-block":"control-desc")."\">".$this->description."</div>";
        $html .= ($this->horizontal && bs3()) ? '</div>' : '';
		return $html;
	}

	static function form($object) {
		$form = new form();
        if (empty($object)) $object = new stdClass();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
            $object->description = "";
			$object->default = "";
			$object->placeholder = "";
			$object->rows = 5;
			$object->cols = 38;
			$object->maxchars = 0;
            $object->maxlength = 0;
            $object->is_hidden = false;
		}
        if (empty($object->description)) $object->description = "";
		$form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
		$form->register("default",gt('Default value'),  new texteditorcontrol($object->default));
		$form->register("placeholder",gt('Placeholder'), new textcontrol($object->placeholder));
		$form->register("rows",gt('Rows'), new textcontrol($object->rows,4,false,3,"integer"));
		$form->register("cols",gt('Columns'), new textcontrol($object->cols,4, false,3,"integer"));
        $form->register("maxlength",gt('Maximum Length'), new textcontrol((($object->maxlength==0)?"":$object->maxlength),4,false,3,"integer"));
        $form->register("is_hidden", gt('Make this a hidden field on initial entry'), new checkboxcontrol(!empty($object->is_hidden),false));
		if (!expJavascript::inAjaxAction())
			$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		return $form;
	}

    static function update($values, $object) {
		if ($object == null) $object = new texteditorcontrol();
		if ($values['identifier'] == "") {
			$post = expString::sanitize($_POST);
			$post['_formError'] = gt('Identifier is required.');
			expSession::set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
        $object->description = $values['description'];
        if (isset($values['default'])) $object->default = $values['default'];
		if (isset($values['placeholder'])) $object->placeholder = $values['placeholder'];
        if (isset($values['rows'])) $object->rows = intval($values['rows']);
        if (isset($values['cols'])) $object->cols = intval($values['cols']);
        if (isset($values['maxchars'])) $object->maxchars = intval($values['maxchars']);
        if (isset($values['maxlength'])) $object->maxlength = intval($values['maxlength']);
		$object->required = !empty($values['required']);
        $object->is_hidden = !empty($values['is_hidden']);
		return $object;
	}

	static function parseData($original_name,$formvalues,$for_db = false) {
		return str_replace(array("\r\n","\n","\r"),'<br />', htmlspecialchars($formvalues[$original_name]));
	}

}

?>
