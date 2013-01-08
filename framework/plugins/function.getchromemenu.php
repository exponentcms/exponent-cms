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
 * Smarty {getchromemenu} function plugin
 *
 * Type:     function<br>
 * Name:     getchromemenu<br>
 * Purpose:  display the chrome menu
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_getchromemenu($params,&$smarty) {
	global $router, $user;
	$cloc = $smarty->getTemplateVars('__loc');
	$module = $params['module'];

	$list = '<ul class="container-menu">';
	$list .= '<li class="container-info">'.gt($module->action).' / '.gt(str_replace($module->action.'_','',$module->view)).'</li>';

	$rerank = $params['rerank'];
	if ($rerank == 'false') {
		$rerank = 0;
	} else {
		$rerank = 1;
	}
	
	if ($user->isAdmin()) {
		$userlink = $router->makeLink(array('module'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'userperms', '_common'=>1));
		$grouplink = $router->makeLink(array('module'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'groupperms', '_common'=>1));
		$list .= '<li><a href="'.$userlink.'" class="user">'.gt("User Permissions").'</a></li>';
		$list .= '<li><a href="'.$grouplink.'" class="group">'.gt("Group Permissions").'</a></li>';
	}

    echo $list;
    $list = '';
	if (!empty($params['rank']) && $module->info['class'] == 'containermodule' && expPermissions::check('configure', $cloc)) {
        foreach ($smarty->smarty->plugins_dir as $value) {
            $filepath = $value ."/function.ddrerank.php";
            if (file_exists($filepath)) {
                require_once $filepath;
                break;
            }
        }
        $reorder = array();
        $reorder['module'] = "container";
        $reorder['where'] = "external='".$module->internal."'";
        $reorder['label'] = gt("Modules");
        echo '
        <li>';
        smarty_function_ddrerank($reorder, $smarty);
        echo '</li>
        ';

//		$uplink = $router->makeLink(array('module'=>'containermodule','src'=>$cloc->src,'action'=>'order','a'=>$params['rank'] - 2,'b'=>$params['rank'] - 1));
//		$downlink = $router->makeLink(array('module'=>'containermodule','src'=>$cloc->src,'action'=>'order', 'a'=>$params['rank'] - 1,'b'=>$params['rank']));
//		if ($params['rank'] != 1) {	//dont show this up arrow if it's the first module in a container
//			$list .= '<li><a href="'.$uplink.'" class="mod-up">'.gt("Move Module Up").'</a></li>';
//		}
//		if (!$params['last']) { //if this is the last module in a container don't show down arrow.
//			$list .= '<li><a href="'.$downlink.'" class="mod-down">'.gt("Move Module Down").'</a></li>';
//		}
	}

	if (!empty($module->id) && expPermissions::check('edit', $cloc) && $module->permissions['manage'] == 1) {
        if (!expModules::controllerExists($module->info['class'])) {
            $editlink = $router->makeLink(array('module'=>'containermodule', 'id'=>$module->id, 'action'=>'edit', 'src'=>$module->info['source']));
            $list .= '<li><a href="'.$editlink.'" class="config-view">'.gt("Configure Action")." &amp; ".gt("View").'</a></li>';
        }
	}

	if ($module->permissions['configure'] == 1) {
		if (expModules::controllerExists($module->info['class'])) {
            if (!empty($params['hcview'])) {
                $hcview = $module->view;
            } else {
                $hcview = null;
            }
			$configlink = $router->makeLink(array('module'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'configure', 'hcview'=>$hcview));
			$list .= '<li><a href="'.$configlink.'" class="config-mod">'.gt("Configure Settings").'</a></li>';
		} elseif ($module->info['hasConfig']) {
			$configlink = $router->makeLink(array('module'=>$module->info['class'], 'src'=>$module->info['source'], 'action'=>'configure', '_common'=>1));
			$list .= '<li><a href="'.$configlink.'" class="config-mod">'.gt("Configure Settings").'</a></li>';
		}
	}

	if (!empty($module->id) && expPermissions::check('delete', $cloc)) {
		$deletelink = $router->makeLink(array('module'=>'containermodule', 'id'=>$module->id, 'action'=>'delete', 'rerank'=>$rerank));
		$list .= '<li><a href="'.$deletelink.'" class="delete" onclick="alert(\''.gt("This content is being sent to the Recycle Bin to be recovered later if you wish.").'\')">'.gt("Remove Module").'</a></li>';
	}
	
	if (HELP_ACTIVE) {
		$helplink = help::makeHelpLink(expModules::getControllerName($module->info['class']));
		$list .= '<li><a href="'.$helplink.'" class="helplink" target="_blank">'.gt("Get Help").'</a></li>';
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
        "src"=>JS_RELATIVE."exp-container.js"
     ));
	
	echo $list;
}

?>
