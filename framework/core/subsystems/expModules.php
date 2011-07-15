<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expModules class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Phillip Ball <phillip@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expModules
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */

class expModules {

    public function listActiveControllers() {
        global $db;
        
        $controllers = listUserRunnableControllers();
        
        foreach ($controllers as $module) {
    		if (class_exists($module)) {
    			$mod = new $module();
    			$modstate = $db->selectObject("modstate","module='$module'");

    			$moduleInfo[$module] = null;
    			$moduleInfo[$module]->class = $module;
    			$moduleInfo[$module]->name = $mod->name();
    			$moduleInfo[$module]->author = $mod->author();
    			$moduleInfo[$module]->description = $mod->description();
    			$moduleInfo[$module]->codequality = isset($mod->codequality) ? $mod->codequality : 'alpha';
    			$moduleInfo[$module]->active = ($modstate != null ? $modstate->active : 0);
    		}
    	}
    	return $moduleInfo;
    }
    
    public function listActiveOSMods() {
        global $db;
        
        $osmods = expModules::exponent_modules_list();

        foreach ($osmods as $module) {
            if (class_exists($module)) {
                $mod = new $module();
                $modstate = $db->selectObject("modstate","module='$module'");

                if (!method_exists($mod,"dontShowInModManager")) {
                    $moduleInfo[$module] = null;
                    $moduleInfo[$module]->class = $module;
                    $moduleInfo[$module]->name = $mod->name();
                    $moduleInfo[$module]->author = $mod->author();
                    $moduleInfo[$module]->description = $mod->description();
                    $moduleInfo[$module]->active = ($modstate != null ? $modstate->active : 0);
                }
            }
        }
        return $moduleInfo;
    }
    
    
    public function exponent_modules_list() {
    	$mods = array();
    	if (is_readable(BASE."modules")) {
    		$dh = opendir(BASE."modules");
    		while (($file = readdir($dh)) !== false) {
    			if (substr($file,-6,6) == "module") $mods[] = $file;
    		}
    	}
    	return $mods;
    }
    
}
?>
