<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

/**
 * Smarty {form} block plugin
 *
 * Type:     block<br>
 * Name:     form<br>
 * Purpose:  Set up a form block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 *
 * @package    Smarty-Plugins
 * @subpackage Block
 */
function smarty_block_form($params,$content,&$smarty, &$repeat) {
	if(empty($content)){
		$name = isset($params['name']) ? $params['name'] : 'form';
		$id = empty($params['id']) ? $name : $params['id'];
		$module = isset($params['module']) ? $params['module'] : $smarty->getTemplateVars('__loc')->mod;
		$controller = isset($params['controller']) ? $params['controller'] : $smarty->getTemplateVars('__loc')->mod;
		$method = isset($params['method']) ? $params['method'] : "POST";
		$enctype = isset($params['enctype']) ? $params['enctype'] : 'multipart/form-data';

		echo "<!-- Form Object 'form' -->\r\n";
		echo '<script type="text/javascript" src="',PATH_RELATIVE,'framework/core/forms/js/inputfilters.js.php"></script>',"\r\n";
		// echo '<script type="text/javascript" src="'.PATH_RELATIVE.'framework/core/forms/controls/listbuildercontrol.js"></script>'."\r\n";
		// echo '<script type="text/javascript" src="'.PATH_RELATIVE.'framework/core/forms/js/required.js"></script>'."\r\n";
		// echo '<script type="text/javascript" src="'.PATH_RELATIVE.'js/PopupDateTimeControl.js"></script>'."\r\n";

//        if (!NEWUI) {
            if (bs2()) {
                expCSS::pushToHead(array(
                    "corecss"=>"forms-bootstrap"
                ));
                $btn_class = 'btn btn-default';
                if (BTN_SIZE === 'large') {
                    $btn_size = '';  // actually default size, NOT true bootstrap large
                } elseif (BTN_SIZE === 'small') {
                    $btn_size = 'btn-mini';
                } else { // medium
                    $btn_size = 'btn-small';
                }
                $btn_class .= ' ' . $btn_size;
            } elseif (bs()) {
                if (bs3()) {
                    expCSS::pushToHead(array(
                        "corecss" => "forms-bootstrap3"
                    ));
                } elseif ( bs4()) {
                    expCSS::pushToHead(array(
                        "corecss" => "forms-bootstrap4"
                    ));
                } else {
                    expCSS::pushToHead(array(
                        "corecss" => "forms-bootstrap5"
                    ));
                }
                if (bs3()) {
                    $btn_class = 'btn btn-default';
                } else {
                    $btn_class = 'btn btn-secondary';
                }
                if (BTN_SIZE === 'large') {
                    $btn_size = 'btn-lg';
                } elseif (BTN_SIZE === 'small') {
                    $btn_size = 'btn-sm';
                } elseif (BTN_SIZE === 'extrasmall') {
                    if (bs3()) {
                        $btn_size = 'btn-xs';
                    } else {
                        $btn_size = 'btn-sm';
                    }
                } else { // medium
                    $btn_size = '';
                }
                $btn_class .= ' ' . $btn_size;
            } else {
                expCSS::pushToHead(array(
                    "corecss"=>"forms"
                ));
                $btn_class = 'awesome ".BTN_SIZE." ".BTN_COLOR."';
            }
//        }
        if (newui()) {
            $newui_class = ' exp-skin';
        } else {
            $newui_class = '';
        }
        if (OLD_BROWSER_SUPPORT) {
            if (expJavascript::inAjaxAction()) {
                $ws_load = "webshim.setOptions({loadStyles:false,canvas:{type:'excanvas'}});webshim.polyfill('canvas forms forms-ext');";
            } else {
                $ws_load = "webshim.setOptions({canvas:{type:'excanvas'}});webshim.polyfill('canvas forms forms-ext');";
            }
            expJavascript::pushToFoot(array(
                "unique" => 'html5forms',
                "jquery" => 1,
                "src" => PATH_RELATIVE . 'external/webshim-1.16.0/js-webshim/dev/polyfiller.js',
                "content" => $ws_load,
            ));
        }
        if (!empty($params['paged'])) {
            if (empty($params['name']) && empty($params['id'])) die("<strong style='color:red'>" . gt(
                    "The 'name' or 'id parameter is required for the paged {form} plugin."
                ) . "</strong>");
            $content = "
                $('#" . $id . "').stepy({
                    validate: true,
                    block: true,
                    errorImage: true,
                //    description: false,
                //    legend: false,
                    btnClass: '" . $btn_class . "',
                    titleClick: true,";
            if (bs5()) {
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
                        highlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to invalid
//                            $(element).parent().find('label').removeClass('is-valid').addClass('is-invalid');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to valid
//                            $(element).parent().find('label').removeClass('is-invalid').addClass('is-valid');
                        },
                        errorElement: 'small',
                        errorClass: 'form-text invalid-feedback',
                        errorPlacement: function(error, element) {
                            if (element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                                error.insertAfter(element.parent().parent());
                            } else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent());
                            } else {
                                error.insertAfter(element);
                            }
                        }
                    }";
            } elseif (bs4()) {
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
                        highlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to invalid
//                          $(element).parent().find('label').removeClass('valid-feedback').addClass('invalid-feedback');
//	 						$(element).closest('.form-group').find('i.fa.valid-feedback').remove();
//	 						$(element).closest('.form-group').find('i.fa.invalid-feedback').remove();
//							$(element).closest('.form-group').append('<i class=\"fas fa-exclamation fa-lg invalid-feedback\"></i>');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to valid
//                          $(element).parent().find('label').removeClass('invalid-feedback').addClass('valid-feedback');
//							$(element).closest('.form-group').find('i.fa.invalid-feedback').remove();
//							$(element).closest('.form-group').find('i.fa.valid-feedback').remove();
//							$(element).closest('.form-group').append('<i class=\"fas fa-check fa-lg valid-feedback\"></i>');
                        },
                        errorElement: 'small',
                        errorClass: 'form-text invalid-feedback',
                        errorPlacement: function(error, element) {
                            if (element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                                error.insertAfter(element.parent().parent());
                            } else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent());
                            } else {
                                error.insertAfter(element);
                            }
                        }
                    }";
            } elseif (bs3()) {
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
                        highlight: function(element, errorClass, validClass) {
                            $(element).closest('.form-group').removeClass('has-success').addClass('has-error').addClass('has-feedback');
                            $(element).closest('.form-group').find('i.fa.form-control-feedback').remove();
                            $(element).closest('.form-group').append('<i class=\"fa fa-exclamation fa-lg form-control-feedback\"></i>');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            $(element).closest('.form-group').removeClass('has-error').addClass('has-success').addClass('has-feedback');
                            $(element).closest('.form-group').find('i.fa.form-control-feedback').remove();
                            $(element).closest('.form-group').append('<i class=\"fa fa-check fa-lg form-control-feedback\"></i>');
                        },
                        errorElement: 'span',
                        errorClass: 'help-block invalid-feedback',
                        errorPlacement: function(error, element) {
                            if (element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                                error.insertAfter(element.parent().parent());
                            } else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent().parent());
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
                    "unique" => 'stepy-' . $id,
                    "jquery" => 'jquery.validate,jquery.stepy',
                    "content" => $content,
                )
            );
        } else {
            if (bs5()) {
                $content = "
                    $('#" . $id . "').validate({
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
                        highlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to invalid
//                            $(element).parent().find('label').removeClass('is-valid').addClass('is-invalid');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to valid
//                            $(element).parent().find('label').removeClass('is-invalid').addClass('is-valid');
                        },
                        errorElement: 'small',
                        errorClass: 'form-text invalid-feedback',
                        errorPlacement: function(error, element) {
                            if (element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                                error.insertAfter(element.parent().parent());
                            } else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent());
                            } else {
                                error.insertAfter(element);
                            }
                        }
                     });
                ";
            } elseif (bs4()) {
                $content = "
                    $('#" . $id . "').validate({
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
                        highlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to invalid
//                          $(element).parent().find('label').removeClass('valid-feedback').addClass('invalid-feedback');
//	 						$(element).closest('.form-group').find('i.fa.valid-feedback').remove();
//	 						$(element).closest('.form-group').find('i.fa.invalid-feedback').remove();
//							$(element).closest('.form-group').append('<i class=\"fas fa-exclamation fa-lg invalid-feedback\"></i>');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            // mark form as validated
                            $(element).closest('form').addClass('was-validated');
                            // move backward to label and set to valid
//                          $(element).parent().find('label').removeClass('invalid-feedback').addClass('valid-feedback');
//							$(element).closest('.form-group').find('i.fa.invalid-feedback').remove();
//							$(element).closest('.form-group').find('i.fa.valid-feedback').remove();
//							$(element).closest('.form-group').append('<i class=\"fas fa-check fa-lg valid-feedback\"></i>');
                        },
                        errorElement: 'small',
                        errorClass: 'form-text invalid-feedback',
                        errorPlacement: function(error, element) {
                            if (element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                                error.insertAfter(element.parent().parent());
                            } else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent());
                            } else {
                                error.insertAfter(element);
                            }
                        }
                     });
                ";
            } elseif (bs3()) {
                $content = "
                    $('#" . $id . "').validate({
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
                        highlight: function(element, errorClass, validClass) {
                            $(element).closest('.form-group').removeClass('has-success').addClass('has-error').addClass('has-feedback');
                            $(element).closest('.form-group').find('i.fa.form-control-feedback').remove();
                            $(element).closest('.form-group').append('<i class=\"fa fa-exclamation fa-lg form-control-feedback\"></i>');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            $(element).closest('.form-group').removeClass('has-error').addClass('has-success').addClass('has-feedback');
                            $(element).closest('.form-group').find('i.fa.form-control-feedback').remove();
                            $(element).closest('.form-group').append('<i class=\"fa fa-check fa-lg form-control-feedback\"></i>');
                        },
                        errorElement: 'span',
                        errorClass: 'help-block invalid-feedback',
                        errorPlacement: function(error, element) {
                            if (element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                                error.insertAfter(element.parent().parent());
                            } else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                                error.appendTo(element.parent().parent());
                            } else {
                                error.insertAfter(element);
                            }
                        }
                     });
                ";
            } else {
                $content = "
                    $('#" . $id . "').validate({
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
            expJavascript::pushToFoot(
                array(
                    "unique" => 'formvalidate-' . $id,
                    "jquery" => 'jquery.validate',
                    "content" => $content,
                )
            );
        }

        if (bs4() || bs5()) {
            $newui_class .= ' row';
        }
		echo '<form role="form" id="',$id,'" name="',$name,'" class="',$params['class'], $newui_class, ($params['horizontal']?' form-horizontal':''),'" method="',$method,'" action="',PATH_RELATIVE,'index.php" enctype="',$enctype,'">',"\r\n";
		if (!empty($controller)) {
			echo '<input type="hidden" name="controller" id="controller" value="',$controller,'" />'."\r\n";
		} else {
			echo '<input type="hidden" name="module" id="module" value="',$module,'" />'."\r\n";
		}
		echo '<input type="hidden" name="src" id="src" value="',$smarty->getTemplateVars('__loc')->src,'" />',"\r\n";
		echo '<input type="hidden" name="int" id="int" value="',$smarty->getTemplateVars('__loc')->int,'" />',"\r\n";
		if (isset($params['action']))  echo '<input type="hidden" name="action" id="action" value="',$params['action'],'" />'."\r\n";

		//echo the innards
	} else {
		echo $content;
		echo '</form>';
	}
}

?>
