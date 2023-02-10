<?php
##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

        echo '<div id="',$params['unique'],'" class="card toggle">
            <div id="',$params['unique'],'-head" class="card-header" title="',$params['collapsed']?gt('Click to Expand'):gt('Click to Collapse') ,'">
                <h5 id="h5-',$params['unique'],'" class="card-title" data-toggle="collapse" data-target="#',$params['unique'],'-content">',$params['title'],'
                <a id="a-',$params['unique'],'" data-toggle="collapse" data-target="#',$params['unique'],'-content" class="',$params['collapsed']?' collapsed':'','"></a></h5>
            </div>
        ';
        echo '  <div id="',$params['unique'],'-content" class="card-body collapse',!$params['collapsed']?' show':'','">';
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
                $(\'#h5-' . $params['unique'] . '\').removeAttr(\'data-toggle\');
                $(\'#a-' . $params['unique'] . '\').removeAttr(\'data-toggle\');
                if ('.(int)!empty($params['collapsed']).') {
                    $(\'#' . $params['unique'] . '-content\').addClass(\'show\')
                    $(\'#a-' . $params['unique'] . '\').toggleClass(\'collapsed\');
                    doClick();
                }
            });
            ';
        } else {  // no summary, simply swap popup title
            $script = '
            var doClick = function (e){
                if ($(\'#' . $params['unique'] . '-content\').hasClass(\'show\')) {
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
            "bootstrap" => "collapse",
            "content"   => $script,
        ));

        $css = '
        .card.toggle .card-title a:after {
            font-family:"Font Awesome 5 Free";
            content:\'\f102\';
            float:right;
            font-size:18px;
            font-weight:700;
        }
        .card.toggle .card-title a.collapsed:after  {
            font-family:"Font Awesome 5 Free";
            content:\'\f103\';
        }
        ';

        expCSS::pushToHead(array(
            "css"=>$css,
        ));
    }

}

?>

