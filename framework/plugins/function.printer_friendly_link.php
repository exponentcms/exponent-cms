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
 * Smarty {printer_friendly_link} function plugin
 *
 * Type:     function<br>
 * Name:     printer_friendly_link<br>
 * Purpose:  format a link for displaying a printer friendly version of the page
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_printer_friendly_link($params,&$smarty) {
	global $router;

	// initialize a couple of variables
	$text = isset($params['text']) ? $params['text'] : gt('View Printer Friendly');
	$view = isset($params['view']) ? $params['view'] : null;

	// spit out the link
	$class = isset($params['class']) ? $params['class'] : 'printer-friendly-link';
	echo $router->printerFriendlyLink($text, $class, 800, 600, $view);
}

?>

