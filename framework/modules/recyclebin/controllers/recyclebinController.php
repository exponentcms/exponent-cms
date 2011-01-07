<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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

class recyclebinController extends expController {
    //public $basemodel_name = '';
    //public $useractions = array('showall'=>'Show all');
    //public $add_permissions = array('show'=>'View Links');
    //public $remove_permissions = array('edit');

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Recycle Bin Manager"; }
    function description() { return "Manage modules that have been deleted from your web pages"; }
    function author() { return "Phillip Ball - OIC Group, Inc"; }
    function hasSources() { return false; }
    function hasViews() { return true; }
    function hasContent() { return false; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }

    function showall() {
    }
    
    public function show() {
        global $db, $template;
        
        $orig_template = $template;
        
        //instantiate an expRecord for the module in question
        //$mod = new $this->params['recymod']();
        define('SOURCE_SELECTOR',1);
        define('PREVIEW_READONLY',1); // for mods
        
        //initialize a new recycle bin and grab the previously trashed items
        $bin = new recyclebin();
        $orphans = $bin->moduleOrphans($this->params['recymod']);

        $template = $orig_template;
        assign_to_template(array('items'=>$orphans,'module'=>$this->params['recymod']));
    }
    
}

?>
