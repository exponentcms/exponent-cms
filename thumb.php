<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
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
/** @define "BASE" "." */
if (defined('EXPONENT')) return;
// bootstrap some exponenty goodness
include_once('exponent_bootstrap.php');

if (!defined('SYS_SESSION_KEY')) define('SYS_SESSION_KEY',PATH_RELATIVE);
if (isset($_GET['id'])) {
    // Since bootstrap doesn't setup the session we need to define this
    // otherwise the expFile can't find it's table desc from cache.
    // Initialize the Database Subsystem
    $db = expDatabase::connect(DB_USER,DB_PASS,DB_HOST.':'.DB_PORT,DB_NAME,'',false,BASE . 'tmp/thumb_sql.log');

    $file_obj = new expFile(intval($_GET['id']));
    //$_GET['src'] = "/" . $file_obj->directory.$file_obj->filename;
//    $_GET['src'] = $file_obj->path;
    $_GET['src'] = $file_obj->path_relative;  // fix for using v1.7.12+

    unset(
        $_GET['id'],
        $_GET['square']
    );
}
if (isset($_GET['src']) && substr($_GET['src'],0,1) != '/' && substr($_GET['src'],0,4) != 'http') {
    $_GET['src'] = PATH_RELATIVE . $_GET['src'];  //kludge since phpThumb v1.17.2+ needs a full path to work correctly
}
require_once(BASE."external/phpThumb/phpThumb.php");

?>