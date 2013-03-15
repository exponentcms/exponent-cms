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

class simplepoll_question extends expRecord {
    public $has_many = array(
        'simplepoll_answer',
        'simplepoll_timeblock'
    );

    public function afterDelete() {
        $sa = new simplepoll_answer();
        $answers = $sa->find('all','simplepoll_question_id='.$this->id);
        foreach ($answers as $answer) {
            $answer->delete();
        }
        $st = new simplepoll_timeblock();
        $tbs = $st->find('all','simplepoll_question_id='.$this->id);
        foreach ($tbs as $tb) {
            $tb->delete();
        }
    }

    public function toggle() {
        global $db;

        $db->toggle('simplepoll_question',"active",'active=1');
    }

}

?>