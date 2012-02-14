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
 * @subpackage Function
 */

/**
 * Smarty {pagebacklink} function plugin
 *
 * Type:     function<br>
 * Name:     pagebacklink<br>
 * Purpose:  display pagination back page link
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_pagebacklink($params,&$smarty) {
	if ($params['page']->page > 1) {
		// initialize a couple of variables
		$class = isset($params['class']) ? $params['class'] : 'page-back';
		$text = isset($params['text']) ? $params['text'] : '< '.gt('Back');

		// if the designer specified an image then show it here
		if (isset($params['image'])) {
			$imgClass = isset($params['imageclass']) ? $params['imageclass'] : 'page-back-image';
			echo '<img class="'.$imgClass.'" src="'.$params['image'].'" />';
		}

		// spit out the link
		$newpage = $params['page']->page - 1;
		echo '<a class="'.$class.'" href="#" onclick="page('.$newpage.')">'.$text.'</a>';
	}
}

?>

