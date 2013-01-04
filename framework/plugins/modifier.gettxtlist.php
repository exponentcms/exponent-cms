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
 * @subpackage Modifier
 */

/**
 * Smarty {gettxtlist} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     gettxtlist<br>
 * Purpose:  Replace comma separated list with the chosen language for the text
 * 
 * @param string $str
 * @return string
 */
function smarty_modifier_gettxtlist($str) {
    return glist($str);
}
