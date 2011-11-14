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
 * Smarty {time_duration} function plugin
 *
 * Type:     function<br>
 * Name:     time_duration<br>
 * Purpose:  calculate and assign a duration
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_time_duration($params,&$smarty) {
	$duration = null;
	if (isset($params['duration'])) {
		$duration = $params['duration'];
	} else {
		$duration = $params['start'] - $params['end'];
	}
	if ($duration < 0) $duration *= -1;
	
	$type = strtolower(isset($params['type']) ? $params['type'] : "hms");
	
	$slots = array();
	if (strstr($type,"d") !== false) {
		$slots["d"] = floor($duration / 86400);
		$duration -= $slots["d"]*86400;
	}
	if (strstr($type,"h") !== false) {
		$slots["h"] = floor($duration / 3600);
		$duration -= $slots["h"] * 3600;
	}
	if (strstr($type,"m") !== false) {
		$slots["m"] = floor($duration / 60);
		$duration -= $slots["m"] * 60;
	}
	if (strstr($type,"s") !== false) $slots["s"] = $duration;
	
	$smarty->assign($params['assign'],$slots);
}

?>