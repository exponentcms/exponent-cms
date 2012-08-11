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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');

$locsql = "(location_data='".serialize($loc)."'";
// look for possible aggregate
$config = $db->selectObject("calendarmodule_config","location_data='".serialize($loc)."'");
if (!empty($config->aggregate)) {
	$locations = unserialize($config->aggregate);
	foreach ($locations as $source) {
		$tmploc = new stdClass();
		$tmploc->mod = 'calendarmodule';
		$tmploc->src = $source;
		$tmploc->int = '';
		$locsql .= " OR location_data='".serialize($tmploc)."'";
	}
}
$locsql .= ')';

$dates = $db->selectObjects("eventdate",$locsql." AND date < ".strtotime('-1 months',time()));
if ($dates) {
    if (expPermissions::check('delete',$loc)) {
        $db->delete('eventdate',$locsql." AND date < ".strtotime('-1 months',time()));
        foreach ($dates as $date) {
            $remaining = $db->countObjects("eventdate",'event_id='.$date->event_id);
            if (!$remaining) {
                $db->delete('calendar','id='.$date->event_id);
                //Delete search entries
                $db->delete('search',"ref_module='calendarmodule' AND ref_type='calendar' AND original_id=".$date->event_id);
            }
        }
        expHistory::back();
    } else {
   		echo SITE_403_HTML;
   	}
} else {
	echo SITE_404_HTML;
}

?>
