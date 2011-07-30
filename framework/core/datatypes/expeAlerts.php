<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expAlerts class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expAlerts
 *
 * @subpackage Core-Datatypes
 * @package Framework
 */
 
class expeAlerts extends expRecord {
    public $table = 'expeAlerts';
    
    public function __construct($params=array()) {
        global $db;
        if (isset($params['module']) && isset($params['src'])) {
            $id = $db->selectValue($this->table, 'id', "module='".$params['module']."' AND src='".$params['src']."'");
            parent::__construct($id, false, false);
        } else {
            parent::__construct($params, false, false);
        }
    }
    
    // we are going to override the build and beforeSave functions to
    // make sure the name of the controller is in the right format
    public function build($params=array()) {
        parent::build($params);
        $this->module = getControllerName($this->module);
    }
    
    public function beforeSave() {
        $this->module = getControllerName($this->module);
        parent::beforeSave();
    }
    
    public static function getPendingBySubscriber($id) {
        expeAlerts::getBySubscriber($id, true);
    }
    
    public static function getBySubscriber($id, $pending=false) {
        global $db;
        
        $enabled = empty($pending) ? 1 : 0;
        
        // find pending subscriptions        
        $sql  = 'SELECT e.* FROM '.DB_TABLE_PREFIX.'_expeAlerts e ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_expeAlerts_subscribers es ON e.id=es.subscribers_id ';
        $sql .= 'WHERE es.enabled='.$enabled.' && es.subscribers_id='.$id;

        return $db->selectObjectsBySql($sql);
    }
}

?>
