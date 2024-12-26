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
 * Smarty {pop} block plugin
 *
 * Type:     block<br>
 * Name:     pop<br>
 * Purpose:  Set up a pop block
 *
 * @param array $params based on expJavascript::panel()
 *          'id' to differentiate popups
 *          'width' width of popup, defaults to '300px'
 *          'type' id type of popup, defaults to 'info', also 'error' & 'alert'
 *          'buttons' text string of 2 button names separated by ':'
 *          'title' title of popup
 *          'close' should popup have a close button (x), defaults to true
 *          'trigger' what object to base event trigger on, defaults to 'selfpop' which displays when popup is ready
 *          'on' what 'event' to display popup, defaults to 'load', or 'click' if 'trigger' is set
 *          'onnogo' what url to browse to when the 'no' button is selected
 *          'onyesgo' what url to browse to when the 'yes' button is selected
 *          'fade' seconds duration of popup 'fade' in/out, defaults to false
 *          'modal' should the popup be 'modal', defaults to true
 *          'draggable' should the popup be 'draggable', defaults to false
 *
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 *
 * @package    Smarty-Plugins
 * @subpackage Block
 */
function smarty_block_pop($params, $content, &$smarty, &$repeat)
{
    if ($content) {
        if (isset($params['icon'])) {
            $icon = $params['icon'];
        } else {
            $icon = 'file';
        }
        if (isset($params['type'])) {
            if ($params['type'] === 'warning') {
                $type = 'bg-warning';
            } elseif ($params['type'] === 'danger') {
                $type = 'bg-danger';
            } else {
                $type = 'bg-info';
            }
        } else {
            $type = 'bg-info';
            $params['type'] = '';
        }
        echo '<a class="' . expTheme::buttonStyle($params['type']) . '" href="#" id="' . $params['id'] . '">' . expTheme::iconStyle($icon, $params['text']) . '</a>';

        if (isset($params['ajax'])) {
//            $script = "
//                $(document).ready(function(){
//                    $('#" . $params['id'] . "').click(function(event) {
//                        event.preventDefault();
//                        $.ajax({
//                            url: '" . $params['ajax'] . "',
//                            data: {ajax_action:1},
//                            success: function(content) {
//                                bootbox.dialog({
//                                    size: 'extra-large',
//                                    className: '" . $type . "',
//                                    title: '" . $params['title'] . "',
//                                    message: content,
//                                    buttons: {
//                                        ok: {
//                                            label: '" . $params['buttons'] . "',
//                                        }
//                                    }
//                                });
//                            }
//                        });
//                    });
//                });
//            ";
//
            $script = "
                $(document).ready(function(){
                    var message = $('<div></div>');
                    message.load('".$params['ajax']."');

                    $('#" . $params['id'] . "').click(function(event) {
                        event.preventDefault();
                        bootbox.dialog ({
                            size: 'extra-large',
                            className: '" . $type . "',
                            title: '" . $params['title'] . "',
                            message: message.html(),
                            buttons: {
                                ok: {
                                    label: '" . $params['buttons'] . "',
                                }
                            }
                        });
                    });
                });
            ";
        } else {
            $script = "
                $(document).ready(function(){
                    $('#" . $params['id'] . "').click(function(event) {
                        event.preventDefault();
                        bootbox.dialog ({
                            size: 'extra-large',
                            className: '" . $type . "',
                            title: '" . $params['title'] . "',
                            message: " . json_encode(str_replace("\n", '', str_replace("\r\n", '', trim($content)))) . ",
                            buttons: {
                                ok: {
                                    label: '" . $params['buttons'] . "',
                                }
                            }
                        });
                    });
                });
            ";
        }
        expJavascript::pushToFoot(array(
            "unique" => 'pop-' . $params['id'],
            "bootstrap" => 'modal,tab',
            "jquery" => "bootbox.all",
            "content" => $script,
        ));
    }
}

?>

