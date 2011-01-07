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

function smarty_function_list_object($params,&$smarty) {
	if (isset($params['object'])) {
		echo "<ul>";
		foreach ($params['object'] as $key=>$val) {
			echo "<li><strong>$key: </strong>$val</li>";
		}
		echo "</ul>";
	} else {
		echo '<span class="error">No Object Found</span><br />';
	}
}

?>

