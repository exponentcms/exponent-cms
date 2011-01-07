<?php

return array(
	'title'=>'User Tracking',
	
	'enable_tracking'=>'Enable User Tracking',
	'tracking_desc'=>'Enabling user tracking will allow you to view detailed statistics about what users are doing on your site.  Performance penalties may be incurred for enabling this.  If you don\'t know what it is, or don\'t need it, don\'t enable it.',
    
    'cookie_expires'=>'How many days until the tracking cookie expires?',
    'cookie_expires_desc'=>'This will affect how long the users\' browsers will retain the tracking data which will help detect returning users.  It should NOT be set to a larger value than the archive retention time.',
    
    'tracking_archive'=>'How often, in hours, should the raw tracking data be processed?',
    'tracking_archive_desc'=>'The more often this updates, the smaller your database will be and the more up-to-date statistics you can view, however, running this too often wastes server resources.',
    
    'tracking_save'=>'How long should archived data be retained, in days?',
    'tracking_save_desc'=>'The longer that data is retained, the more accurate the statistics are that can be produced and also the more storage space required to store them.'
);

?>
