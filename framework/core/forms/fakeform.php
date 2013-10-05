<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

/**
 * Fake Form Class for displaying a wysiwyg form designer
 *
 * An HTML-form building class, that supports
 * registerable and unregisterable controls.
 *
 * @package Subsystems-Forms
 * @subpackage Form
 */
class fakeform extends form {

	function toHTML($forms_id=null, $module=null) {
        if (empty($module)) $module="forms";
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
			}

			$formError = @$last_POST['_formError'];

			//expSession::un_set("last_POST");
		}

		global $router, $head_config;
		$html = "<!-- Form Object '" . $this->name . "' -->\r\n";
		$html .= "<script type=\"text/javascript\" src=\"" .PATH_RELATIVE."framework/core/forms/js/inputfilters.js.php\"></script>\r\n";
        expJavascript::pushToFoot(array(
            "unique"  => 'html5forms1',
            "src"=> PATH_RELATIVE . 'external/html5forms/modernizr-262.js',
        ));
        expJavascript::pushToFoot(array(
            "unique"  => 'html5forms2',
            "src"=> PATH_RELATIVE . 'external/html5forms/EventHelpers.js',
        ));
        expJavascript::pushToFoot(array(
            "unique"  => 'html5forms3',
            "src"=> PATH_RELATIVE . 'external/html5forms/webforms2/webforms2_src.js',
        ));
        expJavascript::pushToFoot(array(
            "unique"  => 'html5forms4',
//            "jquery"=> 'jqueryui,jquery.placeholder,colorpicker',
            "jquery"=> 'jqueryui,jquery.placeholder',
            "src"=> PATH_RELATIVE . 'external/html5forms/html5forms.fallback.js',
        ));
		foreach ($this->scripts as $script) $html .= "<script type=\"text/javascript\" src=\"$script\"></script>\r\n";
		$html .= $formError;
		$html .= "<form id='abc123' name=\"" . $this->name . "\" method=\"" . $this->method . "\" action=\"" . $this->action . "\" enctype=\"".$this->enctype."\">\r\n";
		foreach ($this->meta as $name=>$value) $html .= "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\" />\r\n";
		$rank = 0;
		$even = "odd";
        if (BTN_SIZE == 'large') {
            $btn_size = 'btn-small';
            $icon_size = 'icon-large';
        } else {
            $btn_size = 'btn-mini';
            $icon_size = '';
        }
        $edit_class = '';
        $delete_class = '';
        if ($head_config['framework'] == 'bootstrap') {
            $edit_class = ' class="btn '.$btn_size.' icon-edit'.$icon_size.'"';
            $delete_class = ' class="btn btn-danger '.$btn_size.' icon-remove-sign'.$icon_size.'"';
        }
		foreach ($this->controlIdx as $name) {
			$even = ($even=="odd") ? "even" : "odd";
			$html .= "<div class=\"formmoduleedit ".$even." control\" style=\"border: 1px dashed lightgrey; padding: 1em;\" >";
            $html .= "<div class=\"item-actions\">";
			if (!$this->controls[$name]->_readonly) {
				//$html .= '<a href="?module='.$module.'&action=edit_control&id='.$this->controls[$name]->_id.'&form_id='.$form_id.'">';
				$html .= '<a'.$edit_class.' href="'.$router->makeLink(array('controller'=>$module,'action'=>'edit_control','id'=>$this->controls[$name]->_id,'forms_id'=>$forms_id)).'" title="'.gt('Edit this Control').'" >';
                if (!$head_config['framework'] == 'bootstrap') $html .= '<img style="border:none;" src="'.ICON_RELATIVE.'edit.png" />';
				$html .= '</a>';
			} else {
                if (!$head_config['framework'] == 'bootstrap') {
                    $html .= '<img style="border:none;" src="'.ICON_RELATIVE.'edit.disabled.png" />';
                } else {
                    $html .= '<div class="btn disabled '.$btn_size.' icon-edit'.$icon_size.'"> </div>';
                }
			}

			$html .= '&#160;';
			if (!$this->controls[$name]->_readonly && $this->controls[$name]->_controltype != 'htmlcontrol' ) {
				//$html .= '<a href="?module='.$module.'&action=delete_control&id='.$this->controls[$name]->_id.'" onclick="return confirm(\'Are you sure you want to delete this control? All data associated with it will be removed from the database!\');">';
				$html .= '<a'.$delete_class.' href="'.$router->makeLink(array('controller'=>$module,'action'=>'delete_control','id'=>$this->controls[$name]->_id)).'" title="'.gt('Delete this Control').'"  onclick="return confirm(\'Are you sure you want to delete this control? All data associated with it will be removed from the database!\');">';
			}
			else {
				$html .= '<a'.$delete_class.' href="'.$router->makeLink(array('controller'=>$module,'action'=>'delete_control','id'=>$this->controls[$name]->_id)).'" title="'.gt('Delete this Control').'" onclick="return confirm(\'Are you sure you want to delete this?\');">';
			}
            if (!$head_config['framework'] == 'bootstrap') $html .= '<img style="border:none;" src="'.ICON_RELATIVE.'delete.png" />';
			$html .= '</a>';
            $html .= "</div>";
            if ((!empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype != 'radiogroupcontrol' && $this->controls[$name]->_controltype != 'checkboxcontrol') || (empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol')) {
                $html .= "<label class=\"label\" style=\"background: transparent;\"></label>";
                $html .= $this->controls[$name]->controlToHTML($name, $this->controlLbl[$name]) . "\r\n";
            }
            $for   = ' for="' . $name . '"';
            if ((empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol')) {
                $html .= "<label ".$for." class=\"label\" style=\"width:auto; display:inline;\">";
                if($this->controls[$name]->required) $html .= '<span class="required" title="'.gt('This entry is required').'">* </span>';
                $html .= $this->controlLbl[$name];
                $html .= "</label>";
                if (!empty($this->controls[$name]->description)) $html .= "<br><div class=\"control-desc\" style=\"position:absolute;\">" . $this->controls[$name]->description . "</div>";
            }

            if ((empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol') || $this->controls[$name]->_controltype == 'pagecontrol') {
            } elseif (!empty($this->controlLbl[$name])) {
                if ($this->controls[$name]->_controltype == 'checkboxcontrol') {
                    $html .= "<label ".$for." class=\"label\" style=\"display:inline;\">";
                } else {
                    $html .= "<label class=\"label\">";
                }
                if($this->controls[$name]->required) $html .= '<span class="required" title="'.gt('This entry is required').'">* </span>';
                $html .= $this->controlLbl[$name];
                $html .= "</label>";
            }
//			$html .= "<div class=\"formmoduleeditactions\">";
//			if ($rank != count($this->controlIdx)-1) {
//				//$html .= '<a href="?module='.$module.'&action=order_controls&p='.$form_id.'&a='.$rank.'&b='.($rank+1).'">';
//				$html .= '<a href="'.$router->makeLink(array('module'=>$module, 'action'=>'order_controls', 'p'=>$form_id, 'a'=>$rank, 'b'=>($rank+1))).'">';
//				$html .= "<img border='0' src='".ICON_RELATIVE."down.png' />";
//				$html .= '</a>';
//			} else {
//				$html .= "<img src='".ICON_RELATIVE."down.disabled.png' />";
//			}
//			$html .= "&#160;";
//			if ($rank != 0) {
//				//$html .= '<a href="?module='.$module.'&action=order_controls&p='.$form_id.'&a='.$rank.'&b='.($rank-1).'">';
//				$html .= '<a href="'.$router->makeLink(array('module'=>$module, 'action'=>'order_controls', 'p'=>$form_id, 'a'=>$rank, 'b'=>($rank-1))).'">';
//				$html .= "<img border='0' src='".ICON_RELATIVE."up.png' />";
//				$html .= '</a>';
//			} else {
//				$html .= "<img src='".ICON_RELATIVE."up.disabled.png' />";
//			}
//
            $html .= "&#160;&#160;";
            if ((!empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol')) {
                $html .= "<span style=\"display:inline-block\">".$this->controls[$name]->controlToHTML_newschool($name, $this->controlLbl[$name]) . "</span>\r\n";
                if (!empty($this->controls[$name]->description)) $html .= "<div class=\"control-desc\">" . $this->controls[$name]->description . "</div>";
            }
            if ((empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype != 'checkboxcontrol') || $this->controls[$name]->_controltype == 'radiogroupcontrol') {
                $html .= $this->controls[$name]->controlToHTML($name, $this->controlLbl[$name]) . "\r\n";
            }
			$html .= "</div>";
			
			$rank++;
		}
	//	$html .= "<tr><td width='5%'></td><td wdith='90%'><td></td width='5%'></tr>\r\n";
	//	$html .= "</table>\r\n";
		$html .= "</form>\r\n";
		return $html;
	}
}

?>
