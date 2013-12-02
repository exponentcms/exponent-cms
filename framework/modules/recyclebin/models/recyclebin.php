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
 * @package    Modules
 */

class recyclebin extends expRecord
{
    public $table = 'sectionref';

    //public $validates = '';

    /**
     * Return list of all modules in the recycle bin
     *
     * @param $module
     *
     * @return array
     */
    public function moduleOrphans($module)
    {
        global $db;

        // we only want a preview, not an admin view
        $level = 99;
        if (expSession::is_set('uilevel')) {
            $level = expSession::get('uilevel');
        }
        expSession::set("uilevel", UILEVEL_PREVIEW);

        if (empty($module)) {
            $orphans = $db->selectObjects($this->table, 'refcount = 0 AND source!=\'\' ORDER BY module');
        } else {
            $orphans = $db->selectObjects(
                $this->table,
                'refcount = 0 AND source!=\'\' AND module=\'' . expModules::getModuleName($module) . '\''
            );
        }
        $loc = null;

        //foreach ($orphans as $orphan) {
        $numrecycled = count($orphans);
        for ($i = 0; $i < $numrecycled; $i++) {
            $loc = expCore::makeLocation($orphans[$i]->module, $orphans[$i]->source, $orphans[$i]->internal);
            $orphans[$i]->loc = serialize($loc);
            if ($orphans[$i]->module == 'recyclebin') {
                unset($orphans[$i]);
            } else {
//                if (expModules::controllerExists($orphans[$i]->module)) {
                $orphans[$i]->html = renderAction(
                    array(
                        'controller' => $orphans[$i]->module,
                        'action'     => 'showall',
                        'src'        => $orphans[$i]->source,
                        "no_output"  => true
                    )
                );
//                } else {
//                    echo($module).'...';
//                    if (in_array($orphans[$i]->module,expModules::modules_list())) {
//                        $mod = new $orphans[$i]->module();
//                        ob_start();
//                        $mod->show("Default",$loc);
//                        $orphans[$i]->html = ob_get_contents();
//                        ob_end_clean();
//                    } else {
//                        echo($orphans[$i]->module . ' ' . gt('no longer available!'));
//                    }
//                }
            }
        }
        expSession::set("uilevel", $level);

        return $orphans;
    }

    /** exdoc
     * Decrement the reference count for a given sectionref location.  This is used by the Container and Navigation Modules,
     * and probably won't be needed by 95% of the code in Exponent.
     *
     * @param object  $loc     The location object to decrement references for.
     * @param integer $section The id of the section that the location exists in.
     */
    public static function sendToRecycleBin($loc, $section)
    {
        global $db;

        //FIXME we should only send module with sources or configs to the recycle bin NOT things like navigation or rss
        if ($loc->mod != 'container') {
            $oldSecRef = $db->selectObject(
                "sectionref",
                "module='" . $loc->mod . "' AND source='" . $loc->src . "' AND internal='" . $loc->int . "'"
            );
            $oldSecRef->section = 0;
            $oldSecRef->refcount = 0;
            $db->updateObject(
                $oldSecRef,
                "sectionref",
                "module='" . $loc->mod . "' AND source='" . $loc->src . "' AND internal='" . $loc->int . "'"
            );
        } else {
            // send contained modules to recycle bin
            $modules = $db->selectObjects('container', "external='" . serialize($loc) . "'");
            foreach ($modules as $module) {
                $obj = new container($module->id);
                $obj->delete();
//                $modloc = expUnserialize($module->internal);
//                self::sendToRecycleBin($modloc, $section);
//                // then remove the container table reference
//                $db->delete('container', "internal='".$module->internal."'");
            }
            $db->delete('sectionref', "source='" . $loc->src . "' and module='container'");
        }
    }

    /** exdoc
     * Increment the reference count for a given sectionref location.  This is used by the Container Module,
     * and probably won't be needed by 95% of the code in Exponent.
     *
     * @param object  $loc     The location object to increment references for.
     * @param integer $section The id of the section that the location exists in.
     *
     * @return string
     */
    public static function restoreFromRecycleBin($loc, $section)
    {
        global $db;

        $newSecRef = $db->selectObject(
            "sectionref",
            "module='" . $loc->mod . "' AND source='" . $loc->src . "' AND internal='" . $loc->int . "'"
        );
        $ret = '';
        if ($newSecRef != null) {
            // Pulled an existing source for this section.  Update refcount
            if ($newSecRef->section != $section) {
                $ret = 'Changed sectionref entry for ' . $loc->mod . ' - ' . $loc->src . ' from section ' . $newSecRef->section . ' to section ' . $section;
            }
            $newSecRef->section = $section; // we need to do this for pulling stuff from the recycle bin
            $newSecRef->refcount = 1; // we need to do this for pulling stuff from the recycle bin
            $db->updateObject(
                $newSecRef,
                "sectionref",
                "module='" . $loc->mod . "' AND source='" . $loc->src . "' AND internal='" . $loc->int . "'"
            );
        } else {
            // New source for this section.  Populate reference
            $newSecRef = new stdClass();
            $newSecRef->module = $loc->mod;
            $newSecRef->source = $loc->src;
            $newSecRef->internal = $loc->int;
            $newSecRef->section = $section;
            $newSecRef->refcount = 1;
            $db->insertObject($newSecRef, "sectionref");
            $ret = 'New sectionref entry for ' . $loc->mod . ' - ' . $loc->src . ' on section ' . $section;

        }
        return $ret;
    }

}

?>