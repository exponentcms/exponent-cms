<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
	if(empty($content)){
		if (!empty($params['link'])) echo '<a href="javascript:void(0);" onclick="divtoggle(\''.$params['id'].'\')">'.$params['link'].'</a>';
		echo '<div id="'.$params['id'].'" style="display:none">';
	}else{	
		echo $content;	
		echo '</div>';
	}
	
}

?>

