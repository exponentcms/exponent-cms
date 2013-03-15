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
 * @subpackage Models
 * @package Modules
 */

class recyclebin extends expRecord {
    public $table = 'sectionref';
    //public $validates = '';
    public function moduleOrphans($module) {
        global $db;
        if (empty($module)) {
            $orphans = $db->selectObjects($this->table,'refcount = 0 AND source!=\'\' ORDER BY module');
        } else {
            $orphans = $db->selectObjects($this->table,'refcount = 0 AND source!=\'\' AND module=\''.expModules::getModuleName($module).'\'');
        }
        $loc =null;

        //foreach ($orphans as $orphan) {
        $numrecycled = count($orphans);
        for($i=0; $i<$numrecycled; $i++) {
//            $loc = new stdClass();
//            $loc->mod = $orphans[$i]->module;
//            $loc->src = $orphans[$i]->source;
//            $loc->int = $orphans[$i]->internal;
            $loc = expCore::makeLocation($orphans[$i]->module,$orphans[$i]->source,$orphans[$i]->internal);
            $orphans[$i]->loc = serialize($loc);
            if ($orphans[$i]->module == 'recyclebin') {
                unset($orphans[$i]);
            } else {
                if (expModules::controllerExists($orphans[$i]->module)) {
                    $orphans[$i]->html = renderAction(array('controller'=>$orphans[$i]->module, 'action'=>'showall','src'=>$orphans[$i]->source,"no_output"=>true));
                } else {
                    echo($module).'...';
                    if (in_array($orphans[$i]->module,expModules::modules_list())) {
                        $mod = new $orphans[$i]->module();
                        ob_start();
                        $mod->show("Default",$loc);
                        $orphans[$i]->html = ob_get_contents();
                        ob_end_clean();
                    } else {
                        echo($orphans[$i]->module . ' ' . gt('no longer available!'));
                    }
                }
            }
        }
        
        return $orphans;
    }

    /** exdoc
     * Decrement the reference count for a given location.  This is used by the Container Module,
     * and probably won't be needed by 95% of the code in Exponent.
     *
     * @param object $loc The location object to decrement references for.
     * @param integer $section The id of the section that the location exists in.
     */
    public static function sendToRecycleBin($loc,$section) {
        global $db;
        $oldSecRef = $db->selectObject("sectionref", "module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
        $oldSecRef->refcount = 0;
        $db->updateObject($oldSecRef,"sectionref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
    }

    /** exdoc
     * Increment the reference count for a given location.  This is used by the Container Module,
     * and probably won't be needed by 95% of the code in Exponent.
     *
     * @param object $loc The location object to increment references for.
     * @param integer $section The id of the section that the location exists in.
     */
    public static function restoreFromRecycleBin($loc,$section) {
        global $db;

        $newSecRef = $db->selectObject("sectionref", "module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
        if ($newSecRef != null) {
            // Pulled an existing source for this section.  Update refcount
               $newSecRef->refcount = 1;  // we need to do this for pulling stuff from the recycle bin?
            $db->updateObject($newSecRef,"sectionref","module='".$loc->mod."' AND source='".$loc->src."' AND internal='".$loc->int."' AND section=$section");
        } else {
            // New source for this section.  Populate reference
            $newSecRef = new stdClass();
            $newSecRef->module   = $loc->mod;
            $newSecRef->source   = $loc->src;
            $newSecRef->internal = $loc->int;
            $newSecRef->section = $section;
            $newSecRef->refcount = 1;
            $db->insertObject($newSecRef,"sectionref");
        }
    }

}

?>