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
 * Standalone Template Class
 *
 * A standalone template is a template (tpl) file found in either
 * THEME_ABSOLUTE/views or BASE/views, which uses
 * the corresponding views_c directory for compilation.
 * 
 * @param string $view The name of the standalone view.
 */
class standalonetemplate extends BaseTemplate {
	function __construct($view) {
		parent::__construct("globalviews", "", $view);
	}
}

?>
