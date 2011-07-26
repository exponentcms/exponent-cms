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
/** @define "BASE" "../.." */

if (!defined('EXPONENT')) exit('');

// Normalizer Script
// This script normalizes the database and sets some things back in order.

// Normalize Section Rankings
function exponent_backup_normalize_sections($db,$parent = 0) {
	$sections = $db->selectObjects('section','parent='.$parent);
	if (!defined('SYS_SORTING')) require_once(BASE.'subsystems/sorting.php');
	usort($sections,'exponent_sorting_byRankAscending');
	
	for ($i = 0; $i < count($sections); $i++) {
		$s = $sections[$i];
		$s->rank = $i;
		$db->updateObject($s,'section');
		exponent_backup_normalize_sections($db,$s->id); // Normalize children
	}
}

exponent_backup_normalize_sections($db);

?>