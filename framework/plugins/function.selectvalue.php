<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * Smarty {selectvalue} function plugin
 *
 * Type:     function<br>
 * Name:     selectvalue<br>
 * Purpose:  select a database table value
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool|null
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_selectvalue($params,&$smarty) {
	global $db;

	if (empty($params['field']) || empty($params['table'])) return false;

	$where = isset($params['where']) ? $params['where'] : null;
    $val = $db->selectValue($params['table'], $params['field'], $params['where']);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $val);
    } else {
        return $val;
    }

}

?>
