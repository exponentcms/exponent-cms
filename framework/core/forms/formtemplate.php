<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

/**
 * Form Template Wrapper
 *
 * This class is used for site wide forms.  
 *
 * @package Subsystems-Forms
 * @subpackage Template
 */
class formtemplate extends basetemplate {  //FIXME only used by calendarmodule for feedback forms

	function __construct($form, $view) {
		parent::__construct("forms", $form, $view);
		$this->tpl->assign("__name", $form);
	}

}

?>
