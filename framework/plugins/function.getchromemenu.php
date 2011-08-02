<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

function smarty_function_getchromemenu($params,&$smarty) {
	global $router, $user;
	$cloc = $smarty->_tpl_vars['__loc'];
	$module = $params['module'];

	$list = '<ul class="container-menu">';
	$list .= '<li class="container-info">'.$module->action.' / '.str_replace($module->action.'_','',$module->view).'</li>';
	if (!empty($params['rank']) && exponent_permissions_check('order_modules', $cloc)) {
		$uplink = $router->makeLink(array('module'=>'containermodule','src'=>$cloc->src,'action'=>'order','a'=>$params['rank'] - 2,'b'=>$params['rank'] - 1));
		$downlink = $router->makeLink(array('module'=>'containermodule','src'=>$cloc->src,'action'=>'order', 'a'=>$params['rank'] - 1,'b'=>$params['rank']));
		if ($params['rank'] != 1) {	//dont show this up arrow if it's the first module in a container
			$list .= '<li><a href="'.$uplink.'" class="mod-up">'.expLang::gettext("Move Module Up").'</a></li>';
		}
		if (!$params['last']) { //if this is the last module in a container don't show down arrow.
			$list .= '<li><a href="'.$downlink.'" class="mod-down">'.expLang::gettext("Move Module Down").'</a></li>';
		}
	}

	$rerank = $params['rerank'];
	if ($rerank == 'false') {
		$rerank = 0;
	} else {
		$rerank = 1;
	}
	
	if ($user->isAdmin()) {
		$userlink = $router->makeLink(array('module'=>getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'userperms', '_common'=>1));
		$grouplink = $router->makeLink(array('module'=>getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'groupperms', '_common'=>1));
		$list .= '<li><a href="'.$userlink.'" class="user">'.expLang::gettext("User Permissions").'</a></li>';
		$list .= '<li><a href="'.$grouplink.'" class="group">'.expLang::gettext("Group Permissions").'</a></li>';
	}

	if (!empty($module->id) && exponent_permissions_check('edit_module', $cloc) && $module->permissions['administrate'] == 1) {
		$editlink = $router->makeLink(array('module'=>'containermodule', 'id'=>$module->id, 'action'=>'edit', 'src'=>$module->info['source']));
		$list .= '<li><a href="'.$editlink.'" class="config-view">'.expLang::gettext("Configure Action &amp; View").'</a></li>';
	}

	if ($module->permissions['configure'] == 1) {
		if (controllerExists($module->info['class'])) {
			$configlink = $router->makeLink(array('module'=>getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'configure', 'hcview'=>$module->view));
			$list .= '<li><a href="'.$configlink.'" class="config-mod">'.expLang::gettext("Configure Settings").'</a></li>';
		} elseif ($module->info['hasConfig']) {
			$configlink = $router->makeLink(array('module'=>$module->info['class'], 'src'=>$module->info['source'], 'action'=>'configure', '_common'=>1));
			$list .= '<li><a href="'.$configlink.'" class="config-mod">'.expLang::gettext("Configure Settings").'</a></li>';
		}
	}

	if (!empty($module->id) && exponent_permissions_check('delete_module', $cloc)) {
		$deletelink = $router->makeLink(array('module'=>'containermodule', 'id'=>$module->id, 'action'=>'delete', 'rerank'=>$rerank));
		$list .= '<li><a href="'.$deletelink.'" class="delete" onclick="alert(\''.expLang::gettext("This content is being sent to the Recycle Bin to be recovered later if you wish.").'\')">'.expLang::gettext("Remove Module").'</a></li>';
	}
	
	if (HELP_ACTIVE) {
		$helplink = help::makeHelpLink(getControllerName($module->info['class']));
		$list .= '<li><a href="'.$helplink.'" class="helplink" target="_blank">'.expLang::gettext("Get Help").'</a></li>';
	}
	
	$list .= '</ul>';

    expCSS::pushToHead(array(
	    "unique"=>"container-chrome",
	    "link"=>PATH_RELATIVE."framework/modules/container/assets/css/admin-container.css",
	    )
	);

    expJavascript::pushToFoot(array(
        "unique"=>'container-chrome',
        "yui3mods"=>'node',
        "src"=>PATH_RELATIVE."framework/core/assets/js/exp-container.js"
     ));
	

	echo $list;
}

?>
