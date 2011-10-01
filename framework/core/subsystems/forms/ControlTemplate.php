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

/* exdoc
 *
 * Control Template wrapper
 *
 */
class ControlTemplate extends BaseTemplate {
	
	var $viewitem = "";

	function __construct($control, $view = "Default", $loc = null) {
		parent::__construct("controls", $control, $view);
		$this->tpl->assign("__name", $control);
	}

	/*
	 * Render the template and return the result to the caller.
	 * temporary override for testing functionality
	 */
//	function render() {
//		//pump the viewitem into the view layer
//		
//		$this->tpl->assign("vi", $this->viewitem);
//		$this->tpl->assign("dm", $this->viewitem->datamodel);
//		
//		//call childobjects show() method recursively, based on render depth setting
//		//assign output
//		
//		return $this->tpl->fetch($this->view.'.tpl');
//	}
}

?>
