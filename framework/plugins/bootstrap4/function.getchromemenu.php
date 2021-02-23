<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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

if (!function_exists('smarty_function_getchromemenu')) {
    /**
     * Smarty {getchromemenu} function plugin
     *
     * Type:     function<br>
     * Name:     getchromemenu<br>
     * Purpose:  display the chrome menu
     *
     * @param         $params
     * @param \Smarty $smarty
     *
     * @package    Smarty-Plugins
     * @subpackage Function
     */
    function smarty_function_getchromemenu($params,&$smarty) {
        global $router, $user;
        $cloc = $smarty->getTemplateVars('__loc');
        $module = $params['module'];

        $list = '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu' . $module->id . '">';

        if (!empty($params['hcview'])) $list .= '<li role="presentation" class="dropdown-header">('.ucwords(gt($module->info['scope'])).')</li>';
        $list .= '<li role="presentation" class="dropdown-header">'.gt($module->action).' / '.gt(str_replace($module->action.'_','',$module->view)).'</li>';
        $list .= '<li class="dropdown-divider"></li>';

        $rerank = $params['rerank'];
        if ($rerank == 'false') {
            $rerank = 0;
        } else {
            $rerank = 1;
        }

        // does it need permissions menu items?
        if ($user->isAdmin() && !SIMPLE_PERMISSIONS) {
    //		$userlink = $router->makeLink(array('module'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'userperms', '_common'=>1));
    //		$grouplink = $router->makeLink(array('module'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'groupperms', '_common'=>1));
            $userlink = $router->makeLink(array('controller'=>'users', 'action'=>'userperms', 'mod'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source']));
            $grouplink = $router->makeLink(array('controller'=>'users', 'action'=>'groupperms', 'mod'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source']));
            $list .= '<li role="menuitem"><a href="'.$userlink.'" class="dropdown-item"><i class="fas fa-user fa-fw"></i> '.gt("User Permissions").'</a></li>';
            $list .= '<li role="menuitem"><a href="'.$grouplink.'" class="dropdown-item"><i class="fas fa-users fa-fw"></i> '.gt("Group Permissions").'</a></li>';
        }

        echo $list;
        $list = '';
        // does it need a reorder modules menu item?
        if (!empty($params['rank']) && ($module->info['class'] == 'containerController') && expPermissions::check('configure', $cloc)) {
             //load the {ddrerank} plugin
            foreach ($smarty->smarty->plugins_dir as $value) {
                $filepath = $value ."/function.ddrerank.php";
                if (file_exists($filepath)) {
                    require_once $filepath;
                    break;
                }
            }
            $reorder = array();
            $loc = expUnserialize($module->internal);
            $reorder['id'] = $loc->src;
            $reorder['module'] = "container";
            $reorder['model'] = "container";
            $reorder['where'] = "external='".$module->internal."'";
            $reorder['label'] = gt("Modules");
            echo '
            <li role="menuitem">';
            smarty_function_ddrerank($reorder, $smarty);
            echo '</li>
            ';
        }

        // does it need an old school configure action & view menu item?
        if (!empty($module->id) && expPermissions::check('edit', $cloc) && $module->permissions['manage'] == 1) {
            if (!expModules::controllerExists($module->info['class'])) {
    //            $editlink = $router->makeLink(array('module'=>'containermodule', 'id'=>$module->id, 'action'=>'edit', 'src'=>$module->info['source']));
                $editlink = $router->makeLink(array('controller'=>'container', 'id'=>$module->id, 'action'=>'edit', 'src'=>$module->info['source']));
                $list .= '<li role="menuitem"><a href="'.$editlink.'" class="dropdown-item"><i class="fas fa-cogs fa-fw"></i> '.gt("Configure Action")." &amp; ".gt("View").'</a></li>';
            }
        }

        // does it need a configure settings menu item?
        if ($module->permissions['configure'] == 1) {
            if (expModules::controllerExists($module->info['class'])) {
                if (!empty($params['hcview'])) {
                    $hcview = $module->view;
                } else {
                    $hcview = null;
                }
                $configlink = $router->makeLink(array('module'=>expModules::getControllerName($module->info['class']), 'src'=>$module->info['source'], 'action'=>'configure', 'hcview'=>$hcview));
                $list .= '<li role="menuitem"><a href="'.$configlink.'" class="config-mod dropdown-item"><i class="fas fa-cog fa-fw"></i> '.gt("Configure Settings").'</a></li>';
    //		} elseif ($module->info['hasConfig']) {  // old school module
    //			$configlink = $router->makeLink(array('module'=>$module->info['class'], 'src'=>$module->info['source'], 'action'=>'configure', '_common'=>1));
    //			$list .= '<li class="dropdown-item" role="menuitem"><a href="'.$configlink.'" class="config-mod">'.gt("Configure Settings").'</a></li>';
            }
        }

        // does it need a delete module menu item?
        if (!empty($module->id) && expPermissions::check('delete', $cloc)) {
    //		$deletelink = $router->makeLink(array('module'=>'containermodule', 'id'=>$module->id, 'action'=>'delete', 'rerank'=>$rerank));
            $deletelink = $router->makeLink(array('controller'=>'container', 'id'=>$module->id, 'action'=>'delete', 'rerank'=>$rerank));
            $list .= '<li role="menuitem"><a href="'.$deletelink.'" class="dropdown-item" onclick="return confirm(\''.gt("Remove this module and send the content to the Recycle Bin to be recovered later?").'\')"><i class="fas fa-times fa-fw"></i> '.gt("Remove Module").'</a></li>';
        }

        // does it need a help menu item?
        if (HELP_ACTIVE) {
            $helplink = help::makeHelpLink(expModules::getControllerName($module->info['class']));
            $list .= '<li role="menuitem"><a href="'.$helplink.'" class="dropdown-item" target="_blank"><i class="fas fa-question fa-fw"></i> '.gt("Get Help").'</a></li>';
        }

        $list .= '</ul>';

        echo $list;
    }
}

?>
