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

/**
 * @subpackage Models
 * @package Core
 */
class storeCategoryFeeds extends storeCategory {

	public $has_and_belongs_to_many = array(
        'google_product_types',
        'bing_product_types',
        'nextag_product_types',
        'shopzilla_product_types',
        'shopping_product_types'
    );
	
	public function __construct($params=null, $get_assoc=true, $get_attached=true) {
		parent::__construct($params, $get_assoc, $get_attached);
	}

}

?>