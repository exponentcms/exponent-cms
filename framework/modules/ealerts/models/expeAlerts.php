<?php
##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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

class expeAlerts extends expRecord {
    public $table = 'expeAlerts';

    public function __construct($params=array()) {
        global $db;
        if ((isset($params['module']) || isset($params['controller'])) && isset($params['src'])) {
            $mod = !empty($params['module']) ? $params['module'] : (!empty($params['controller']) ? $params['controller'] : null);
            $id = $db->selectValue($this->table, 'id', "module='".expModules::getModuleBaseName($mod)."' AND src='".$params['src']."'");
            parent::__construct($id, false, false);
        } else {
            parent::__construct($params, false, false);
        }
    }

    // we are going to override the build and beforeSave functions to
    // make sure the name of the controller is in the right format
    public function build($params=array()) {
        parent::build($params);
        if (!empty($this->module)) $this->module = expModules::getControllerName($this->module);
    }

    public function beforeSave() {
        if (!empty($this->module)) $this->module = expModules::getControllerName($this->module);
        parent::beforeSave();
    }

    public static function getPendingBySubscriber($id) {
        return self::getBySubscriber($id, true);
    }

    public static function getBySubscriber($id, $pending=false) {
        global $db;

        $enabled = empty($pending) ? 1 : 0;

        // find pending subscriptions  //FIXME, not pulling any items?
        $sql  = 'SELECT e.* FROM ' . $db->tableStmt('expeAlerts') . ' e ';
        $sql .= 'JOIN ' . $db->tableStmt('expeAlerts_subscribers') . ' es ON e.id=es.subscribers_id ';
        $sql .= 'WHERE es.enabled='.$enabled.' && es.subscribers_id='.$id;

        return $db->selectObjectsBySql($sql);
    }
}

?>