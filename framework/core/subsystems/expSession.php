<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expSession class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expSessionr
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */

class expSession {
    /**
     * set
     *
     * @param unknown $var
     * @param unknown $val
     * @return void
     */
    public function set($var, $val) {
        exponent_sessions_set($var, $val);
    }
    
    /**
     * get
     *
     * @param unknown $var
     * @return void
     */
    public function get($var) {
        return exponent_sessions_get($var);
    }

    public function exists($var) {
        return isset($_SESSION[SYS_SESSION_KEY]['vars'][$var]);
    }

    public function deleteVar($var) {
        exponent_sessions_unset($var);
    }
    
    public function setCache($params=array()) {
        $_SESSION[SYS_SESSION_KEY]['cache'][$params['module']] = $params['val'];
    }
    
    public function setTableCache($tablename, $desc) {
        $_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename] = $desc;
    }
    
    public function getTableCache($tablename) {
        if (isset($_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename])) {
            return $_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename];
        } else {
            return null;
        }
    }
    
    public function issetTableCache($tablename) {
        return isset($_SESSION[SYS_SESSION_KEY]['cache']['table_descriptions'][$tablename]) ? true : false;
    }
    
    public function clearUserCache() {
        exponent_sessions_clearCurrentUserSessionCache();
    }
}

?>
