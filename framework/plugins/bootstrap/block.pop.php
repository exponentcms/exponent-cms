<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
function smarty_block_pop($params,$content,&$smarty, &$repeat) {
	if($content){
        $content = json_encode(str_replace("\n", '', str_replace("\r\n", '', trim($content))));
        if (isset($params['icon'])) {
            $icon = $params['icon'];
        } else {
            $icon = 'file';
        }
        $width  = !empty($params['width']) ? $params['width'] : "800px";
        echo '<a class="' . expTheme::buttonStyle() . '" href="#" id="' . $params['id'] . '">' . expTheme::iconStyle('file', $params['text']) . '</a>';
        if (isset($params['type'])) {
            $type = $params['type'];
        } else {
            $type = '';
        }

        $script = "
            $(document).ready(function(){
                $('#".$params['id']."').click(function(event) {
                    event.preventDefault();
                    var message = ".$content.";
                    $.prompt(message, {
                        title: '".$params['title']."',
                        position: {
                            width: '" . $width . "'
                        },
                        classes: {
                            title: '".$type."',
                        },
                        buttons: {'".$params['buttons']."': true},
                        submit: function(e,v,m,f){
                            // use e.preventDefault() to prevent closing when needed or return false.
                        }
                    });
                });
            });
        ";
        expJavascript::pushToFoot(array(
            "unique"=>'pop-'.$params['id'],
            "jquery"=>"jquery-impromptu",
            "content"=>$script,
         ));

	}
}

?>

