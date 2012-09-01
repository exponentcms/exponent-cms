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
 * This is the class expBot
 *
 * @package Subsystems
 * @subpackage Subsystems
 */

class expBot {

    public $url = '';
    public $method = 'GET';
    
    public function __construct($params) {
        $this->url = isset($params['url']) ? $params['url'] : '';
        $this->method = isset($params['method']) ? $params['method'] : '';
    }
    
    public function fire() {
        $convo  = $this->method." ".$this->url."&ajax_action=1 HTTP/1.1\r\n";
        if ($this->method == 'POST') $convo .= "Content-Type: multipart/form-data\r\n";
        $convo .= "Host: " . HOSTNAME . "\r\n";
        $convo .= "User-Agent:  ExponentCMS/".EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR.".".EXPONENT_VERSION_REVISION."  Build/".EXPONENT_VERSION_ITERATION." PHP/".phpversion()."\r\n";
        $convo .= "Connection: Close\r\n\r\n";
        
        try {
            $theSpawn = fsockopen(HOSTNAME, 80);
            try {
            	fwrite ($theSpawn, $convo);
//                sleep(1);
                while (!feof($theSpawn)) {  //FIXME is this better than sleep(1)? seems to make it work, but delays?
                    echo fgets($theSpawn, 128);
                }
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

?>