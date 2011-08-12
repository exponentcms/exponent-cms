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

class convert_db_trim extends upgradescript {
	protected $from_version = '1.99.0';
	protected $to_version = '1.99.0'; 

	function name() { return "Upgrade Database Table Removal Code"; }

	function upgrade() {
		// check if the files are there and remove them
		$file1 = BASE."/framework/modules-1/administrationmodule/actions/trimdatabase.php";
		$file2 = BASE."/framework/modules-1/administrationmodule/actions/trimdatabase_final.php";
		if (expUtil::isReallyWritable($file1) && expUtil::isReallyWritable($file2)) {
			unlink ($file1);
			unlink ($file2);
			return "Complete";
		} else {
		    return "Could not delete files.";    
		}
		
	}
}

?>
