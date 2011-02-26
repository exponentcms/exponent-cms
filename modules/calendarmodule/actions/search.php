<?php

##################################################
#
# Copyright (c) 2004-2005 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# Exponent is distributed in the hope that it
# will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR
# PURPOSE.  See the GNU General Public License
# for more details.
#
# You should have received a copy of the GNU
# General Public License along with Exponent; if
# not, write to:
#
# Free Software Foundation, Inc.,
# 59 Temple Place,
# Suite 330,
# Boston, MA 02111-1307  USA
#
# $Id: view.php,v 1.1.1.1 2005/07/14 18:34:04 cvs Exp $
##################################################
if (!defined('EXPONENT')) exit('');

exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);

$template = new template("calendarmodule","_search",$loc,false);
$time = (isset($_GET['time']) ? $_GET['time'] : time());
if (!defined("SYS_DATETIME")) include_once(BASE."subsystems/datetime.php");
if (isset($_GET['categoryid'])) $xsearch = " AND category_id='" . $_GET['categoryid'] ."'";
$o = $db->selectObjects("calendar","title='".mysql_escape_string(trim($_GET['title']))."'" . $xsearch);
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
$template->register_permissions(
	array("manage_approval"),
	$loc);
$template->assign("days",$o);
$template->assign("count",sizeof($o));
$template->assign("title",trim($_GET['title']));
$template->output();
?>
