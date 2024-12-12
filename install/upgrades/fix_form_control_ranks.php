<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * This is the class fix_form_control_ranks
 *
 * @package Installation
 * @subpackage Upgrade
 */
class fix_form_control_ranks extends upgradescript
{
    protected $from_version = '2.3.9';
//	protected $to_version = '2.4.2';
//    public $optional = true;
//    public $priority = 52;

    /**
     * name/title of upgrade script
     * @return string
     */
    static function name()
    {
        return "Correct the rank order of Form Controls";
    }

    /**
     * generic description of upgrade script
     * @return string
     */
    function description()
    {
        return "The Form Designer control reordering logic was flawed.  This script corrects that.";
    }

    /**
     * additional test(s) to see if upgrade script should be run
     * @return bool
     */
    function needed()
    {
        $f = new forms();
        $fc = new forms_control();
        $fixed = 0;
        foreach ($f->findValue('all', 'id') as $form_id) {
            $controls = $fc->find('all', "forms_id=" . $form_id, 'rank');
            $rank = 1;
            foreach ($controls as $control) {
                if ($control->rank != $rank) {
                    $fixed++;
                }
                $rank++;
            }
        }

        return $fixed > 0;  // are there any form controls?
    }

    /**
     * Corrects form control ranking
     *
     * @return string
     */
    function upgrade()
    {
        $f = new forms();
        $fc = new forms_control();
        $fixed = 0;
        foreach ($f->findValue('all', 'id') as $form_id) {
            $controls = $fc->find('all', "forms_id=" . $form_id, 'rank');
            // Fix control ranks to be sequential beginning at 1
            $rank = 1;
            foreach ($controls as $control) {
                if ($control->rank != $rank) {
                    $fixed++;
                    $control->rank = $rank;
                    $control->update();
                }
                $rank++;
            }
        }

        return ($fixed ? $fixed : gt('No')) . ' ' . gt('form controls were reranked.');
    }

}

?>
