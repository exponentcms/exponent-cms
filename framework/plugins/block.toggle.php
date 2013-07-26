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
    if (empty($params['title']) && !empty($params['label'])) $params['title'] = $params['label'];
    if (empty($params['title']) && empty($params['link'])) die("<strong style='color:red'>".gt("The 'title' parameter is required for the {toggle} plugin.")."</strong>");
    $summary = !empty($params['summary']) ? $params['summary'] : '';
//    if ($summary) {
//        $css = ".yui3-module.yui3-closed a.yui3-toggle { top: ".$summary."px; }";
//    } else {
//        $css = "";
//    }
	if(empty($content)) {
        if (!empty($params['link'])) $params['title'] = $params['link'];

        echo '<div id="'.$params['unique'].'" class="yui3-module">
            <div id="head" class="yui3-hd">
                <h4 id="h4-'.$params['unique'].'" title="'.gt('Click to Expand').'">'.$params['title'].'</h4>
                <a id="a-'.$params['unique'].'" title="'.gt('Click to Expand').'" class="yui3-toggle"></a>
            </div>
        ';
        echo '  <div class="yui3-bd">';
        if (!empty($summary)) {
            echo  '<div id="'.$params['unique'].'-summary" class="hide">' . $params['summary'] . '  </div>';
        }
        echo '  <div id="'.$params['unique'].'-body">

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
		echo '</div></div></div>';

        $script = "
    YUI(EXPONENT.YUI3_CONFIG).use('anim', function(Y) {
        var module = Y.one('#".$params['unique']."');
        ";
        if (!empty($summary)) $script .= "
        var bodytext = Y.one('#".$params['unique']."-body');
        var summarytext = Y.one('#".$params['unique']."-summary');
        var bodyheight = module.one('.yui3-bd').get('scrollHeight');
        bodytext.toggleClass('hide');
        summarytext.toggleClass('hide');
        var summaryheight = module.one('.yui3-bd').get('scrollHeight');
        bodytext.toggleClass('hide');
        summarytext.toggleClass('hide');
        ";

        $script .= "
        // add fx plugin to module body
        var content = module.one('.yui3-bd').plug(Y.Plugin.NodeFX, {
            from: { height: 0 },
            ";
        if (empty($summary)) {
            $script .= "
            to: { height: function(node) { // dynamic in case of change
                    return node.get('scrollHeight'); // get expanded height (offsetHeight may be zero)
                    }
                },
                ";
        } else {
            $script .= "
            to: { height: summaryheight },
            ";
        }
        $script .= "
            easing: Y.Easing.".$anim."Both,
            duration: 0.5
        });

        var onClick = function(e) {
            e.preventDefault();
            module.toggleClass('yui3-closed');
            content.fx.run();  // close
            content.fx.set('reverse', !content.fx.get('reverse')); // toggle reverse
            ";
        if (!empty($summary)) $script .= "
            bodytext.toggleClass('hide');
            summarytext.toggleClass('hide');
            if (bodytext.hasClass('hide')) {
                content.fx.set('to', { height: bodyheight });
            } else {
                content.fx.set('to', { height: summaryheight });
            }
            content.fx.run();  // re-open
            content.fx.set('reverse', !content.fx.get('reverse')); // toggle reverse
            ";
        $script .= "
            if (module.hasClass('yui3-closed'))  {
                Y.one('#h4-".$params['unique']."').set('title','".gt('Click to Expand')."');
                Y.one('#a-".$params['unique']."').set('title','".gt('Click to Expand')."');
            } else {
                Y.one('#h4-".$params['unique']."').set('title','".gt('Click to Collapse')."');
                Y.one('#a-".$params['unique']."').set('title','".gt('Click to Collapse')."');
            }
        };

        module.one('#head').on('click', onClick);
        ";

        if (!empty($params['collapsed'])) {
            $script .= "
            // start w/ item collapsed
            module.toggleClass('yui3-closed');
            ";
            if (empty($summary)) {
                $script .= "
                content.fx.run();  // close
                content.fx.set('reverse', !content.fx.get('reverse')); // toggle reverse
                ";
            } else {
                $script .= "
                bodytext.toggleClass('hide');
                summarytext.toggleClass('hide');
                if (bodytext.hasClass('hide')) {
                    content.fx.set('to', { height: bodyheight });
                } else {
                    content.fx.set('to', { height: summaryheight });
                }
                ";
            }
            $script .= "
            Y.one('#h4-".$params['unique']."').set('title','".gt('Click to Expand')."');
            Y.one('#a-".$params['unique']."').set('title','".gt('Click to Expand')."');
            ";
        }

        $script .= "
    });
            ";

        expJavascript::pushToFoot(array(
            "unique"  => 'toggle-' . $params['unique'],
            "yui3mods"=> 1,
            "content" => $script,
        ));
        expCSS::pushToHead(array(
//            "unique"=>'toggle',
            "corecss"=>"toggle",
//            "css"=>$css,
        ));
    }

}

?>

