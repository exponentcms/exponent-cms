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

global $router;

//expHistory::flowSet(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);
expHistory::set('viewable', $router->params);

$template = new template("calendarmodule","_search",$loc,false);
$time = (isset($_GET['time']) ? $_GET['time'] : time());
if (isset($_GET['categoryid'])) $xsearch = " AND category_id='" . $_GET['categoryid'] ."'";
//$o = $db->selectObjects("calendar","title='".mysql_escape_string(trim($_GET['title']))."'" . $xsearch);
//$o = $db->selectObjects("calendar","title='".mysqli_real_escape_string(trim($_GET['title']))."'" . $xsearch);
$o = $db->selectObjects("calendar","title='".$db->escapeString(trim($_GET['title']))."'" . $xsearch);
for ($j = 0; $j < count($o); $j++) {
	$o[$j]->dates = $db->selectObjects("eventdate","event_id=".$o[$j]->id);
	foreach ($o[$j]->dates as $key=>$date){
		$o[$j]->eventdate = $date->date;
		$o[$j]->dates[$key]->eventstart = $date->date + $o[$j]->eventstart;
		$o[$j]->dates[$key]->eventend = $date->date + $o[$j]->eventend;
	}
}
//echo "<xmp>";
//print_r($o);//
//echo "</xmp>";
$template->assign("days",$o);
$template->assign("count",sizeof($o));
$template->assign("title",trim($_GET['title']));
$template->output();
?>
