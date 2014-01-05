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
 * @subpackage Controllers
 * @package Modules
 */

class countdownController extends expController {
	public $useractions = array(
        'show'=>'Show Clock'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Countdown"); }
    static function description() { return gt("Displays a timer counting down to a specified date/time."); }
    static function author() { return "Ported to Exponent by Phillip Ball. Original JS at http://tutorialzine.com/2011/12/countdown-jquery/"; }

    /**
   	 * default view for individual item
   	 */
   	function show() {
        if (!empty($this->config['count'])) {
            // parse out date into calendarcontrol fields 'date-count' 'time-h-count' 'time-m-count' 'ampm-count'
            $this->config['date-count'] = date('m/d/Y', $this->config['count']);
            $this->config['time-h-count'] = date('h', $this->config['count']);
            $this->config['time-m-count'] = date('i', $this->config['count']);
            $this->config['ampm-count'] = date('a', $this->config['count']);
        }
        assign_to_template(array(
            'config'=>$this->config
        ));
    }

    function saveconfig() {
        // get parsed data and unset calendarcontrol fields
        $this->params['count'] = calendarcontrol::parseData('count',$this->params);
        unset($this->params['date-count']);
        unset($this->params['time-h-count']);
        unset($this->params['time-m-count']);
        unset($this->params['ampm-count']);
        parent::saveconfig();
    }
}

?>