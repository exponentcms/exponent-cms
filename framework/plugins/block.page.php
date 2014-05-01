<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * Smarty {page} block plugin
 *
 * Type:     block<br>
 * Name:     page<br>
 * Purpose:  Set up a form page (wizard) block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 */
function smarty_block_page($params,$content,&$smarty, &$repeat) {
	if(empty($content)) {
        if (empty($params['label'])) die("<strong style='color:red'>".gt("The 'label' parameter is required for the {page} plugin.")."</strong>");
		$title = ' title="'.$params['label'].'"';
        $description =  (!empty($params['description'])) ? $params['description'] : '';
		echo '<fieldset'.$title.'>
                  <legend>'.$description.'</legend>';
	} else {
		echo $content;	
		echo '</fieldset>';
	}

}

?>

