<?php
##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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
 * Smarty {toggle} block plugin
 *
 * Type:     block<br>
 * Name:     toggle<br>
 * Purpose:  Set up a toggle block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 *
 * @package    Smarty-Plugins
 * @subpackage Block
 */
function smarty_block_toggle($params,$content,&$smarty, &$repeat) {
    if (empty($params['unique'])) die("<strong style='color:red'>".gt("The 'unique' parameter is required for the {toggle} plugin.")."</strong>");
    if (empty($params['title']) && !empty($params['label'])) $params['title'] = $params['label'];
    if (empty($params['title']) && empty($params['link'])) die("<strong style='color:red'>".gt("The 'title' parameter is required for the {toggle} plugin.")."</strong>");

    $summary = !empty($params['summary']) ? $params['summary'] : '';

	if(empty($content)) {
        if (!empty($params['link'])) $params['title'] = $params['link'];

        echo '<div id="',$params['unique'],'" class="panel panel-default toggle">
            <div id="',$params['unique'],'-head" class="panel-heading" title="',$params['collapsed']?gt('Click to Expand'):gt('Click to Collapse') ,'">
                <h2 id="h2-',$params['unique'],'" class="panel-title" data-toggle="collapse" data-target="#',$params['unique'],'-content">',$params['title'],'
                <a id="a-',$params['unique'],'" data-toggle="collapse" data-target="#',$params['unique'],'-content" class="',$params['collapsed']?' collapsed':'','"></a></h2>
            </div>
        ';
        echo '  <div id="',$params['unique'],'-content" class="panel-body collapse',!$params['collapsed']?' in':'','">';
        if (!empty($summary)) {
            echo  '<div id="',$params['unique'],'-summary" class="hide">', $params['summary'], '  </div>';
        }
        echo '  <div id="',$params['unique'],'-body">

        ';
	} else {
		echo $content;
		echo '</div></div></div>';

        if (!empty($summary)) {
            $script = '
            var bodytext = $(\'#' . $params['unique'] . '-body\');
            var summarytext = $(\'#' . $params['unique'] . '-summary\');

            var doClick = function (e){
                bodytext.toggleClass(\'hide\');
                summarytext.toggleClass(\'hide\');
                $(\'#a-' . $params['unique'] . '\').toggleClass(\'collapsed\');

                if ($(\'#' . $params['unique'] . '-body\').hasClass(\'hide\')) {
                    $(\'#' . $params['unique'] . '-head\').prop(\'title\',"' . gt('Click to Expand') . '");
                } else {
                    $(\'#' . $params['unique'] . '-head\').prop(\'title\',"' . gt('Click to Collapse') . '");
                }
            }

            $(document).ready(function(){  // swap to summary and display it
                $(\'#h2-' . $params['unique'] . '\').removeAttr(\'data-toggle\');
                $(\'#a-' . $params['unique'] . '\').removeAttr(\'data-toggle\');
                if ('.(int)!empty($params['collapsed']).') {
                    $(\'#' . $params['unique'] . '-content\').addClass(\'in\')
                    $(\'#a-' . $params['unique'] . '\').toggleClass(\'collapsed\');
                    doClick();
                }
            });
            ';
        } else {  // no summary, simply swap popup title
            $script = '
            var doClick = function (e){
                if ($(\'#' . $params['unique'] . '-content\').hasClass(\'in\')) {
                    $(\'#' . $params['unique'] . '-head\').prop(\'title\',\'' . gt('Click to Expand') . '\');
                } else {
                    $(\'#' . $params['unique'] . '-head\').prop(\'title\',\'' . gt('Click to Collapse') . '\');
                }
            }
            ';
        }

        // generic code to handle clicking title
        $script .= '
            $(document).ready(function(){  // swap to summary and display it
                $(\'#' . $params['unique'] . '-head\').on(\'click\',doClick);
            });
        ';

        expJavascript::pushToFoot(array(
            "unique"    => 'accordion',
            "bootstrap" => "collapse,transition",
            "content"   => $script,
        ));

        $css = '
        .panel.toggle .panel-title a:after {
            font-family:Fontawesome;
            //content:\'\f077\';
            content:\'\f102\';
            float:right;
            font-size:18px;
            font-weight:700;
        }
        .panel.toggle .panel-title a.collapsed:after  {
            font-family:Fontawesome;
            //content:\'\f078\';
            content:\'\f103\';
        }
        ';

        expCSS::pushToHead(array(
            "css"=>$css,
        ));
    }

}

?>

