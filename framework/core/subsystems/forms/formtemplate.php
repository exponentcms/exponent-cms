<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006-2007 Maxim Mueller
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
/** @define "BASE" "../../.." */

/*
 * Form Template Wrapper
 *
 * This class is used for site wide forms.  
 *
 * @package Subsystems
 * @subpackage Template
 */
class formtemplate extends BaseTemplate {

	function __construct($form, $view) {
		parent::__construct("forms", $form, $view);
		$this->tpl->assign("__name", $form);
	}
}

?>
