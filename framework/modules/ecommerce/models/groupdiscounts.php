<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * @package Core
 */
class groupdiscounts extends expRecord {
    public $table = 'groupdiscounts';
    public $has_one = array(
        'group',
        'discounts',
    );

    public $validates = array(
        'presence_of'=>array(
            'group_id'=>array('message'=>'You must select a group to apply this discount to.'),
            'discounts_id'=>array('message'=>'You must select a discount')
        ));
        
    public static function getGroupDiscountsForUser() {
        global $db, $user;

        if (!$user->isLoggedIn()) return false;
        
        $sql  = 'SELECT g.id as group_id, g.name as discount_name, gd.dont_allow_other_discounts as dont_allow_other_discounts, d.* ';
        $sql .= 'FROM '.DB_TABLE_PREFIX.'_group g JOIN '.DB_TABLE_PREFIX.'_groupmembership gm ';
        $sql .= 'ON g.id=gm.group_id JOIN '.DB_TABLE_PREFIX.'_groupdiscounts gd ON gd.group_id=gm.group_id ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_discounts d ON d.id=gd.discounts_id WHERE gm.member_id='.$user->id;
        $sql .= ' ORDER BY gd.rank ASC';
            
        return $db->selectObjectsBySql($sql);
    }
        
}

?>