<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * Standalone Template Class
 *
 * A standalone template is a global view template (tpl) file found in either
 * THEME_ABSOLUTE/views or /framework/core/views.
 *
 * @package Subsystems-Forms
 * @subpackage Template
 *
 * @param string $view The name of the standalone view.
 */
class standalonetemplate extends basetemplate {

	function __construct($view) {
		parent::__construct("globalviews", "", $view);
        // substitute a framework variation if available
        if (bs(true)) {
            $bstrpview = substr($this->viewfile, 0, -4) . '.bootstrap.tpl';
            if (file_exists($bstrpview) && !strpos($this->viewfile, THEME_ABSOLUTE)) {
                $this->viewfile = $bstrpview;
                $this->view = substr(basename($this->viewfile), 0, -4);
            }
        }
        if (bs3(true)) {
            $bstrpview = substr($this->viewfile, 0, -4) . '.bootstrap3.tpl';
            if (file_exists($bstrpview) && !strpos($this->viewfile, THEME_ABSOLUTE)) {
                $this->viewfile = $bstrpview;
                $this->view = substr(basename($this->viewfile), 0, -4);
            }
        } elseif (newui()) {
            $bstrpview = substr($this->viewfile, 0, -4) . '.newui.tpl';
            if (file_exists($bstrpview) && !strpos($this->viewfile, THEME_ABSOLUTE)) {
                $this->viewfile = $bstrpview;
                $this->view = substr(basename($this->viewfile), 0, -4);
            }
        }
	}

}

?>
