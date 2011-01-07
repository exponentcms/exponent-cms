<?php
/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @package    Framework
 * @subpackage Subsystems
 * @author     Adam Kessler <adam@oicgroup.net>
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @version    Release: @package_version@
 * @link       http://www.exponent-docs.org/api/package/PackageName
 */

class expBot {
    public $url = '';
    public $method = 'GET';
    
    public function __construct($params) {
        $this->url = isset($params['url']) ? $params['url'] : '';
        //$this->method = isset($params['method']) ? $params['method'] : '';
    }
    
    public function fire() {
        $convo  = $this->method." ".$this->url."&ajax_action=1 HTTP/1.1\r\n";
        if ($this->method == 'POST') $convo .= "Content-Type: multipart/form-data";
        $convo .= "Host: " . HOSTNAME . "\r\n";
        $convo .= "User-Agent:  ExponentCMS/".EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR.".".EXPONENT_VERSION_REVISION."  Build/".EXPONENT_VERSION_ITERATION." PHP/".phpversion()."\r\n";
        $convo .= "Connection: Close\r\n\r\n";
        
        try {
            $theSpawn = fsockopen(HOSTNAME, 80);
            try {
            	fwrite ($theSpawn, $convo);
                sleep(1);
                fclose($theSpawn);
            } catch (Exception $error) {
                eLog("Error writing to socket: <br />",'','',1);
                eLog($error->getMessage(),'','',1);
            }
        } catch (Exception $error) {
            eLog("Error opening socket: <br />",'','',1);
            eLog($error->getMessage(),'','',1);
        }
    }
}
