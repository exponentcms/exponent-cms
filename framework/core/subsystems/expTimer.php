<?php
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * This is the class expTimer
 * This class allows a user to to determine execution
 * time of code blocks.
 *
 * @author John Ciacia <Sidewinder@extreme-hq.com>
 * @version 1.0
 * @copyright Copyright (c) 2007, John Ciacia
 * @license [url=http://opensource.org/licenses/gpl-license.php]Open Source Initiative OSI - The GPL:Licensing | Open Source Initiative[/url] GNU Public License
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expTimer {

    var $starttime   = 0;
    var $endtime     = 0;
    var $elapsed     = 0;
    var $timername   = "Exponent Internal Timer";
    
    public function __construct($start = true) {
        if($start) $this->start();
    }
    
    public function start() {
        $this->starttime = $this->_time();
    }
    
    public function stop() {
        $this->endtime = $this->_time();
        $this->_compute();    
    }
    
    public function mark() {
        $this->endtime = $this->_time();
        $this->_compute();    
        $ret = $this->elapsed();
        $this->clear();
        $this->start();
        return $ret;
    }
    
    public function clear() {
        $this->starttime   = 0;
        $this->endtime     = 0;
        $this->elapsed     = 0;
        $this->timername   = "Not Named";
    }
    
    public function elapsed() {
        return $this->elapsed;
    }
    
    public function setTimerName($name) {
        $this->timername = $name;
    }
    
    public function getTimerName() {
        return $this->timername;
    }
    
    private function _time() {
        $mtime = microtime(); 
        $mtime = explode(' ', $mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        return $mtime; 
    }
    
    private function _compute() {
        $this->elapsed = (($this->endtime) - ($this->starttime));
    }


}

?>