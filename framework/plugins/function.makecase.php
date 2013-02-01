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
 * @subpackage Function
 */

/**
 * Smarty {makecase} function plugin
 *
 * Type:     function<br>
 * Name:     makecase<br>
 * Purpose:  change case of string
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return string
 */
function smarty_function_makecase($params,&$smarty) {
  switch ($params['type']){
    case 'toupper':
	    return strtoupper($params['value']);
      break;
    case 'tolower':
	    return strtolower($params['value']);
      break;
    case 'ucfirst':
	    return ucfirst(strtolower($params['value']));
      break;
    case 'ucwords':
	    return ucwords(strtolower($params['value']));
      break;
    default:
      return $params['value'];
  }
}
?>
