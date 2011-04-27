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

class testbot {
	function name() { return "testbot"; }
	function displayname() { return "Test eXp Bot"; }
	function author() { return "Adam Kessler"; }
	
	function start() {
		global $db;
		$bot = $db->selectObject('bots', 'name="'.$this->name().'"');
		while ($bot->state > 0) {
			eLog(time()." - Bots Rock!",'','',1);
			$bot = $db->selectObject('bots', 'name="'.$this->name().'"');
			sleep(1);
		}
	}

	function stop() {
		
	}
}
?>
