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

class countdownController extends expController {
	public $useractions = array(
        'show'=>'Show Clock'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

    static function displayname() { return gt("Countdown"); }
    static function description() { return gt("Displays a timer counting down to a specified date/time."); }
    static function author() { return "Ported to Exponent by Phillip Ball. JS written by http://www.hashemian.com/tools/javascript-countdown.htm"; }

    /**
   	 * default view for individual item
   	 */
   	function show() {
       assign_to_template(array(
           'config'=>$this->config
       ));
   }

}

?>