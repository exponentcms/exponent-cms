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

if (!defined('EXPONENT')) exit('');

expSession::clearAllUsersSessionCache('containermodule');

$orphans = array();
foreach ($db->selectObjects("sectionref","refcount=0", "module") as $orphan) {
	$obj = null;
	$loc = expCore::makeLocation($orphan->module,$orphan->source,$orphan->internal);
//	eDebug($orphan);
}

//$template = new template("containermodule","");
//$template->output();

?>
