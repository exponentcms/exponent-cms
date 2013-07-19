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
 * @subpackage Controllers
 * @package Modules
 */
/** @define "BASE" "../../../.." */

class motdController extends expController {
    public $useractions = array(
        'show'=>'Show Todays Message'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
//        'pagination',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags')

    static function displayname() { return gt("Message of the Day"); }
    static function description() { return gt("Display a message for a given day of the year."); }
    static function isSearchable() { return true; }
    
    function show() {
        global $db;
        expHistory::set('viewable', $this->params);
        $now = time();
        $month = date('n', $now);
        $day = date('j', $now);
        $message = $this->motd->find('first', 'month='.$month.' AND day='.$day);
        if (empty($message->id)) {
            $message = $this->motd->find('first', 'month=0 AND day='.$day);
            if (empty($message->id) && (!empty($this->config['userand']) && $this->config['userand']==true)) {
                $message = $this->motd->find('first', null, 'RAND()');
            }
        }
        
        assign_to_template(array(
            'message'=>$message
        ));
    }
    
    function showall() {
        expHistory::set('viewable', $this->params);
        $page = new expPaginator(array(
                    'model'=>'motd',
                    'where'=>$this->aggregateWhereClause(), 
                    'limit'=>(isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
                    'order'=>'month,day',
                    'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'src'=>$this->loc->src,
                    'columns'=>array(
                        gt('Date')=>'month',
                        gt('Message')=>'body'
                    ),
                ));
        
        assign_to_template(array(
            'page'=>$page
        ));
    }
    
    function update() {
        $timestamp = mktime(0, 0, 0, $this->params['month'], 1);
        $endday = expDateTime::endOfMonthDay($timestamp);
        if ($this->params['day'] > $endday) {
            expValidator::failAndReturnToForm(gt('There are only').' '.$endday.' '.gt('days in').' '.$this->motd->months[$this->params['month']], $this->params);
        }
        parent::update();
    }
    
//    function index() {
//        redirect_to(array('controller'=>'motd', 'action'=>'show'));
////        $this->show();
//    }

}

?>