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

/**
 * @subpackage Models
 * @package Modules
 */

class search_queries extends expRecord {
	public $table = 'search_queries';
	
	public function __construct($params=array(), $get_assoc=true, $get_attached=true) {
		global $db;
		parent::__construct($params, $get_assoc, $get_attached);

	}
	
}

?>