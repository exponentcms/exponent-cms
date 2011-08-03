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

// Part of the Extensions category

if (!defined('EXPONENT')) exit('');

if (isset($_REQUEST['id'])) {
    $bot = $db->selectObject('bots', 'id='.$_REQUEST['id']);
    $name = $bot->name;
} else {
    $name = $_REQUEST['name'];
}

include_once(BASE.'framework/modules-1/bots/bots/'.$bot->name.'.php');
$thisbot = new $bot->name;
$thisbot->start();

?>
