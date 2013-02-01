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
 * @subpackage Block
 */

/**
 * Smarty {if_elements} block plugin
 *
 * Type:     block<br>
 * Name:     if_elements<br>
 * Purpose:  Set up a if elements block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 * @return string
 */
function smarty_block_if_elements($params,$content,&$smarty, &$repeat) {
	if ($content) {
		return (count($params['array']) ? $content : "");
	}
}

?>