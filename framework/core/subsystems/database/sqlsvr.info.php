<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * SQL Server Database Engine Info File
 *
 * Contains information about the SQL Server Database Engine implementation
 *
 * @package Subsystems
 * @subpackage Database
 */
return array(
	'name'=>'MS SQL Server Database Backend',
	'author'=>'Dave Leffler',
	'description'=>'MS SQL Server Database Backend available in PHP5+.',
	'is_valid'=>(function_exists('sqlsrv_connect') ? 1 : 0),
	'version'=>expVersion::getVersion(true)
);

?>