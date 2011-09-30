<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Form Class
 *
 * An HTML-form building class, that supports
 * registerable and unregisterable controls.
 *
 * @package Subsystems-Forms
 * @subpackage Form
 */
class form extends baseform {

	var $controls   = array();
	var $controlIdx = array();
	var $controlLbl = array();
	
	var $validationScript = "";

	function ajaxUpdater($module=null, $ajax_action=null, $div_to_update=null) {
		if ( ($ajax_action != null) && ($module != null) ) {
			$this->ajax_updater = 1;
			$this->meta('action',$ajax_action);
			$this->meta('module',$module);
			$this->meta('ajax_action', '1');
		}

		if ($div_to_update != null) {
			$this->div_to_update = $div_to_update;
		}
	}
	
	function secure() {
		$this->action = (ENABLE_SSL ? SSL_URL : '') . SCRIPT_RELATIVE . SCRIPT_FILENAME;
		$this->meta("expid",session_id());
	}

	/**
	 * Registers a new Control with the form.  This function will simply append the new Control to the end of the Form.
	 *
	 * @param $name The internal name of the control.  This is used for referring to the control later.  If this is a null string, the Control will not be registered, and this function will return false.
	 * @param $label
	 * @param $control The Control object to register with the form.
	 * @param \A|bool $replace boolean dictating what to do if a Control with the specified internal name already exists on the form.  If passed as true (default), the existing Control will be replaced.  Otherwise, the Control registration will fail and return false.
	 *
	 * @return boolean Returns true if the new Control was registered.
	 */
	function register($name,$label, $control,$replace=true) {
		if ($name == null || $name == "") $name = uniqid("");
		if (isset($this->controls[$name])) {
			if (!$replace) return false;
		} else $this->controlIdx[] = $name;
		$this->controls[$name] = $control;
		$this->controlLbl[$name] = $label;
		$control->onRegister($this);
		return true;
	}

	/**
	 * Unregisters a previously registered Control.
	 *
	 * @param $name The internal name of the control to remove from the Form.
	 *
	 * @return boolean Returns true if the Control was unregistered.
	 */
	function unregister($name) {
		if (in_array($name,$this->controlIdx)) {
			$control = $this->controls[$name];
			unset($this->controls[$name]);
			unset($this->controlLbl[$name]);
			
			$tmp = array_flip($this->controlIdx);
			unset($tmp[$name]);

			// Regenerate indices
			$this->controlIdx = array();
			foreach ($tmp as $name=>$rank) {
				$this->controlIdx[] = $name;
			}
			$control->onUnregister($this);
		}
		return true;
	}

	/**
	 * Registers a new Control, placing it after a pre-existing named Control.  If the Control that the caller wants to insert after does not exist, the new Control is appended to the end of the Form.
	 *
	 * @param $afterName The internal name of the Control to register the new Control after.
	 * @param $name The internal name of the new Control.
	 * @param $label
	 * @param $control The Control object to register with the Form.
	 *
	 * @return boolean Returns true if the new Control was registered.
	 */
	function registerAfter($afterName,$name,$label, $control) {
		if ($name == null || $name == "") $name = uniqid("");
		if (in_array($name,$this->controlIdx)) return false;
		
		$this->controls[$name] = $control;
		$this->controlLbl[$name] = str_replace(" ","&nbsp;",$label);
		if (!in_array($afterName,$this->controlIdx)) {
			$this->controlIdx[] = $name;
			$control->onRegister($this);
			return true;
		} else {
			$tmp = array_flip($this->controlIdx);
			$idx = $tmp[$afterName]+1;
			array_splice($this->controlIdx,$idx,0,$name);
			$control->onRegister($this);
			return true;
		}
	}

	/**
	 * Registers a new Control, placing it before a pre-existing named Control.  If the Control that the caller wants to insert the new Control before does not exist, the new Control is prepended to the form.
	 *
	 * @param $beforeName The internal name of the Control to register the new Control before.
	 * @param $name The internal name of the new Control.
	 * @param $label
	 * @param $control the Control object to register with the Form.
	 *
	 * @return boolean Returns true if the new Control was registered.
	 */
	function registerBefore($beforeName,$name,$label, $control) {
		if ($name == null || $name == "") $name = uniqid("");
		if (in_array($name,$this->controlIdx)) return false;
		
		$this->controls[$name] = $control;
		$this->controlLbl[$name] = str_replace(" ","&nbsp;",$label);
		if (!in_array($beforeName,$this->controlIdx)) {
			$this->controlIdx[] = $name;
			$control->onRegister($this);
			return true;
		} else {
			$tmp = array_flip($this->controlIdx);
			$idx = $tmp[$beforeName];
			array_splice($this->controlIdx,$idx,0,$name);
			$control->onRegister($this);
			return true;
		}
	}

	/**
	 * Convert the form to HTML output.
	 *
	 * @return The HTML code use to display the form to the browser.
	 */
	function toHTML() {
		// Form validation script
		if ($this->validationScript != "") {
			$this->scripts[] = $this->validationScript;
			$this->controls["submit"]->validateJS = "validate(this.form)";
		}
	
		// Persistent Form Data extension
		$formError = "";
		if (expSession::is_set("last_POST")) {
			// We have cached POST data.  Use it to update defaults.
			$last_POST = expSession::get("last_POST");
			
			foreach (array_keys($this->controls) as $name) {
				// may need to look to control a la parseData
				$this->controls[$name]->default = @$last_POST[$name];
				$this->controls[$name]->inError = 1; // Status flag for controls that need to do some funky stuff.
			}
			
			$formError = @$last_POST['_formError'];
			
			//expSession::un_set("last_POST");
		}
		
		$html = "<!-- Form Object '" . $this->name . "' -->\r\n";
		$html .= '<script type="text/javascript" src="'.PATH_RELATIVE.'framework/core/subsystems/forms/js/required.js"></script>'."\r\n";
		$html .= "<script type=\"text/javascript\" src=\"" .PATH_RELATIVE."framework/core/subsystems/forms/js/inputfilters.js.php\"></script>\r\n";
		foreach ($this->scripts as $name=>$script) $html .= "<script type=\"text/javascript\" src=\"$script\"></script>\r\n";
		$html .= '<div class="error">'.$formError.'</div>';
		if (isset($this->ajax_updater)) {
			$html .= "<form name=\"" . $this->name . "\" method=\"" ;
			$html .= $this->method . "\" action=\"" . $this->action ."\" ";
			$html .= " onsubmit=\"new Ajax.Updater('".$this->div_to_update."', '".$this->action."', ";
			$html .= "{asynchronous:true, parameters:Form.serialize(this)}); return false;\">\r\n";
		} else {
			$html .= "<form name=\"" . $this->name . "\" method=\"" . $this->method . "\" action=\"" . $this->action . "\" enctype=\"".$this->enctype."\">\r\n";
		}
		//$html .= "<form name=\"" . $this->name . "\" method=\"" . $this->method . "\" action=\"" . $this->action . "\" enctype=\"".$this->enctype."\">\r\n";
		foreach ($this->meta as $name=>$value) $html .= "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\" />\r\n";
		$html .= "<div class=\"form_wrapper\">\r\n";
		foreach ($this->controlIdx as $name) {
			$html .= $this->controls[$name]->toHTML($this->controlLbl[$name],$name) . "\r\n";
		}
		$html .= "</div>\r\n";
		$html .= "</form>\r\n";
		return $html;
	}
	
	/*
	function mergeFormBefore($before_name,$form) {
		
	}
	
	function mergeFormAfter($after_name,$form) {
	
	}
	*/
}

?>
