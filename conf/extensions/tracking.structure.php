<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

if (!defined('EXPONENT')) exit('');

$ctl = new checkboxcontrol(false,true);
$ctl->disabled = 0;

return array(
	gt('User Tracking'),
	array(
		'ENABLE_TRACKING'=>array(
			'title'=>gt('Enable User Tracking'),
			'description'=>gt('Enabling user tracking will allow you to view detailed statistics about what users are doing on your site.  Performance penalties may be incurred for enabling this.  If you don\'t know what it is, or don\'t need it, don\'t enable it.'),
			'control'=>$ctl
		),
        'TRACKING_COOKIE_EXPIRES'=>array(
            'title'=>gt('How many days until the tracking cookie expires?'),
            'description'=>gt('This will affect how long the users\' browsers will retain the tracking data which will help detect returning users.  It should NOT be set to a larger value than the archive retention time.'),
            'control'=>new textcontrol()
        ),
        'TRACKING_ARCHIVE_DELAY'=>array(
            'title'=>gt('How often, in hours, should the raw tracking data be processed?'),
            'description'=>gt('The more often this updates, the smaller your database will be and the more up-to-date statistics you can view, however, running this too often wastes server resources.'),
            'control'=>new textcontrol()
        ),
        'TRACKING_ARCHIVE_TIME'=>array(
            'title'=>gt('How long should archived data be retained, in days?'),
            'description'=>gt('The longer that data is retained, the more accurate the statistics are that can be produced and also the more storage space required to store them.'),
            'control'=>new textcontrol()
        )
	)
);

?>
