<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

// Part of Extensions category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('development',exponent_core_makeLocation('administrationmodule'))) {
	$files = expTheme::removeSmartyCache();
	$template = new template('administrationmodule','_remove_css',$loc);
	$template->assign('file_type', exponent_lang_getText('Smarty Cache Files'));
	$template->assign('files',$files);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
