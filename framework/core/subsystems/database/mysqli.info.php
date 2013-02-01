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

/**
 * MySQL Database Engine Info File
 *
 * Contains information about the MySQL Database Engine implementation
 *
 * @package Subsystems
 * @subpackage Database
 */
return array(
	'name'=>'MySQLi Database Backend',
	'author'=>'Ron Miller',
	'description'=>'MySQLi Database Backend available in PHP5+. Offers greatly improved database performance.',
	'is_valid'=>(function_exists('mysqli_connect') ? 1 : 0),
	'version'=>expVersion::getVersion(true)
);

?>