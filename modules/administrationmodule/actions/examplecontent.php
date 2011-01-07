<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

if (exponent_permissions_check('extensions',exponent_core_makeLocation('administrationmodule'))) {
	exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);

	$modclass = $_GET['name'];
	
	$template = new template('administrationmodule','_examplecontent',$loc);
	
	$views = array();
	$loc = exponent_core_makeLocation($modclass,'@example');
	foreach (exponent_template_listModuleViews($modclass) as $view) {
		echo $view;
		$v = null;
		$v->view = $view;
		
		ob_start();
		call_user_func(array($modclass,'show'),$view,$loc,'Example Title');
		$v->content = ob_get_contents();
		ob_end_clean();
		
		$views[] = $v;
	}
	$template->assign('views',$views);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>