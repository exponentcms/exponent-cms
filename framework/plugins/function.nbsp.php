<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * Smarty {nbsp} function plugin
 *
 * Type:     function<nbsp>
 * Name:     nbsp
 * Purpose:  create an appropriate non-breaking space
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_nbsp($params,&$smarty) {
    if (empty($params['count'])) {
        echo "&#160;";
    } else {
        for ($i=0; $i<$params['count']; $i++) {
            echo "&#160;";
        }
    }
}

?>

