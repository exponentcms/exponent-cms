<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

function smarty_function_chainbytags($params,&$smarty) {
	if (empty($params['controller'])) return false;

	// we're expecting these to come in as an array of expTags
	$tags = array();
	foreach($params['tags'] as $tag) {
		$tags[] = $tag->id;
	}

	$view = isset($params['view']) ? $params['view'] : 'showall';
	$action = isset($params['action']) ? $params['action'] : 'showByTags';
	$model = isset($params['model']) ? $params['model'] : '';
	echo renderAction(array('controller'=>$params['controller'], 'action'=>$action, 'view'=>$view, 'tags'=>$tags, 'model'=>$model));
}

?>

