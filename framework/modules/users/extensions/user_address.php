<?php

##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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
 * @subpackage Profile Extensions
 * @package Modules
 */

class user_address extends expRecord {

    public function name() { return 'Addresses'; }
	public function description() { return 'The extension allows users to enter their addresses.'; }

}

?>