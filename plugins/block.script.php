<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

function smarty_block_script($params,$content,&$smarty, &$repeat) {
	if ($content) {
		global $userjsfiles;
		
		if (empty($params['unique'])) die("<strong style='color:red'>The 'unique' parameter is required for the {script} pluggin.</strong>"); 
		
		//exponent_javascript_toFoot($params['unique'],$params['yuimodules'],$smarty->_tpl_vars[__name],$content,$params['src']);
        expJavascript::pushToFoot(array(
            "unique"=>$params['unique'],
            "yui2mods"=>$params['yui2mods']?$params['yui2mods']:$params['yuimodules'],
            "yui3mods"=>$params['yui3mods'],
            "content"=>$content,
            "src"=>$params['src']
         ));
	}
}


?>