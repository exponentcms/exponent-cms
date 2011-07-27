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

/**
 * PosGreSQL Database Engine Info File
 *
 * Contains information about the PosGreSQL Database Engine implementation
 *
 * @author James Hunt
 * @copyright 2004-2011 OIC Group, Inc.
 * @version 0.95
 *
 * @package Subsystems
 * @subpackage Database
 */

return array(
	"name"=>"PostGreSQL Database Backend",
	"author"=>"James Hunt",
	"description"=>"PostGreSQL Database Backend.",
	'is_valid'=>0,//'is_valid'=>(function_exists('pg_connect') ? 1 : 0),
	"version"=>expUtil::getVersion(true)
);

?>