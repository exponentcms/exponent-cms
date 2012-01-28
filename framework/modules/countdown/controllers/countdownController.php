<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

class countdownController extends expController {
	//protected $basemodel_name = '';
	public $useractions = array(
        'show'=>'Show Clock'
    );
    public $remove_configs = array(
        'ealerts',
        'tags',
        'files',
        'rss',
        'comments',
        'aggregation'
    ); // all options: ('aggregation', 'cats','comments','ealerts','files','pagination', 'rss','tags')

	function displayname() { return "Countdown"; }
	function description() { return "This module allows you to display a timer counting down to a specified date/time."; }
	function author() { return "Ported to Exponent by Phillip Ball. JS written by http://www.hashemian.com/tools/javascript-countdown.htm"; }
	
}

?>
