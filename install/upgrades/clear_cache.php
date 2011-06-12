<?php
##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class clear_cache extends upgradescript {
	protected $from_version = '1.99.0';
//	protected $to_version = '1.99.2';

	function name() { return "Clear the Cache"; }

	function upgrade() {
		$files = exponent_theme_remove_smarty_cache();
		return count($files['removed'])." files were removed from the cache.";

	}
}

?>
