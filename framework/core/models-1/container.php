<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

class container {
    function form($object,$modules_list = null) {
    }
    
    static function update($values,$object,$loc) {
        global $db;
        
        // check if this is a controller or module
        $iscontroller = controllerExists($values['i_mod']);
        if (!isset($values['id'])) {
            // Only deal with the inc/dec stuff if adding a module.
            $src = "";
            if (empty($values['existing_source'])) {
                $src = "@random".uniqid("");
                $object->is_existing = 0;
                $object->is_existing = 0;
            } else {
                $src = $values['existing_source'];
                $object->is_existing = 1;
            }
        
            // set the location data for the new module/controller
            $newInternal = expCore::makeLocation($values['modcntrol'],$src);

            // REFERENCES - Section and Location
            //$sect = $db->selectObject('section','id='.$_POST['current_section']);
            expCore::incrementLocationReference($newInternal,intval($_POST['current_section']));
            
            // Rank is only updateable from the order action
            $object->rank = $values['rank'];
//            if (isset($values['rerank'])) $db->increment("container","rank",1,"external='".serialize($loc)."' AND rank >= " . $values['rank']);
			if ((isset($values['rerank'])) && ($values['rerank'])) $db->increment("container","rank",1,"external='".serialize($loc)."' AND rank >= " . $values['rank']);
            $object->internal = serialize($newInternal);
            $object->external = serialize($loc);
        }
        
        $object->is_private = (isset($_POST['is_private']) ? 1 : 0);
        // UPDATE the container
        $object->action = isset($values['actions']) ? $values['actions'] : null;
        //$object->view = $iscontroller ? $values['ctlview'] : $values['view'];
        $object->view = $values['views'];
        $object->title = $values['title'];
        return $object;
    }
    
    static function delete($object,$rerank = false) {
        if ($object == null) return false;
        
        $internal = unserialize($object->internal);
        
        global $db;
        $section = expSession::get("last_section");
//        $locref = $db->selectObject("locationref","module='".$internal->mod."' AND source='".$internal->src."' AND internal='".$internal->int."'");
        $secref = $db->selectObject("sectionref", "module='".$internal->mod."' AND source='".$internal->src."' AND internal='".$internal->int."' AND section=$section");
        
        // if ($locref) {
            // $locref->refcount -= 1;
            // $db->updateObject($locref,"locationref","module='".$internal->mod."' AND source='".$internal->src."' AND internal='".$internal->int."'");
        // }
        
        if ($secref) {
            $secref->refcount -= 1;
            $db->updateObject($secref,"sectionref", "module='".$internal->mod."' AND source='".$internal->src."' AND internal='".$internal->int."' AND section=$section");
        }
        
        // Fix ranks
        if ($rerank) $db->decrement("container","rank",1,"external='".$object->external."' AND rank > " . $object->rank);
    }
}

?>
