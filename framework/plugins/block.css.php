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
 * Smarty {css} block plugin
 *
 * Type:     block<br>
 * Name:     css<br>
 * Purpose:  Set up a css block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 */
function smarty_block_css($params,$content,&$smarty, &$repeat) {
	if ($content) {		
		if (empty($params['unique'])) die("<strong style='color:red'>".gt("The 'unique' parameter is required for the {css} plugin.")."</strong>");

		expCSS::pushToHead(array(
		    "unique"=>$params['unique'],
		    "css"=>$content,
		    "link"=>!empty($params['link']) ? $params['link'] : '',
		    "corecss"=>!empty($params['corecss']) ? $params['corecss'] : '',
            "lesscss"=>!empty($params['lesscss']) ? $params['lesscss'] : '',
            "lessvars"=>!empty($params['lessvars']) ? $params['lessvars'] : '',
		    )
		);
	}
}

?>