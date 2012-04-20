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
 * Smarty {export_pdf_link} function plugin
 *
 * Type:     function<br>
 * Name:     export_pdf_link<br>
 * Purpose:  format a link for exporting a PDF version of the page
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_export_pdf_link($params,&$smarty) {
	global $router;

	// initialize a couple of variables
	$text = isset($params['text']) ? $params['text'] : gt('Export as PDF');
	$view = isset($params['view']) ? $params['view'] : null;
    $orientation = isset($params['landscapepdf']) ? $params['landscapepdf'] : false;
    $limit = isset($params['limit']) ? $params['limit'] : '';

	// spit out the link
	$class = isset($params['class']) ? $params['class'] : 'export-pdf-link';
	echo $router->exportAsPDFLink($text, $class, 800, 600, $view, $orientation, $limit);
}

?>

