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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Block
 */

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
 */
function smarty_block_toggle($params,$content,&$smarty, &$repeat) {
    if (empty($params['unique'])) die("<strong style='color:red'>".gt("The 'unique' parameter is required for the {toggle} plugin.")."</strong>");
    if (empty($params['title']) && empty($params['link'])) die("<strong style='color:red'>".gt("The 'title' parameter is required for the {toggle} plugin.")."</strong>");
    $summary = !empty($params['summary']) ? $params['summary'] : 0;
    if ($summary) {
        $css = ".yui3-module.yui3-closed a.yui3-toggle { top: ".$summary."px; }";
    } else {
        $css = "";
    }
	if(empty($content)) {
        if (!empty($params['link'])) $params['title'] = $params['link'];

        echo '<div id="'.$params['unique'].'" class="yui3-module">
            <div id="head" class="yui3-hd">
                <h3 title="'.gt('Click to Collapse/Expand').'">'.$params['title'].'</h3>
                <a title="'.gt('Collapse/Expand').'" class="yui3-toggle"></a>
            </div>
        ';
        echo '    <div class="yui3-bd">
        ';
	} else {
        if (!empty($params['anim'])) {
            $anim = 'ease';
            //FIXME replace w/ system default?
        } else {
            switch ($params['anim']) {
                case 'back':
                    $anim = 'back';
                    break;
                case 'bounce':
                    $anim = 'bounce';
                    break;
                case 'elastic':
                    $anim = 'elastic';
                    break;
                case 'ease':
                default:
                    $anim = 'ease';
                    break;
            }
        }
		echo $content;
		echo '</div></div>';

        $script = "
    YUI(EXPONENT.YUI3_CONFIG).use('anim', function(Y) {
        var module = Y.one('#".$params['unique']."');

        // add fx plugin to module body
        var content = module.one('.yui3-bd').plug(Y.Plugin.NodeFX, {
            from: { height: ".$summary." },
            to: {
                height: function(node) { // dynamic in case of change
                    return node.get('scrollHeight'); // get expanded height (offsetHeight may be zero)
                }
            },

            easing: Y.Easing.".$anim."Both,
            duration: 0.5
        });

        var onClick = function(e) {
            e.preventDefault();
            module.toggleClass('yui3-closed');
            content.fx.set('reverse', !content.fx.get('reverse')); // toggle reverse
            content.fx.run();
        };

        module.one('#head').on('click', onClick);
        ";

        if (!empty($params['collapsed']))$script .= "
        // start w/ item collapsed
        module.toggleClass('yui3-closed');
        content.fx.set('reverse', !content.fx.get('reverse')); // toggle reverse
        content.fx.run();
        ";

        $script .= "
    });
            ";

        expJavascript::pushToFoot(array(
            "unique"  => 'toggle-' . $params['unique'],
            "yui3mods"=> 1,
            "content" => $script,
        ));
        expCSS::pushToHead(array(
            "unique"=>'toggle',
            "corecss"=>"toggle",
            "css"=>$css,
        ));
    }

}

?>

