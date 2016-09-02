<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
class recyclebinController extends expController
{
    protected $add_permissions = array(
        'showall' => 'View Recycle Bin',
        'show' => 'View Recycle Bin',
        'remove' => 'Remove Recycle Bin Item'
    );

    //protected $remove_permissions = array('edit');

    static function displayname()
    {
        return gt("Recycle Bin Manager");
    }

    static function description()
    {
        return gt("Manage modules that have been deleted from your web pages");
    }

    static function author()
    {
        return "Phillip Ball - OIC Group, Inc";
    }

    static function hasSources()
    {
        return false;
    }

    static function hasContent()
    {
        return false;
    }

    function showall()
    {
        expHistory::set('manageable', $this->params);

        //initialize a new recycle bin and grab the previously trashed items
        $bin = new recyclebin();
        $orphans = $bin->moduleOrphans();

        assign_to_template(
            array(
                'items' => $orphans
            )
        );
    }

    public function show()
    {
        //instantiate an expRecord for the module in question
        //$mod = new $this->params['recymod']();
        define('SOURCE_SELECTOR', 1);
        define('PREVIEW_READONLY', 1); // for mods

        //initialize a new recycle bin and grab the previously trashed items
        $bin = new recyclebin();
        $orphans = $bin->moduleOrphans($this->params['recymod']);

        assign_to_template(
            array(
                'items' => $orphans,
                'module' => $this->params['recymod']
            )
        );
    }

    /**
     * Permanently remove a module from the recycle bin and all it's items from the system
     *
     */
    public function remove()
    {
        global $db;

        $mod = expModules::getController($this->params['mod'], $this->params['src']);
        if ($mod != null) {
            $mod->delete_instance();  // delete all assoc items
            $db->delete(
                'sectionref',
                "source='" . $this->params['src'] . "' and module='" . $this->params['mod'] . "'"
            );  // delete recycle bin holder
            flash('notice', gt('Item removed from Recycle Bin'));
        }
        expHistory::back();
    }

    /**
     * Permanently remove all modules from the recycle bin and all their items from the system
     *
     */
    public function remove_all()
    {
        global $db;

        $bin = new recyclebin();
        $orphans = $bin->moduleOrphans();
        foreach ($orphans as $orphan) {
            $mod = expModules::getController($orphan->module, $orphan->source);
            if ($mod != null) {
                $mod->delete_instance();  // delete all assoc items
                $db->delete(
                    'sectionref',
                    "source='" . $orphan->source . "' and module='" . $orphan->module . "'"
                );  // delete recycle bin holder
            }
        }
        flash('notice', gt('Recycle Bin has been Emptied'));
        expHistory::back();
    }

}

?>