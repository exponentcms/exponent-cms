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

class remove_exp1_faqmodule extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '1.99.2'; 

	static function name() { return "Remove the old FAQ Module"; }

	function upgrade() {
//	    global $db;
	    
		// check if the files are there and remove them
		$files = array(
		    BASE."datatypes/definitions/faqmodule_config.php",
		    BASE."datatypes/definitions/faq.php",
		    BASE."datatypes/faqmodule_config.php",
		    BASE."datatypes/faq.php",
		    BASE."modules/faqmodule/"
		);

        // delete the files.
        $removed = 0;
        $errors = 0;
		foreach ($files as $file) {
		    if (expUtil::isReallyWritable($file)) {
		        unlink ($file);
		        $removed += 1;
		    } else {
		        $errors += 1;
		    }
		} 
		
		return $removed." files were deleted.<br>".$errors." files could not be removed.";
		
	}
}

?>
