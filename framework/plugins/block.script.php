<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * Smarty {script} block plugin
 *
 * Type:     block<br>
 * Name:     script<br>
 * Purpose:  Set up a script block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 */
function smarty_block_script($params,$content,&$smarty, &$repeat) {
	if ($content) {
//		global $userjsfiles;
		
		if (empty($params['unique'])) die("<strong style='color:red'>".gt("The 'unique' parameter is required for the {script} plugin.")."</strong>");
		
        if ((isset($params['yui2mods']) || isset($params['yuimodules'])) && !strstr($content,"YUI(")) {
            $params['yui3mods'] = 1;
            $yui2mods = $params['yui2mods']?$params['yui2mods']:$params['yuimodules'];
            $toreplace = array('"',"'"," ");
            $stripmodquotes = str_replace($toreplace, "", $yui2mods);               
            $splitmods = explode(",",$stripmodquotes);

            $y3wrap = "YUI(EXPONENT.YUI3_CONFIG).use(";            
            $y3wrap .= "'yui2-yahoo-dom-event', ";
            foreach ($splitmods as $key=>$mod) {
                if ($mod=="menu") {
                    $y3wrap .= "'yui2-container', ";
                }
                $y3wrap .= "'yui2-".$mod."', ";
            }
            $y3wrap .= "function(Y) {\r\n";
            $y3wrap .= "var YAHOO=Y.YUI2;";
            $y3wrap .= $content;
            $y3wrap .= "});";
            
            $content = $y3wrap;
        }
		
        expJavascript::pushToFoot(array(
            "unique"=>$params['unique'],
            //"yui2mods"=>$params['yui2mods']?$params['yui2mods']:$params['yuimodules'],
            "yui3mods"=>$params['yui3mods'],
            "content"=>$content,
            "src"=>$params['src']
         ));
	}
}


?>