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

$errors = array();
//FIXME need to make a better link and use it in template

expSession::clearAllUsersSessionCache();

$template = new template('importer','_eql_results',$loc);
//GREP:UPLOADCHECK
//FIXME need to determine db version after restore and report it in relation to software version
expFile::restoreDatabase($db,$_FILES['file']['tmp_name'],$errors);
$template->assign('success',!count($errors));
$template->assign('errors',$errors);
$template->output();

?>