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

function smarty_function_selectobjects($params,&$smarty) {
	global $db;
	$where = isset($params['where']) ? $params['where'] : null;
	$where = isset($params['orderby']) ? $params['orderby'] : null;
	$arr = $db->selectObjects($params['table'], $params['where'], $params['orderby']);
	$smarty->assign($params['item'], $arr);
}

?>
