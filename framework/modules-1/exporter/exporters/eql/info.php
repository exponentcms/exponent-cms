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

return array(
	'name'=>gt('EQL Database Exporter'),
	'description'=>gt('Export the data in your site database to an EQL (Exponent Query Language) format file, which can be used to restore the database through the EQL File Importer.'),
	'author'=>'James Hunt'
);

?>