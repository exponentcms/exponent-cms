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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Block
 */

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
 */
if (!function_exists('smarty_block_form')) {
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

//            if (!NEWUI) {
//                if (expSession::get('framework') != 'bootstrap') {
//                    expCSS::pushToHead(array(
//                        "corecss"=>"forms"
//                    ));
//                    $btn_class = "awesome " . BTN_SIZE . " " . BTN_COLOR;
//                } else {
//                    expCSS::pushToHead(array(
//                        "corecss"=>"forms-bootstrap"
//                    ));
//                    $btn_class = 'btn btn-default';
//                    if (BTN_SIZE == 'large') {
//                        $btn_size = '';  // actually default size, NOT true bootstrap large
//                    } elseif (BTN_SIZE == 'small') {
//                        $btn_size = 'btn-mini';
//                    } else { // medium
//                        $btn_size = 'btn-small';
//                    }
//                    $btn_class .= ' ' . $btn_size;
//                }
//            }
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
            }
//        }
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
            if (!empty($params['paged'])) {
                if (empty($params['name']) && empty($params['id'])) die("<strong style='color:red'>".gt("The 'name' or 'id parameter is required for the paged {form} plugin.")."</strong>");
                $content = "
                    $('#" . $id . "').stepy({
                        validate: true,
                        block: true,
                        errorImage: true,
                    //    description: false,
                    //    legend: false,
                        btnClass: '" . $btn_class . "',
                        titleClick: true,
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
//                                $('.stepy-header .stepy-active').addClass('stepy-error');
                                $(element).closest('.control').removeClass('has-success').addClass('has-error');
        //                        var id_attr = '#' + $( element ).attr('id') + '1';
        //                        $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');  // requires a feedback <span> and has-feedback class to control
                            },
                            unhighlight: function(element) {
//                                $('.stepy-header .stepy-error').removeClass('stepy-error');
                                $(element).closest('.control').removeClass('has-error').addClass('has-success');
        //                        var id_attr = '#' + $( element ).attr('id') + '1';
        //                        $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');  // requires a feedback <span> and has-feedback class to control
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
                        }
                    });
                ";
                expJavascript::pushToFoot(array(
                    "unique"  => 'stepy-'.$id,
                    "jquery"  => 'jquery.validate,jquery.stepy',
                    "content" => $content,
                ));
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
                        highlight: function(element) {
                            $(element).closest('.control').removeClass('has-success').addClass('has-error');
    //                        var id_attr = '#' + $( element ).attr('id') + '1';
    //                        $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');  // requires a feedback <span> and has-feedback class to control
                        },
                        unhighlight: function(element) {
                            $(element).closest('.control').removeClass('has-error').addClass('has-success');
    //                        var id_attr = '#' + $( element ).attr('id') + '1';
    //                        $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');  // requires a feedback <span> and has-feedback class to control
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
                expJavascript::pushToFoot(
                    array(
                        "unique" => 'formvalidate-' . $id,
                        "jquery" => 'jquery.validate',
                        "content" => $content,
                    )
                );
            }

            echo '<div class="exp-skin">';
            echo '<form role="form" id="',$id,'" name="',$name,'" class="',$params['class'], ($params['horizontal']?' form-horizontal':''),'" method="',$method,'" action="',PATH_RELATIVE,'index.php" enctype="',$enctype,'">',"\r\n";
            if (!empty($controller)) {
                echo '<input type="hidden" name="controller" id="controller" value="',$controller,'" />',"\r\n";
            } else {
                echo '<input type="hidden" name="module" id="module" value="',$module,'" />',"\r\n";
            }
            echo '<input type="hidden" name="src" id="src" value="',$smarty->getTemplateVars('__loc')->src,'" />',"\r\n";
            echo '<input type="hidden" name="int" id="int" value="',$smarty->getTemplateVars('__loc')->int,'" />',"\r\n";
            if (isset($params['action']))  echo '<input type="hidden" name="action" id="action" value="',$params['action'],'" />'."\r\n";

            //echo the innards
        } else {
            echo $content;
            echo '</form></div>';
        }
    }
}

?>
