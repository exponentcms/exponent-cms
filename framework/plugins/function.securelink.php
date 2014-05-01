<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * Smarty {securelink} function plugin
 *
 * Type:     function<br>
 * Name:     securelink<br>
 * Purpose:  create a secure link
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_securelink($params,&$smarty) {
	/*$loc = $smarty->getTemplateVars('__loc');
	if (!isset($params['module'])) $params['module'] = $loc->mod;
	if (!isset($params['src'])) $params['src'] = $loc->src;
	if (!isset($params['int'])) $params['int'] = $loc->int;
	
	$params['expid'] = session_id();
	*/
        $loc = $smarty->getTemplateVars('__loc');
        if (!isset($params['module'])) {
            $params['module'] = empty($params['controller']) ? $loc->mod : $params['controller'];
        }
        if (!isset($params['src'])) {
             if (expModules::controllerExists($params['module'])) {
                $params['src'] = $loc->src;
//            } elseif (@call_user_func(array($loc->mod,'hasSources'))) {
//                $params['src'] = $loc->src;
            }
        }
        if (!isset($params['int'])) $params['int'] = $loc->int;
//	echo expCore::makeSecureLink($params);
    global $router;
    echo $router->makeLink($params, false, true);
}

?>
