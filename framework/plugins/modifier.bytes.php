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
 * Smarty {bytes} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     bytes<br>
 * Purpose:  convert to mega/kilo/bytes
 *
 * @param array
 *
 * @return string
 *
 * @package Smarty-Plugins
 * @subpackage Modifier
 */
function smarty_modifier_bytes($bytes) {
    if ($bytes >= 1048576) {
        return number_format(round($bytes/1048576, 2)) . ' ' . gt('mb');
    } elseif ($bytes >= 1024) {
        return number_format(round($bytes/1024, 2)) . ' ' . gt('kb');
    } else {
        return number_format($bytes) . ' ' . gt('bytes');
    }

}

?>
