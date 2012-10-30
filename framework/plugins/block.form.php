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
 * Smarty {form} block plugin
 *
 * Type:     block<br>
 * Name:     form<br>
 * Purpose:  Set up a form block
 * 
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 */
function smarty_block_form($params,$content,&$smarty, &$repeat) {
	if(empty($content)){
		$name = isset($params['name']) ? $params['name'] : 'form';
		$id = empty($params['id']) ? $name : $params['id'];
		$module = isset($params['module']) ? $params['module'] : $smarty->getTemplateVars('__loc')->mod;
		$controller = isset($params['controller']) ? $params['controller'] : $smarty->getTemplateVars('__loc')->con;  //FIXME there is no 'con' property
		$method = isset($params['method']) ? $params['method'] : "POST";
		$enctype = isset($params['enctype']) ? $params['enctype'] : 'multipart/form-data';

		echo "<!-- Form Object 'form' -->\r\n";
		echo '<script type="text/javascript" src="'.PATH_RELATIVE.'framework/core/subsystems/forms/js/inputfilters.js.php"></script>'."\r\n";
		// echo '<script type="text/javascript" src="'.PATH_RELATIVE.'framework/core/subsystems/forms/controls/listbuildercontrol.js"></script>'."\r\n";
		// echo '<script type="text/javascript" src="'.PATH_RELATIVE.'framework/core/subsystems/forms/js/required.js"></script>'."\r\n";
		// echo '<script type="text/javascript" src="'.PATH_RELATIVE.'js/PopupDateTimeControl.js"></script>'."\r\n";

		expCSS::pushToHead(array("corecss"=>"forms"));

		echo '<form id="'.$id.'" name="'.$name.'" class="'.$params['class'].'" method="'.$method.'" action="'.PATH_RELATIVE.'index.php" enctype="'.$enctype.'">'."\r\n";
		if (!empty($controller)) {
			echo '<input type="hidden" name="controller" id="controller" value="'.$controller.'" />'."\r\n";
		} else {
			echo '<input type="hidden" name="module" id="module" value="'.$module.'" />'."\r\n";
		}
		echo '<input type="hidden" name="src" id="src" value="'.$smarty->getTemplateVars('__loc')->src.'" />'."\r\n";
		echo '<input type="hidden" name="int" id="int" value="'.$smarty->getTemplateVars('__loc')->int.'" />'."\r\n";
		if (isset($params['action']))  echo '<input type="hidden" name="action" id="action" value="'.$params['action'].'" />'."\r\n";

		//echo the innards
	}else{	
		echo $content;	
		echo '</form>';
	}
	
}

?>
