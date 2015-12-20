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
 */
function smarty_block_pop($params,$content,&$smarty, &$repeat) {
	if($content){
        $content = json_encode(str_replace("\n", '', str_replace("\r\n", '', trim($content))));
        if (isset($params['ajax'])) {
            $content = json_encode("function(dialogRef) {
                    var message = $('<div><i class=\"fa fa-spinner fa-spin\"></i> ".gt('Loading')."...</div>');
                    $.ajax({
                        url: '" . $params['ajax'] . "',
                        data: {ajax_action:1},
                        context: {
                            theDialogWeAreUsing: dialogRef
                        },
                        success: function(content) {
                            this.theDialogWeAreUsing.setMessage(content.replace(%s,'').replace(%t,''));
                        }
                    });
                    return message;
                }
            ");
            // clean up code for passing to javascript via json
            $content = trim(str_replace('\n', '', str_replace('\r\n', '', $content)) , '"');
            $content = str_replace('%s', '/\r\n/g', $content);
            $content = str_replace('%t', '/[\r\n]/g', $content);
        }
        if (isset($params['icon'])) {
            $icon = $params['icon'];
        } else {
            $icon = 'file';
        }
        echo '<a class="' . expTheme::buttonStyle() . '" href="#" id="' . $params['id'] . '">' . expTheme::iconStyle($icon, $params['text']) . '</a>';
        if (isset($params['type'])) {
            if ($params['type'] == 'warning') {
                $type = 'BootstrapDialog.TYPE_WARNING';
            } elseif ($params['type'] == 'danger') {
                $type = 'BootstrapDialog.TYPE_DANGER';
            } else {
                $type = 'BootstrapDialog.TYPE_INFO';
            }
        } else {
            $type = 'BootstrapDialog.TYPE_INFO';
        }
        $script = "
            $(document).ready(function(){
                $('#".$params['id']."').click(function(event) {
                    event.preventDefault();
                    BootstrapDialog.show({
                        size: BootstrapDialog.SIZE_WIDE,
                        type: ".$type.",
                        title: '".$params['title']."',
                        message: ".$content.",
                        buttons: [{
                            label: '".$params['buttons']."',
                            action: function(dialogRef){
                                dialogRef.close();
                            }
                        }]
                    });
                });
            });
        ";
        expJavascript::pushToFoot(array(
            "unique"=>'pop-'.$params['id'],
            "bootstrap"=>'modal,transition,tab',
            "jquery"=>"bootstrap-dialog",
            "content"=>$script,
         ));
	}
}

?>

