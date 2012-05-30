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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

$errors = null;
$continue = PATH_RELATIVE.'index.php?section='.SITE_DEFAULT_SECTION;

expSession::clearAllUsersSessionCache();

$template = new template('importer','_eql_results',$loc);
//GREP:UPLOADCHECK
if (!expFile::restoreDatabase($db,$_FILES['file']['tmp_name'],$errors)) {
	$template->assign('success',0);
	$template->assign('errors',$errors);
} else {
	$template->assign('success',1);
	$template->assign('continue',$continue);
}
$template->output();

?>