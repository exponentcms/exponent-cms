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

    var $rank = 0;
    var $even = "odd";
    var $edit_class = '';
    var $edit_icon_class = '';
    var $delete_class = '';
    var $delete_icon_class = '';
    var $forms_id = null;

    /**
     * Display the form in Edit mode as HTML output.
     *
     * @param null/integer $form_id
     *
     * @return string The HTML code use to display the form to the browser.
     */
	function toHTML($forms_id=null) {
        $this->forms_id = $forms_id;
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

		$html = "<!-- Form Object '" . $this->name . "' -->\r\n";
		$html .= "<script type=\"text/javascript\" src=\"" .PATH_RELATIVE."framework/core/forms/js/inputfilters.js.php\"></script>\r\n";
        if (expJavascript::inAjaxAction()) {
            $ws_load = "webshim.setOptions({loadStyles:false,canvas:{type:'excanvas'}});webshim.polyfill('canvas forms forms-ext');";
        } else {
            $ws_load = "webshim.setOptions({canvas:{type:'excanvas'}});webshim.polyfill('canvas forms forms-ext');";
        }
        expJavascript::pushToFoot(array(
            "unique"  => 'html5forms',
            "jquery"  => 1,
            "src"     => PATH_RELATIVE . 'external/webshim-1.15.10/js-webshim/dev/polyfiller.js',
            "content" => $ws_load,
        ));
		foreach ($this->scripts as $script)
            $html .= "<script type=\"text/javascript\" src=\"$script\"></script>\r\n";
		$html .= $formError;
        $class = '';
        if ($this->horizontal) {
            if (newui()) {
                $class = " class=\"exp-skin form-horizontal\"";
            } else {
                $class = " class=\"form-horizontal\"";
            }
        } elseif (newui()) {
            $class = " class=\"exp-skin\"";
        }
		$html .= "<form role=\"form\" id='abc123' name=\"" . $this->name . "\"" . $class . " method=\"" . $this->method . "\" action=\"" . $this->action . "\" enctype=\"".$this->enctype."\">\r\n";
		foreach ($this->meta as $name=>$value)
            $html .= "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\" />\r\n";
        $this->style_form();
		foreach ($this->controlIdx as $name) {
            $html .= $this->controlToHTML($name);
            $this->rank++;
		}
        if (empty($this->controlIdx)) {
            $html .= '<div class="item empty">&#160;</div>';
        }
		$html .= "</form>\r\n";
		return $html;
	}

    function controlToHTML($name, $label = null) {
        global $router;

        $this->even = ($this->even=="odd") ? "even" : "odd";
        $html = "<div id='".$this->controls[$name]->_id."' class=\"formmoduleedit item ".$this->even." control" . (!empty($this->controls[$name]->is_hidden)?' ishidden':'') . ($this->controls[$name]->_controltype == 'pagecontrol'?' ispaged':'') . "\" >";
        if ($this->controls[$name]->horizontal&&bs3())
            $html .= '<div class="row">';

        $html .= "<div class=\"item-actions".($this->controls[$name]->horizontal&&bs3()?' col-sm-12':'')."\">";
        
        //build edit button
        if (!$this->controls[$name]->_readonly) {
            //$html .= '<a href="?module='.$module.'&action=edit_control&id='.$this->controls[$name]->_id.'&form_id='.$form_id.'">';
            $html .= '<a'.$this->edit_class.' href="'.$router->makeLink(array('controller'=>'forms','action'=>'edit_control','id'=>$this->controls[$name]->_id,'forms_id'=>$this->forms_id)).'" title="'.gt('Edit this Control').'" >';
            if (bs()) {
                $html .= $this->edit_icon_class;
            } else {
//                $html .= '<img style="border:none;" src="'.ICON_RELATIVE.'edit.png" />';
            }
            $html .= '</a>';
        } else {
            if (bs()) {
                $html .= '<div class="btn btn-default disabled ' . $this->btn_size . '">'.$this->edit_icon_class.'</div>';
            } else {
                $html .= '<img style="border:none;" src="'.ICON_RELATIVE.'edit.disabled.png" />';
            }
        }

        //build delete button
        $html .= '&#160;';
        if (!$this->controls[$name]->_readonly && $this->controls[$name]->_controltype != 'htmlcontrol' ) {
            //$html .= '<a href="?module='.'forms'.'&action=delete_control&id='.$this->controls[$name]->_id.'" onclick="return confirm(\'Are you sure you want to delete this control? All data associated with it will be removed from the database!\');">';
            $html .= '<a'.$this->delete_class.' href="'.$router->makeLink(array('controller'=>'forms','action'=>'delete_control','id'=>$this->controls[$name]->_id)).'" title="'.gt('Delete this Control').'"  onclick="return confirm(\'Are you sure you want to delete this control? All data associated with it will be removed from the database!\');">';
        } else {
            $html .= '<a'.$this->delete_class.' href="'.$router->makeLink(array('controller'=>'forms','action'=>'delete_control','id'=>$this->controls[$name]->_id)).'" title="'.gt('Delete this Control').'" onclick="return confirm(\'Are you sure you want to delete this?\');">';
        }
        if (bs()) {
            $html .= $this->delete_icon_class;
        } else {
//            $html .= '<img style="border:none;" src="'.ICON_RELATIVE.'delete.png" />';
        }
        $html .= '</a>';

        $html .= "</div>";

        if ((!empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype != 'radiogroupcontrol' && $this->controls[$name]->_controltype != 'checkboxcontrol')  // flipped non-checkbox non-radio group
              || (empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol')) {  // not flipped checkbox
             if (bs3() && $this->controls[$name]->_controltype == 'checkboxcontrol') {
                 $html .= $this->controls[$name]->toHTML($this->controlLbl[$name], $name) . "\r\n";
             } else {
                 $html .= "<label class=\"".(bs3()||bs2()?"control-label":"label").($this->horizontal&&bs3()?' col-sm-2':'')."\" style=\"background: transparent;\"></label>";
                 $html .= $this->controls[$name]->controlToHTML($name, $this->controlLbl[$name]) . "\r\n";
             }
        }
        $for   = ' for="' . $name . '"';
        if (!bs3() && (empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol')) {  // not flipped checkbox
            $html .= "<label ".$for." class=\"".(bs3()||bs2()?"control-label":"label").($this->horizontal&&bs3()?' col-sm-2':'')."\" style=\"width:auto; display:inline;\">";
            if($this->controls[$name]->required)
                $html .= '<span class="required" title="'.gt('This entry is required').'">* </span>';
            $html .= $this->controlLbl[$name];
            $html .= "</label>";
            if (!empty($this->controls[$name]->description))
                $html .= "<br><div class=\"".(bs3()?"help-block":"control-desc")."\" style=\"position:absolute;\">" . $this->controls[$name]->description . "</div>";
        }

        if ((empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol') || $this->controls[$name]->_controltype == 'pagecontrol') {
        } elseif (!empty($this->controlLbl[$name])) {  // flipped non-checkbox or page control
            if ($this->controls[$name]->_controltype == 'checkboxcontrol') {
                $html .= "<label ".$for." class=\"".(bs3()||bs2()?"control-label":"label").($this->horizontal&&bs3()?' col-sm-2':'')."\" style=\"display:inline;\">";
            } else {
                $break = $this->controls[$name]->_controltype == 'radiogroupcontrol' && $this->controls[$name]->cols != 1 ? true : false;
                $html .= "<label class=\"".(bs3()||bs2()?"control-label":"label").($this->horizontal&&bs3()?' col-sm-2':'').($break?" show":"")."\">";
            }
            if($this->controls[$name]->required)
                $html .= '<span class="required" title="'.gt('This entry is required').'">* </span>';
            $html .= $this->controlLbl[$name];
            $html .= "</label>";
        }
//           $html .= "&#160;&#160;";
        if ((!empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype == 'checkboxcontrol')) {  // flipped checkbox
            $html .= "<span style=\"display:inline-block\">".$this->controls[$name]->controlToHTML_newschool($name, $this->controlLbl[$name]) . "</span>\r\n";
            if (!empty($this->controls[$name]->description))
                $html .= "<div class=\"".(bs3()?"help-block":"control-desc")."\">" . $this->controls[$name]->description . "</div>";
        }
        if ((empty($this->controls[$name]->flip) && $this->controls[$name]->_controltype != 'checkboxcontrol')  // not fipped non-checkbox control
              || $this->controls[$name]->_controltype == 'radiogroupcontrol') {  // flipped/not flipped radio group
            $this->controls[$name]->design_time = true;
            $html .= $this->controls[$name]->controlToHTML($name, $this->controlLbl[$name]) . "\r\n";
        }
        if ($this->controls[$name]->horizontal&&bs3())
            $html .= '</div>';
        $html .= "</div>";

        return $html;
    }

    function style_form() {
        if (bs2()) {
            expCSS::pushToHead(array(
                "corecss"=>"forms-bootstrap"
            ));
            if (BTN_SIZE == 'large') {
                $this->btn_size = '';  // actually default size, NOT true bootstrap large
                $icon_size = 'icon-large';
            } elseif (BTN_SIZE == 'small') {
                $this->btn_size = 'btn-mini';
                $icon_size = '';
            } else { // medium
                $this->btn_size = 'btn-small';
                $icon_size = 'icon-large';
            }
            $this->edit_class = ' class="btn '.$this->btn_size.'"';
            $this->edit_icon_class = '<i class="icon-edit '.$icon_size.'"></i>';
            $this->delete_class = ' class="btn btn-danger '.$this->btn_size.'"';
            $this->delete_icon_class = '<i class="icon-remove-sign '.$icon_size.'"></i>';
        } elseif (bs3()) {
            expCSS::pushToHead(array(
                "corecss"=>"forms-bootstrap3"
            ));
            if (BTN_SIZE == 'large') {
                $this->btn_size = 'btn-lg';
                $icon_size = 'fa-lg';
            } elseif (BTN_SIZE == 'small') {
                $this->btn_size = 'btn-sm';
                $icon_size = '';
            } elseif (BTN_SIZE == 'extrasmall') {
                $this->btn_size = 'btn-xs';
                $icon_size = '';
            } else {
                $this->btn_size = '';
                $icon_size = 'fa-lg';
            }
            $this->edit_class = ' class="btn btn-default '.$this->btn_size.' edit"';
            $this->edit_icon_class = '<i class="fa fa-pencil-square-o '.$icon_size.'"></i>';
            $this->delete_class = ' class="btn btn-danger '.$this->btn_size.' delete"';
            $this->delete_icon_class = '<i class="fa fa-times-circle '.$icon_size.'"></i>';
        } else {
            $this->edit_class = ' class="edit"';
            $this->delete_class = ' class="delete"';
            expCSS::pushToHead(array(
                "corecss"=>"forms"
            ));
        }
    }
    
}

?>
