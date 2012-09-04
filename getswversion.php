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

ini_set('error_reporting', E_ALL);
include('./exponent_version.php');
//$swversion->major = 2;
//$swversion->minor = 0;
//$swversion->revision = 4;
//$swversion->type = '';
//$swversion->iteration = '';
//$swversion->builddate = '1324662757';
$swversion = new stdClass();
$swversion->major = EXPONENT_VERSION_MAJOR;
$swversion->minor = EXPONENT_VERSION_MINOR;
$swversion->revision = EXPONENT_VERSION_REVISION;
$swversion->type = EXPONENT_VERSION_TYPE;
$swversion->iteration = EXPONENT_VERSION_ITERATION;
$swversion->builddate = EXPONENT_VERSION_BUILDDATE;

$ajaxObj['data'] = $swversion;
$ajaxObj['replyCode'] = 201;
$ajaxObj['replyText'] = 'Ok';
echo json_encode($ajaxObj);

?>