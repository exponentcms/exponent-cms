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

if (!defined('EXPONENT')) exit('');

global $router;

expHistory::set('viewable', $router->params);

$view = (isset($_GET['view']) ? $_GET['view'] : "Default");
$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");

calendarmodule::show($view,$loc,$title);

?>