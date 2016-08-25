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
 * @subpackage Models
 * @package Modules
 */

class sectionref extends expRecord {

    /**
     * Rebuild the sectionref table as a list of modules on a page
     */
    public static function rebuild_sectionrefs() {
        global $db;

        // recursive run though all the nested containers
        function scan_container($container_id, $page_id) {
            global $db;

            $containers = $db->selectObjects('container',"external='" . $container_id . "'");
            $ret = '';
            foreach ($containers as $container) {
                $iLoc = expUnserialize($container->internal);
                $newret = recyclebin::restoreFromRecycleBin($iLoc, $page_id);
                if (!empty($newret)) $ret .= $newret . '<br>';
                if ($iLoc->mod == 'container') {
                    $ret .= scan_container($container->internal, $page_id);
                }
            }
            return $ret;
        }

        // recursive run through all the nested pages
        function scan_page($parent_id) {
            global $db;

            $sections = $db->selectObjects('section','parent=' . $parent_id);
            $ret = '';
            foreach ($sections as $page) {
                $cLoc = serialize(expCore::makeLocation('container','@section' . $page->id));
                $ret .= scan_container($cLoc, $page->id);
                $ret .= scan_page($page->id);
            }
            return $ret;
        }

        // first remove duplicate records
        $db->sql('DELETE FROM ' . $db->prefix . 'sectionref WHERE id NOT IN (SELECT * FROM (SELECT MIN(n.id) FROM ' . $db->prefix . 'sectionref n GROUP BY n.module, n.source) x)');
        $ret = scan_page(0);  // the page hierarchy
        $ret .= scan_page(-1);  // now the stand alone pages

        // we need to get the non-main containers such as sidebars, footers, etc...
        $hardcodedmods = $db->selectObjects('sectionref',"refcount=1000 AND source NOT LIKE '%@section%' AND source NOT LIKE '%@random%'");
        foreach ($hardcodedmods as $hardcodedmod) {
            if ($hardcodedmod->module == 'container') {
                $page_id = intval(preg_replace('/\D/', '', $hardcodedmod->source));
                if (empty($page_id)) {
                    $page_id = SITE_DEFAULT_SECTION;  // we'll default to the home page
                }
                $ret .= scan_container(serialize(expCore::makeLocation($hardcodedmod->module, $hardcodedmod->source)), $page_id);
            } else {
                $hardcodedmod->section = 0;  // this is a hard-coded non-container module
                $db->updateObject($hardcodedmod, 'sectionref');
            }
        }

        // mark modules in the recycle bin as section 0
        $db->columnUpdate('sectionref', 'section', 0, "refcount=0");
//        $recycledmods = $db->selectObjects('sectionref',"refcount=0");
//        foreach ($recycledmods as $recycledmod) {
//            $recycledmod->section = 0;  // this is a module in the recycle bin
//            $db->updateObject($recycledmod, 'sectionref');
//        }
        return $ret;
    }

}

?>