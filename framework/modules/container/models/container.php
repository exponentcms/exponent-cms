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

class container extends expRecord {

    public $table = 'container';

    public function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc, $get_attached);
        $this->grouping_sql = " AND external='".$params['external']."'";
    }

    public function update($params = array()) {
        if (!isset($params['id'])) {
            if (empty($params['existing_source'])) {
                $src = "@random".uniqid("");
            } else {
                $src = $params['existing_source'];
            }

            // set the location data for the new module/controller
            $newInternal = expCore::makeLocation($params['modcntrol'],$src);
            $params['internal'] = serialize($newInternal);
            // make sure we're not in the recycle bin
            recyclebin::restoreFromRecycleBin($newInternal,intval($params['current_section']));
        }
        $this->grouping_sql = " AND external='".$params['external']."'";
        parent::update($params);
    }

    // from container model delete
    public function delete($where = '') {
        $internal = unserialize($this->internal);
        recyclebin::sendToRecycleBin($internal,expSession::get("last_section"));  // send it to recycle bin first
        parent::delete("internal='" . $this->internal . "'");  // param is for reranking remaining objects
        expSession::clearAllUsersSessionCache('containers');
    }

//    /**
//     * rerank method since we don't have a location_data field
//     *
//     * @param        $direction
//     * @param string $where
//     */
//    public function rerank($direction, $where = '') {
//        global $db;
//        if (!empty($this->rank)) {
//            $next_prev = $direction == 'up' ? $this->rank - 1 : $this->rank + 1;
//            $where = "internal='" . $this->internal . "'";
//            $db->switchValues($this->tablename, 'rank', $this->rank, $next_prev, $where);
//        }
//    }

}

?>
