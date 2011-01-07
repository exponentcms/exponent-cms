<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class motdController extends expController {
    //public $basemodel_name = '';
    public $useractions = array('show'=>'Show Todays Message');

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Message of the Day"; }
    function description() { return "Display a message for a given day of the year."; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return true; }
    
    function show() {
        global $db;
        expHistory::set('viewable', $this->params);
        $now = time();
        $month = date('n', $now);
        $day = date('j', $now);
        $message = $this->motd->find('first', 'month='.$month.' AND day='.$day); 
        
        if (empty($message->id) && $this->config['userand']==true) {
            $message = $this->motd->find('first', null, 'RAND()');  
        }
        
        assign_to_template(array('message'=>$message));
    }
    
    function showall() {
        expHistory::set('viewable', $this->params);
        $page = new expPaginator(array(
                    'model'=>'motd',
                    'where'=>$this->aggregateWhereClause(), 
                    'limit'=>empty($this->config['limit']) ? 10 : $this->config['limit'],
                    'order'=>'month,day',
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array('Date'=>'month', 'Message'=>'body'),
                    ));
        
        assign_to_template(array('page'=>$page));
    }
    
    function update() {
        if (!defined("SYS_DATETIME")) include_once(BASE."subsystems/datetime.php");
        $timestamp = mktime(0, 0, 0, $this->params['month'], 1);
        $endday = exponent_datetime_endOfMonthDay($timestamp);
        if ($this->params['day'] > $endday) {
            expValidator::failAndReturnToForm('There are only '.$endday.' days in '.$this->motd->months[$this->params['month']], $this->params);
        }
        parent::update();
    }
    
    function index() {
        redirect_to(array('controller'=>'motd', 'action'=>'show'));
    }

}


?>
