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

if (!defined('EXPONENT')) exit('');

// Will translate together with the bb module
//$i18n = exponent_lang_loadFile('subsystems/search.info.php');

return array(
	//'name'=>$i18n['subsystem_name'],	
	'name'=>'Paging Objects',
	'author'=>'Jacob Mesu',
	//'description'=>$i18n['subsystem_description'],
	'description'=>'Easy page handling for a module with paging enabled.',
	'version'=>exponent_core_version(true)
);

?>
