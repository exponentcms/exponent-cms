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

    $config = $smarty->getTemplateVars('config');
    if (is_object($config)) {
        $print = !empty($config->printlink);
    } elseif (is_array($config)) {
        $print = !empty($config['printlink']);
    } elseif (isset($params['show'])) {  // force display of link
        $print = isset($params['show']) ? $params['show'] : null;
    }
    if ($print && !PRINTER_FRIENDLY && !EXPORT_AS_PDF && file_exists(BASE.'external/dompdf/dompdf.php')) {
        // initialize a couple of variables
        if (BTN_SIZE == 'large') {
            $btn_size = 'btn-small';
            $icon_size = 'icon-large';
        } else {
            $btn_size = 'btn-mini';
            $icon_size = '';
        }

        $text = isset($params['text']) ? $params['text'] : gt('Export as PDF');
        $view = isset($params['view']) ? $params['view'] : null;
        $prepend = isset($params['prepend']) ? $params['prepend'] : '';
        $orientation = isset($params['landscapepdf']) ? $params['landscapepdf'] : false;
        $limit = isset($params['limit']) ? $params['limit'] : '';
        $class = isset($params['class']) ? $params['class'] : $icon_size.' icon-book btn '.$btn_size;

        // spit out the link
        echo $prepend.$router->exportAsPDFLink($text, $class, 800, 600, $view, $orientation, $limit).'<br>';
    }
}

?>

