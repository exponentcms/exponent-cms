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

if (!defined('EXPONENT')) exit('');

if ($user) {
	$sections = navigationmodule::levelTemplate(0,0);
	$standalones = $db->selectObjects('section','parent = -1');
	$template = new template('navigationmodule','_linker');
	$template->assign('sections',$sections);
	$template->assign('standalones',$standalones);
	$template->assign('haveStandalones',count($standalones));
	$template->output();
}

?>