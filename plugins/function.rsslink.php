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

function smarty_function_rsslink($params,&$smarty) {
	$loc = $smarty->_tpl_vars['__loc'];
	if (!isset($params['module'])) $params['module'] = $loc->mod;
	if (!isset($params['src'])) $params['src'] = $loc->src;
	if (!isset($params['int'])) $params['int'] = $loc->int;
	
	echo exponent_core_makeRSSLink($params);
}

?>
