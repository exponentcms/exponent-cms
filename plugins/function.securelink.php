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

function smarty_function_securelink($params,&$smarty) {
	/*$loc = $smarty->_tpl_vars['__loc'];
	if (!isset($params['module'])) $params['module'] = $loc->mod;
	if (!isset($params['src'])) $params['src'] = $loc->src;
	if (!isset($params['int'])) $params['int'] = $loc->int;
	
	$params['expid'] = session_id();
	*/
        $loc = $smarty->_tpl_vars['__loc'];
        if (!isset($params['module'])) {
                $params['module'] = empty($params['controller']) ? $loc->mod : $params['controller'];
        }
        if (!isset($params['src'])) {
                 if (controllerExists($params['module'])) {
                        $params['src'] = $loc->src;
                } elseif (@call_user_func(array($loc->mod,'hasSources'))) {
                        $params['src'] = $loc->src;
                }
        }
        if (!isset($params['int'])) $params['int'] = $loc->int;
	echo exponent_core_makeSecureLink($params);
}

?>
