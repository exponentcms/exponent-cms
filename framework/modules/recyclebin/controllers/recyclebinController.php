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
 * @subpackage Controllers
 * @package Modules
 */

class recyclebinController extends expController {
//    public $useractions = array('showall'=>'Show all');
    public $add_permissions = array('show'=>'View Recycle Bin');
    //public $remove_permissions = array('edit');

    static function displayname() { return gt("Recycle Bin Manager"); }
    static function description() { return gt("Manage modules that have been deleted from your web pages"); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }
    static function hasSources() { return false; }
    static function hasContent() { return false; }

    function showall() {
        global $template;

        expHistory::set('manageable', $this->params);
        $orig_template = $template;

        //initialize a new recycle bin and grab the previously trashed items
        $bin = new recyclebin();
        $orphans = $bin->moduleOrphans(null);

        $template = $orig_template;
        assign_to_template(array(
            'items'=>$orphans
        ));
    }
    
    public function show() {
        global $template;
        
        $orig_template = $template;
        
        //instantiate an expRecord for the module in question
        //$mod = new $this->params['recymod']();
        define('SOURCE_SELECTOR',1);
        define('PREVIEW_READONLY',1); // for mods
        
        //initialize a new recycle bin and grab the previously trashed items
        $bin = new recyclebin();
        $orphans = $bin->moduleOrphans($this->params['recymod']);

        $template = $orig_template;
        assign_to_template(array(
            'items'=>$orphans,
            'module'=>$this->params['recymod']
        ));
    }

    /**
     * Permanently remove a module and all it's items from the system
     *
     */
    public function remove() {
        global $db;

        $mod = expModules::getController($this->params['mod'],$this->params['src']);
        $mod->delete_instance();  // delete all assoc items
        $db->delete('sectionref', "source='" . $this->params['src'] . "' and module='".$this->params['mod']."'");  // delete recycle bin holder
        expHistory::back();
    }
}

?>