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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class fix_help_sections
 */
class fix_help_sections extends upgradescript
{
    protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.2.1';
    public $optional = true;
    public $priority = 52;

    /**
     * name/title of upgrade script
     * @return string
     */
    static function name()
    {
        return "Validate and fix help documents location info";
    }

    /**
     * generic description of upgrade script
     * @return string
     */
    function description()
    {
        return "In some instances, the help doc section entry doesn't match it's location data, and the rank entries are incorrect.  This script corrects that.";
    }

    /**
     * additional test(s) to see if upgrade script should be run
     * @return bool
     */
    function needed()
    {
        $help = new help();
        return $help->find('count');  // are there any help docs?
    }

    /**
     * Updates help doc section based on location_data entry
     *
     * @return bool
     */
    function upgrade()
    {
        global $db;

//        $bad = '';
        $fixed = 0;
        $help = new help();
        $hv = new help_version();
        foreach ($hv->findValue('all', 'id') as $version_id) {
            // build the help section list
            $sectionlist = array();
            $helplocs = $help->findValue('all', 'location_data', "help_version_id=" . $version_id, null, true);
            foreach ($helplocs as $helploc) {
                if (!empty($helploc)) {
                    $helpsrc = expUnserialize($helploc);
                    $sectionlist[$helpsrc->src] = $db->selectValue('sectionref', 'section', 'module = "help" AND source="' . $helpsrc->src . '"');
                }
            }

            // Fix help doc 'section' to match 'location_data'
//            $fixed = 0;
            $helpdocs = $help->find('all', "help_version_id=" . $version_id);
//            $bad .= '<strong>' . help_version::getHelpVersion($version_id) . ' with ' . count($helpdocs) . ' Total Help Docs</strong><br>';
            foreach ($helpdocs as $helpdoc) {
                if (!empty($helpdoc->loc) && !empty($sectionlist[$helpdoc->loc->src]) && $helpdoc->section != $sectionlist[$helpdoc->loc->src]) {
//                    $bad .= ' - ' . $helpdoc->title . ' - ' . $helpdoc->section . ' - ' . $sectionlist[$helpdoc->loc->src] . ' is bad<br>';
                    $helpdoc->section = $sectionlist[$helpdoc->loc->src];
                    $helpdoc->update();
                    $fixed++;
                }
            }
//            $bad .= '<strong>' . $fixed . ' Bad Docs</strong><br>';

            // Fix help doc ranks to be sequential beginning at 1
//        $bad .= '<strong>Doc Ranks</strong><br>';
            foreach ($helplocs as $helploc) {
                $helpdocs = $help->find('all', "help_version_id=" . $version_id . " AND location_data='" . $helploc . "'", 'rank' );
                $rank = 1;
//            $ranksarray = array();
                foreach ($helpdocs as $helpdoc) {
//                $ranksarray[] = $helpdoc->rank;
                    $helpdoc->rank = $rank++;
                    $helpdoc->update();
                }
//            $loc = expUnserialize($helploc);
//            if (!empty($sectionlist[$loc->src]))
//                $bad .= ' - ' . $db->selectValue( 'section', 'name', 'id="' . $sectionlist[$loc->src] . '"') . ' - ' . implode(',',$ranksarray) . '<br><br>';
            }
        }
//        return $bad;

        return ($fixed ? $fixed : gt('No')) . ' ' . gt('incorrect help docs were found and corrected, and then all were reranked.');
    }

}

?>
