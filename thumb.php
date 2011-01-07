<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

if (defined('EXPONENT')) return;

// bootstrap some exponenty goodness
include_once('exponent_bootstrap.php');

// Since bootstrap doesn't setup the session we need to define this
// otherwise the expFile can't find it's table desc from cache.
if (!defined('SYS_SESSION_KEY')) define('SYS_SESSION_KEY',PATH_RELATIVE);


//if (empty($_GET['lgcy'])) {
    // we're dealing with phpThumb, not the oldschool thumnailer
    
    //
    if (isset($_GET['id'])) {
    	include_once('subsystems/autoloader.php');
    	include_once('subsystems/config/load.php');
    	// Initialize the Database Subsystem
    	include_once(BASE.'subsystems/database.php');
    	$db = exponent_database_connect(DB_USER,DB_PASS,DB_HOST.':'.DB_PORT,DB_NAME);

    	$file_obj = new expFile($_GET['id']); 
        $_GET['src'] = $file_obj->directory.$file_obj->filename;

        unset($_GET['id']);
        unset($_GET['square']);
    }
    
    
    
    require(BASE."external/phpThumb/phpThumb.php");
//} else{
    // we're dealing with the old school thumnailer
//    require(BASE."expLegacyThumb.php");
//};

?>
