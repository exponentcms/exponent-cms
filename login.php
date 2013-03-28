<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
expSession::set('redirecturl', expHistory::getLastNotEditable());
redirect_to(array("controller"=>"login","action"=>"showlogin"));

?>
