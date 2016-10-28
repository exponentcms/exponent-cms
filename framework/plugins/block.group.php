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
 * Smarty {group} block plugin
 *
 * Type:     block<br>
 * Name:     group<br>
 * Purpose:  Set up a group block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 */
function smarty_block_group($params,$content,&$smarty, &$repeat) {
	if(empty($content)) {
        if (!empty($params['id']))
            echo '<div id="' . $params['id'] . '">';
		if (!empty($params['label']))
            echo '<div class="control" style="margin-bottom: 0;padding-bottom: 0;"><label class="'.(bs3()?'control-label':'label').'" style="margin-bottom: 0;padding-bottom: 0;">'.$params['label'].'</label></div>';
        $class = !empty($params['class']) ? ' ' . $params['class'] : '';
		echo '<div role="group" class="group-controls', $class, '">';
	} else {
		echo $content;
		echo '</div>';
        if (!empty($params['description'])) {
            if (bs3()) {
                echo "<div class=\"control\"><p class=\"help-block\">",$params['description'],"</p></div>";
            } else {
                echo "<div class=\"control\"><div class=\"control-desc\">",$params['description'],"</div></div>";
            }
        }
        if (!empty($params['id']))
            echo '</div/>';
	}

}

?>
