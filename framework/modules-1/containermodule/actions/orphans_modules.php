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

// PERM CHECK
   	expSession::clearAllUsersSessionCache('containermodule');

	$orphan_mods = array();
//	$orph = $db->selectObjects('locationref','refcount = 0 AND module='.$_GET['module']);
	$orph = $db->selectObjects('sectionref','refcount = 0 AND module='.$_GET['module']);
	foreach ($orph as $orphan) {
		if (!isset($orphan_mods[$orphan->module]) && class_exists($orphan->module)) {
			$modclass = $orphan->module;
			$mod = new $modclass();
			$orphan_mods[$modclass] = $mod->name();
		}
	}
	uasort($orphan_mods,'strnatcmp');
	
	$template = new template('containermodule','_orphans_modules');
	$template->assign('orphan_mods',$orphan_mods);
	$template->output();
// END PERM CHECK

?>
