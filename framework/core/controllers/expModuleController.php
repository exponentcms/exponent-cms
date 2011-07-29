<?php
/**
 *  This file is part of Exponent
 * 
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expModuleController class.
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expModuleController
 *
 * @subpackage Core-Controllers
 * @package Framework
 */

class expModuleController extends expController {

    function displayname() { return "Modules"; }
    function description() { return "Manages all things about Exponent Modules"; }
    function author() { return "Phillip Ball"; }
    function hasSources() { return false; }
	function hasContent() { return false; }
    
    function manage () {
        $controllers = expModules::listActiveControllers();
        $old_school_mods = expModules::listActiveOSMods();
        assign_to_template(array('controllers'=>$controllers,'old_school_mods'=>$old_school_mods));
    }

    function update () {
        global $db;
        //$db->delete('modstate');
        
        $aMods = $db->selectObjects('modstate',1);
        		        
        foreach ($aMods as $key => $value) {
            if (!empty($this->params['mods']) && array_key_exists($value->module,$this->params['mods'])) {
                $aMods[$key]->active = $this->params['mods'][$value->module];
                $db->updateObject($aMods[$key],'modstate',"module='".$value->module."'");
            } else {
                $aMods[$key]->active = 0;
                $db->updateObject($aMods[$key],'modstate',"module='".$value->module."'");
            }
            unset($this->params['mods'][$value->module]);
        }
        
        if (!empty($this->params['mods'])) {
            foreach ($this->params['mods'] as $key => $value) {
                $aMod->module = $key;
                $aMod->active = $value;
                $db->insertObject($aMod,'modstate');
            }
        }
        flash("message", "Active Modules have been updated.");
        expHistory::returnTo('editable');
    }

}

?>