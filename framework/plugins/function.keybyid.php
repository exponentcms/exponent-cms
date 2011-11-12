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
 * Smarty {keybyid} function plugin
 *
 * Type:     function<br>
 * Name:     keybyid<br>
 * Purpose:  get and assign key by its id
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_keybyid($params,&$smarty) {

	$obj = $params['obj'];
	
	foreach($obj as $key=>$value){
		$rekeyed[$value->id] = $value;
	}

	if (isset($params['assign'])) $smarty->assign($params['assign'],$rekeyed);
}


?>

