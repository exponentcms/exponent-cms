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
/** @define "BASE" "." */

// Initialize the Exponent Framework
require_once('exponent.php');

//Fire off the login form via an exponent action.
if (!defined('SYS_SESSIONS')) require_once(BASE.'subsystems/sessions.php');
exponent_sessions_set('redirecturl', exponent_flow_get());
redirect_to(array("module"=>"loginmodule","action"=>"loginredirect"));

?>
