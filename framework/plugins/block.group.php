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
		if (!empty($params['label'])) echo '<div class="control" style="margin-bottom: 0;padding-bottom: 0;"><label class="label" style="margin-bottom: 0;padding-bottom: 0;">'.$params['label'].'</label></div>';
		echo '<div class="group-controls">';
	} else {
		echo $content;	
		echo '</div>';
	}

}

?>

