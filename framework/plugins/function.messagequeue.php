<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {messagequeue} function plugin
 *
 * Type:     function<br>
 * Name:     messagequeue<br>
 * Purpose:  display 'flash' message queue
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_messagequeue($params,&$smarty) {
    echo show_msg_queue();
}

?>

