<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

// Part of the HTMLArea category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('htmlarea',exponent_core_makeLocation('administrationmodule'))) {
	exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);

	$template = new template('administrationmodule','_htmlareaconfigs',$loc);
	$configs = $db->selectObjects('toolbar_' . SITE_WYSIWYG_EDITOR);
	$template->assign('configs',$configs);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>