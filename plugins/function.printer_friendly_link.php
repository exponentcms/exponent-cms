<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Written and Designed by James Hunt
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

function smarty_function_printer_friendly_link($params,&$smarty) {
	global $router;

	// initialize a couple of variables
	$text = isset($params['text']) ? $params['text'] : 'View Printer Friendly';
	$view = isset($params['view']) ? $params['view'] : null;

	// spit out the link
	$class = isset($params['class']) ? $params['class'] : 'printer-friendly-link';
	echo $router->printerFriendlyLink($text, $class, 800, 600, $view);
}

?>

