<?php

##################################################
#
# Copyright (c) 2004-2020 OIC Group, Inc.
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

class eventdate extends expRecord {
    public $has_one = array('event');
    public $default_sort_field = 'date';

    public function afterDelete() {
        global $db;

        if (!$db->countObjects('eventdate','event_id='.$this->event_id)) {
            $ev = new event($this->event_id);
            $ev->delete();
        }
    }

}

?>