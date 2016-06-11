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
 * Form Class for building and displaying a form
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
    var $id = null;
//    var $tabs       = array();
//    var $is_tabbed  = false;
    var $div_to_update = null;
    var $is_paged  = 0;

	var $validationScript = "";

	function ajaxUpdater($module=null, $ajax_action=null, $div_to_update=null) {
		if ( ($ajax_action != null) && ($module != null) ) {
			$this->ajax_updater = 1;
			$this->meta('action',$ajax_action);
			$this->meta('controller',$module);
			$this->meta('ajax_action', '1');
		}

		if ($div_to_update != null) {
			$this->div_to_update = $div_to_update;
		}
	}
	
	function secure() {
//		$this->action = (ENABLE_SSL ? SSL_URL : '') . SCRIPT_RELATIVE . SCRIPT_FILENAME;
        $this->action = SCRIPT_RELATIVE . SCRIPT_FILENAME;
		$this->meta("expid",session_id());
	}

    /**
     * Registers a new Control with the form.  This function will simply append the new Control to the end of the Form.
     *
     * @param string       $name    The internal name of the control.  This is used for referring to the control later.  If this is a null string, the Control will not be registered, and this function will return false.
     * @param string       $label
     * @param \formcontrol $control The Control object to register with the form.
     * @param bool         $replace boolean dictating what to do if a Control with the specified internal name already exists on the form.  If passed as true (default), the existing Control will be replaced.  Otherwise, the Control registration will fail and return false.
     * @param array        $params
     *
     * @return boolean Returns true if the new Control was registered.
     */
	function register($name, $label, $control, $replace=true, $params=null) {
		if ($name == null || $name == "")
			$name = uniqid("");
		if (isset($this->controls[$name])) {
			if (!$replace) return false;
		} else {
			$this->controlIdx[] = $name;
		}

        if ($this->horizontal)
			$control->horizontal = true;
		if (!empty($params)) {
			foreach ($params as $name => $value) {
				$control->$name = $value;
			}
		}
		$this->controls[$name] = $control;
		$this->controlLbl[$name] = $label;
        if (method_exists($control,'onRegister'))
			$control->onRegister($this);
		return true;
	}

	/**
	 * Unregisters a previously registered Control.
	 *
	 * @param string $name The internal name of the control to remove from the Form.
	 *
	 * @return boolean Returns true if the Control was unregistered.
	 */
	function unregister($name) {
		if (in_array($name,$this->controlIdx)) {
			$control = $this->controls[$name];
			unset(
                $this->controls[$name],
                $this->controlLbl[$name]
            );

			$tmp = array_flip($this->controlIdx);
			unset($tmp[$name]);

			// Regenerate indices
			$this->controlIdx = array();
			foreach ($tmp as $name=>$rank) {
				$this->controlIdx[] = $name;
			}
            if (method_exists($control,'onUnRegister'))
				$control->onUnregister($this);
		}
		return true;
	}

    /**
     * Registers a new Control, placing it after a pre-existing named Control.  If the Control that the caller wants to insert after does not exist, the new Control is appended to the end of the Form.
     *
     * @param string $afterName The internal name of the Control to register the new Control after.
     * @param string $name The internal name of the new Control.
     * @param string $label
     * @param object $control The Control object to register with the Form.
     *
     * @param array  $params
     * @return boolean Returns true if the new Control was registered.
     */
	function registerAfter($afterName, $name, $label, $control, $params=null) {
		if ($name == null || $name == "")
			$name = uniqid("");
		if (in_array($name,$this->controlIdx))
			return false;
		
        if ($this->horizontal)
			$control->horizontal = true;
		if (!empty($params)) {
			foreach ($params as $name => $value) {
				$control->$name = $value;
			}
		}
		$this->controls[$name] = $control;
		$this->controlLbl[$name] = str_replace(" ","&#160;",$label);
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
     * @param string $beforeName The internal name of the Control to register the new Control before.
     * @param string $name The internal name of the new Control.
     * @param string $label
     * @param object $control the Control object to register with the Form.
     *
     * @param array  $params
     * @return boolean Returns true if the new Control was registered.
     */
	function registerBefore($beforeName, $name, $label, $control, $params=null) {
		if ($name == null || $name == "")
			$name = uniqid("");
		if (in_array($name,$this->controlIdx))
			return false;
		
        if ($this->horizontal)
			$control->horizontal = true;
		if (!empty($params)) {
			foreach ($params as $name => $value) {
				$control->$name = $value;
			}
		}
		$this->controls[$name] = $control;
		$this->controlLbl[$name] = str_replace(" ","&#160;",$label);

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
     * @param null $form_id
     *
     * @return string The HTML code use to display the form to the browser.
     */
	function toHTML($form_id = null) {
		// Form validation script
		if ($this->validationScript != "") {
			$this->scripts[] = $this->validationScript;
            if (empty($this->controls["submit"])) $this->controls["submit"] = new stdClass();
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
//		$html .= '<script type="text/javascript" src="'.PATH_RELATIVE.'framework/core/forms/js/required.js"></script>'."\r\n";
		$html .= "<script type=\"text/javascript\" src=\"" .PATH_RELATIVE."framework/core/forms/js/inputfilters.js.php\"></script>\r\n";
        if (bs2()) {
            expCSS::pushToHead(array(
                "corecss"=>"forms-bootstrap"
            ));
            $btn_class = 'btn btn-default';
            if (BTN_SIZE == 'large') {
                $btn_size = '';  // actually default size, NOT true bootstrap large
            } elseif (BTN_SIZE == 'small') {
                $btn_size = 'btn-mini';
            } else { // medium
                $btn_size = 'btn-small';
            }
            $btn_class .= ' ' . $btn_size;
        } elseif (bs3()) {
            expCSS::pushToHead(array(
                "corecss"=>"forms-bootstrap3"
            ));
            $btn_class = 'btn btn-default';
            if (BTN_SIZE == 'large') {
                $btn_size = 'btn-lg';
            } elseif (BTN_SIZE == 'small') {
                $btn_size = 'btn-sm';
			} elseif (BTN_SIZE == 'extrasmall') {
		       $btn_size = 'btn-xs';
            } else { // medium
                $btn_size = '';
            }
            $btn_class .= ' ' . $btn_size;
        } else {
            expCSS::pushToHead(array(
                "corecss"=>"forms"
            ));
            $btn_class = 'awesome ".BTN_SIZE." ".BTN_COLOR."';
        };
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
		foreach ($this->scripts as $script) $html .= "<script type=\"text/javascript\" src=\"".$script."\"></script>\r\n";
		$html .= '<div class="error">'.$formError.'</div>';
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
		if (isset($this->ajax_updater)) {
			$html .= "<form role=\"form\" name=\"" . $this->name . "\" method=\"" ;
			$html .= $this->method . "\" action=\"" . $this->action ."\" ";
			$html .= " onsubmit=\"new Ajax.Updater('".$this->div_to_update."', '".$this->action."', ";
			$html .= "{asynchronous:true, parameters:Form.serialize(this)}); return false;\">\r\n";
		} else {
			$html .= "<form role=\"form\" id='".$this->id."' name=\"" . $this->name . "\"" . $class . " method=\"" . $this->method . "\" action=\"" . $this->action . "\" enctype=\"".$this->enctype."\">\r\n";
		}
		//$html .= "<form name=\"" . $this->name . "\" method=\"" . $this->method . "\" action=\"" . $this->action . "\" enctype=\"".$this->enctype."\">\r\n";
		foreach ($this->meta as $name=>$value) $html .= "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\" />\r\n";
//		$html .= "<div class=\"form_wrapper\">\r\n";

//        $oldname = 'oldname';
//        $save = '';
        $rank = 0;
		foreach ($this->controlIdx as $name) {
            if ($rank && get_class($this->controls[$name]) == 'pagecontrol') {
                $html .= '</fieldset>';
            }
            if ($rank == 0) $this->controls[$name]->focus = true;  // first control gets the focus
            $html .= $this->controls[$name]->toHTML($this->controlLbl[$name],$name) . "\r\n";
            $rank ++;
		}

        if ($this->is_paged) $html .= '</fieldset>';
//		$html .= "</div>\r\n";
//        $html .= $save;
		$html .= "</form>\r\n";
        if ($this->is_paged) {
            $content = "
                $('#" . $this->id . "').stepy({
                    validate: true,
                    block: true,
                    errorImage: true,
                //    description: false,
                //    legend: false,
                    btnClass: '" . $btn_class . "',
                    titleClick: true,";
            if (bs3()) {
                $content .= "
                    validateOptions: {
						rules: {
							'hiddenRecaptcha': {
								required: function() {
									if(grecaptcha.getResponse() == '') {
										return true;
									} else {
										return false;
									}
								}
							}
						},
                        highlight: function(element) {
//                            $('.stepy-header .stepy-active').addClass('stepy-error');
                            $(element).closest('.control').removeClass('has-success').addClass('has-error');
//	 						$(element).closest('.form-group').find('i.fa').remove();
//							$(element).closest('.form-group').append('<i class=\"fa fa-exclamation fa-lg form-control-feedback\"></i>');
//                           var id_attr = '#' + $( element ).attr('id') + '1';
//                           $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');  // requires a feedback <span> and has-feedback class to control
                        },
                        unhighlight: function(element) {
//							$('.stepy-header .stepy-error').removeClass('stepy-error');
                            $(element).closest('.control').removeClass('has-error').addClass('has-success');
//							$(element).closest('.form-group').find('i.fa').remove();
//							$(element).closest('.form-group').append('<i class=\"fa fa-check fa-lg form-control-feedback\"></i>');
//                           var id_attr = '#' + $( element ).attr('id') + '1';
//                           $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');  // requires a feedback <span> and has-feedback class to control
                        },
                        errorElement: 'span',
                        errorClass: '".(bs3()?"help-block":"control-desc")."',
                        errorPlacement: function(error, element) {
                            if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent().parent());
                            } else if(element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else {
                                error.insertAfter(element);
                            }
                        }
                    }";
            }
            $content .= "
                });
            ";
            expJavascript::pushToFoot(
                array(
                    "unique"  => 'stepy-' . $this->id,
                    "jquery"  => 'jquery.validate,jquery.stepy',
                    "content" => $content,
                )
            );
        } else {
            if (bs3()) {
                $content = "
                    $('#" . $this->id . "').validate({
						rules: {
							'hiddenRecaptcha': {
								required: function() {
									if(grecaptcha.getResponse() == '') {
										return true;
									} else {
										return false;
									}
								}
							}
						},
                        highlight: function(element) {
                            $(element).closest('.control').removeClass('has-success').addClass('has-error');
//	 						$(element).closest('.form-group').find('i.fa').remove();
//							$(element).closest('.form-group').append('<i class=\"fa fa-exclamation fa-lg form-control-feedback\"></i>');
//                            var id_attr = '#' + $( element ).attr('id') + '1';
//                            $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');  // requires a feedback <span> and has-feedback class to control
                        },
                        unhighlight: function(element) {
                            $(element).closest('.control').removeClass('has-error').addClass('has-success');
//							$(element).closest('.form-group').find('i.fa').remove();
//							$(element).closest('.form-group').append('<i class=\"fa fa-check fa-lg form-control-feedback\"></i>');
//                            var id_attr = '#' + $( element ).attr('id') + '1';
//                            $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');  // requires a feedback <span> and has-feedback class to control
                        },
                        errorElement: 'span',
                        errorClass: '".(bs3()?"help-block":"control-desc")."',
                        errorPlacement: function(error, element) {
                            if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent().parent());
                            } else if(element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else {
                                error.insertAfter(element);
                            }
                        }
                     });
                ";
            } else {
                $content = "
                    $('#" . $this->id . "').validate({
						rules: {
							'hiddenRecaptcha': {
								required: function() {
									if(grecaptcha.getResponse() == '') {
										return true;
									} else {
										return false;
									}
								}
							}
						},
                    });
                ";
            }
            expJavascript::pushToFoot(array(
                "unique"  => 'formvalidate-' . $this->id,
                "jquery"  => 'jquery.validate',
                "content" => $content,
            ));
        }
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
