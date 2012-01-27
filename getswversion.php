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

ini_set('error_reporting', E_ALL);
$swversion->major = 2;
$swversion->minor = 0;
$swversion->revision = 4;
$swversion->type = '';
$swversion->iteration = '';
$swversion->builddate = '1324662757';

$ajaxObj['data'] = $swversion;
$ajaxObj['replyCode'] = 201;
$ajaxObj['replyText'] = 'Ok';
echo json_encode($ajaxObj);

?>
